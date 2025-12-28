<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\User;
use App\Models\UserTarget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AdminReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $users = User::where('role', 'user')
            ->orderBy('nama_lengkap')
            ->get();

        $currentMonth = now()->format('Y-m');

        return view('admin.user-reports.index', [
            'users' => $users,
            'currentMonth' => $currentMonth,
        ]);
    }

    public function show(Request $request, User $user)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $report = $this->buildReport($user, $month);

        return view('admin.user-reports.show', [
            'user' => $user,
            'month' => $month,
            'report' => $report,
        ]);
    }

    public function export(Request $request, User $user)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $report = $this->buildReport($user, $month);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Meta info
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', $user->nama_lengkap ?? $user->username);
        $sheet->setCellValue('A2', 'Program');
        $sheet->setCellValue('B2', $user->level);
        $sheet->setCellValue('A3', 'Periode');
        $sheet->setCellValue('B3', $month);

        // Header
        $headers = ['No', 'Program', 'Target Mingguan', 'Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Total', 'Presentase'];
        $sheet->fromArray($headers, null, 'A5');

        // Data rows
        $rowNum = 6;
        foreach ($report as $idx => $row) {
            $sheet->setCellValue("A{$rowNum}", $idx + 1);
            $sheet->setCellValue("B{$rowNum}", $row['program']->nama_program);
            $sheet->setCellValue("C{$rowNum}", $row['weeklyTarget']);
            $sheet->setCellValue("D{$rowNum}", $row['weeks'][1]);
            $sheet->setCellValue("E{$rowNum}", $row['weeks'][2]);
            $sheet->setCellValue("F{$rowNum}", $row['weeks'][3]);
            $sheet->setCellValue("G{$rowNum}", $row['weeks'][4]);
            $sheet->setCellValue("H{$rowNum}", $row['totalScore']);
            $sheet->setCellValue("I{$rowNum}", $row['percentage'] / 100);
            $sheet->getStyle("I{$rowNum}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
            $rowNum++;
        }

        // Auto width
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'laporan-' . ($user->nama_lengkap ?? $user->username) . '-' . $month . '.xlsx';
        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function calculateTargetScore(UserTarget $target): ?float
    {
        $program = $target->program;

        if (!$program) {
            return null;
        }

        if ($target->score !== null) {
            return (float) $target->score;
        }

        if ($program->type === 'boolean') {
            return $target->value ? 100 : 0;
        }

        $programTarget = (float) ($program->target ?? 0);
        if ($programTarget <= 0) {
            return null;
        }

        return max(0, min(100, round(($target->value / $programTarget) * 100, 2)));
    }

    private function getWeeklyTarget(Program $program)
    {
        if ($program->type === 'boolean') {
            return 7;
        }

        if (is_numeric($program->target)) {
            return (float) $program->target * 7;
        }

        return '-';
    }

    private function getRawValue(Program $program, UserTarget $target): float
    {
        if ($program->type === 'boolean') {
            return $target->value ? 1 : 0;
        }

        return (float) ($target->value ?? 0);
    }

    private function buildReport(User $user, string $month)
    {
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $programs = Program::whereIn('level', [$user->level, 'both'])->get();

        return $programs->map(function ($program) use ($user, $start, $end) {
            $targets = UserTarget::where('user_id', $user->id)
                ->where('program_id', $program->id)
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->get();

            $weeks = [
                1 => ['sum' => 0],
                2 => ['sum' => 0],
                3 => ['sum' => 0],
                4 => ['sum' => 0],
            ];

            foreach ($targets as $target) {
                $value = $this->getRawValue($program, $target);
                $day = Carbon::parse($target->date)->day;
                $week = $day <= 7 ? 1 : ($day <= 14 ? 2 : ($day <= 21 ? 3 : 4));
                $weeks[$week]['sum'] += $value;
            }

            $weekValues = [];
            foreach ([1, 2, 3, 4] as $i) {
                $weekValues[$i] = round($weeks[$i]['sum'], 1);
            }

            $weeklyTarget = $this->getWeeklyTarget($program);
            $totalScore = round(array_sum($weekValues), 1);
            $percentage = is_numeric($weeklyTarget) && $weeklyTarget > 0
                ? round(($totalScore / ($weeklyTarget * 4)) * 100, 1)
                : 0;

            return [
                'program' => $program,
                'weeks' => $weekValues,
                'weeklyTarget' => $weeklyTarget,
                'totalScore' => $totalScore,
                'percentage' => $percentage,
            ];
        });
    }
}
