<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\PaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminFeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('role:admin'); // Asumsi middleware role sudah ada
    }

    public function index()
    {
        $users = User::where('level', '!=', 'admin')->with(['kos', 'payments'])->get();
        $pembayaran_masuk = PaymentDetail::where('status', 'pending')->get()->count();
        // dd($pembayaran_masuk);
        return view('admin.fees.index', compact(['users', 'pembayaran_masuk']));
    }

    public function show($userId)
    {
        // dd($userId);
        $user = User::with(['kos', 'payments.payment_details'])->findOrFail($userId);
        // $payments = $user->payments()->with('payment_details')->get();
        return view('admin.fees.show', compact('user'));
    }

    public function updateFee(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'annual_fee' => 'required|numeric|min:0',
        ]);

        $userId = $request->input('user_id');
        $annualFee = $request->input('annual_fee');

        $user = User::findOrFail($userId);
        $user->update(['annual_fee' => $annualFee]);

        return redirect()->back()->with('success', 'Biaya tahunan untuk ' . $user->nama_lengkap . ' berhasil diperbarui.');
    }

    public function setDueDate(Request $request)
    {
        $userId = $request->input('user_id');
        $installmentNumber = $request->input('installment_number');
        $dueDate = $request->input('due_date');

        $user = User::findOrFail($userId);
        $payment = $user->payments()->where('installment_number', $installmentNumber)->first();
        if ($payment) {
            $payment->update(['due_date' => $dueDate]);
        } else {
            $payment = new Payment([
                'user_id' => $userId,
                'installment_number' => $installmentNumber,
                'due_date' => $dueDate,
                'status' => 'pending',
                'payment_date' => null,
                'amount' => 0,
                'proof_path' => null,
            ]);
            $payment->save();
        }

        return redirect()->back()->with('success', 'Batas waktu angsuran ' . $installmentNumber . ' untuk ' . $user->nama_lengkap . ' berhasil diset.');
    }

    public function summary()
    {
        $users = User::where('level', '!=', 'admin')->with('payments')->get();
        return view('admin.fees.summary', compact('users'));
    }

    public function konfirmasi_pembayaran()
    {
        // $pembayaran_masuk = Payment::with('payment_details')->where('status', 'pending')->get();

        // $pembayaran_masuk = Payment::whereHas('payment_details', function ($query) {
        //     $query->where('status', 'pending');
        // })->with(['payment_details', 'user'])->get();

        $pembayaran_masuk = PaymentDetail::where('status', 'pending')->with('payment.user.kos')->get();

        // dd($pembayaran_masuk);
        return view('admin.fees.konfirmasi_pembayaran', compact('pembayaran_masuk'));
    }

    public function halaman_konfirmasi($paymentId)
    {
        // dd($paymentId);
        $paymentDetail = PaymentDetail::with('payment.user')->findOrFail($paymentId);
        // dd($payment);
        return view('admin.fees.halaman_konfirmasi', compact('paymentDetail'));
    }

    public function validasiPembayaran($id)
    {
        try {
            DB::beginTransaction(); // ← Tambahkan ini di awal

            $detail = PaymentDetail::with('payment')->findOrFail($id);

            if ($detail->status !== 'pending') {
                return back()->with('error', 'Pembayaran sudah divalidasi.');
            }

            $detail->status = 'diterima';
            $detail->save();

            $payment = $detail->payment;
            $payment->total_terbayar = ($payment->total_terbayar ?? 0) + $detail->jumlah;
            $hargaKos = $payment->harga_kos ?? 0;
            $totalTerbayar = $payment->total_terbayar;
            $payment->kurang = max(0, $hargaKos - $totalTerbayar);
            if ($payment->kurang <= 0) {
                $payment->status = 'lunas';
            }

            $payment->save();

            DB::commit();

            return redirect()->route('admin.konfirmasi_pembayaran')->with('success', 'Pembayaran berhasil divalidasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            // \Log::error('Gagal validasi: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memvalidasi pembayaran: ' . $e->getMessage());
        }
    }

    public function tolakPembayaran($id)
    {
        try {
            $detail = PaymentDetail::findOrFail($id);

            if ($detail->status !== 'pending') {
                return back()->with('error', 'Pembayaran ini sudah diproses sebelumnya.');
            }

            $detail->status = 'ditolak';
            $detail->save();

            return back()->with('success', 'Pembayaran berhasil ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
    }
}
