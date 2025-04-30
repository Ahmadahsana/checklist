<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\PaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Muat user saat ini dengan relasi payments
        $user = Auth::user()->load('payments');
        // $user = User::with('payments')->find(Auth::user()->id);

        // dd(Auth::user()->id);
        // dd($user); // Debugging untuk melihat data pembayaran
        $payments = $user->payments;

        return view('users.payment.index', compact('user', 'payments'));
    }

    // public function submitPayment(Request $request)
    // {
    //     $request->validate([
    //         'nominal' => 'required|numeric|min:0',
    //         'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
    //     ]);

    //     $user = Auth::user();

    //     $totalPaid = $user->payments->total_terbayar; //jumlah yang sudah dibayar
    //     $remaining = $user->payments->kurang; //sisa biaya yang harus dibayar

    //     if ($request->nominal > $remaining) {
    //         return redirect()->back()->with('error', 'Nominal pembayaran melebihi sisa biaya.');
    //     }

    //     if ($request->hasFile('proof')) {
    //         $proofPath = $request->file('proof')->store('proofs', 'public');
    //     }

    //     $nextInstallment = $user->payments->angsuran_ke + 1 ?? 1; //pembayaran ke ....

    //     $payment_detail = new PaymentDetail([
    //         'payment_id' => $user->id,
    //         'jumlah' => $request->jumlah,
    //         'bukti_tf' => $proofPath ?? null,
    //         'tanggal' => now(),
    //         'status' => 'pending',
    //         'angsuran_ke' => $nextInstallment,
    //     ]);
    //     $payment_detail->save();


    //     return redirect()->back()->with('success', 'Pembayaran angsuran berhasil diajukan. Menunggu persetujuan admin.');
    // }

    public function submitPayment(Request $request)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1',
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();

        // Ambil satu payment aktif dari user (bisa ditambah kondisi spesifik kalau banyak payment)
        $payment = $user->payments()->where('status', '!=', 'lunas')->first();

        if (!$payment) {
            return redirect()->back()->with('error', 'Tidak ditemukan tagihan aktif.');
        }

        $jumlahAngsuranSaatIni = $payment->payment_details()->count();
        $nextInstallment = $jumlahAngsuranSaatIni + 1;

        // Cek batas maksimal angsuran
        if ($nextInstallment > $payment->max_bayar) {
            return redirect()->back()->with('error', 'Jumlah angsuran melebihi batas maksimum yang diperbolehkan.');
        }

        // Jika ini angsuran terakhir, nominal harus sama dengan sisa kurang
        if ($nextInstallment === $payment->max_bayar && $request->nominal != $payment->kurang) {
            return redirect()->back()->with('error', 'Nominal angsuran terakhir harus sesuai dengan sisa biaya.');
        }

        // Simpan bukti transfer
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('proofs', 'public');
        }

        // Simpan ke payment_details
        $paymentDetail = new PaymentDetail([
            'payment_id' => $payment->id,
            'jumlah' => $request->nominal,
            'bukti_tf' => $proofPath ?? null,
            'tanggal' => now(),
            'status' => 'pending',
            'angsuran_ke' => $nextInstallment,
        ]);
        $paymentDetail->save();

        // // Update payment summary
        // $payment->total_terbayar += $request->nominal;
        // $payment->kurang = max(0, $payment->harga_kos - $payment->total_terbayar);

        // // Jika lunas, update status
        // if ($payment->kurang <= 0) {
        //     $payment->status = 'lunas';
        // }

        // $payment->save();

        return redirect()->back()->with('success', 'Pembayaran angsuran berhasil diajukan. Menunggu persetujuan admin.');
    }
}
