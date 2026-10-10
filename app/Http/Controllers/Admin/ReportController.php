<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\StaffTask;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->get('type', 'operations_summary');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::now()->toDateString());

        $clusters = User::whereNotNull('cluster')->where('cluster', '!=', '')->distinct()->pluck('cluster')->sort()->values();
        $personnel = User::where('status', 'active')->orderBy('name')->get();

        $data = $this->getReportData($reportType, $request, $dateFrom, $dateTo);

        return view('admin.reports.index', compact(
            'reportType',
            'dateFrom',
            'dateTo',
            'clusters',
            'personnel',
            'data'
        ));
    }

    public function print(Request $request)
    {
        $reportType = $request->get('type', 'operations_summary');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::now()->toDateString());

        $data = $this->getReportData($reportType, $request, $dateFrom, $dateTo);

        AuditLog::record('Reports', 'Print Report', "Printed operations report: {$reportType}");

        return view('admin.reports.print', compact(
            'reportType',
            'dateFrom',
            'dateTo',
            'data'
        ));
    }

    public function exportCsv(Request $request)
    {
        $reportType = $request->get('type', 'operations_summary');
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::now()->toDateString());

        $data = $this->getReportData($reportType, $request, $dateFrom, $dateTo);

        AuditLog::record('Reports', 'Export CSV', "Exported CSV for task report: {$reportType}");

        $filename = "task_report_{$reportType}_" . date('Ymd_His') . ".csv";

        $response = new StreamedResponse(function() use ($reportType, $data) {
            $handle = fopen('php://output', 'w');
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel

            fputcsv($handle, ['Task Code', 'Directive Title', 'Assigned By (Tagged)', 'Assigned To', 'Role', 'Cluster', 'Priority', 'Status', 'Due Date', 'Evidence Count', 'Remarks']);
            foreach ($data['tasks'] as $t) {
                fputcsv($handle, [
                    $t->task_code,
                    $t->title,
                    $t->assignedBy->name . ' (' . $t->assignedBy->role_label . ')',
                    $t->assignedTo->name ?? '',
                    $t->assignedTo->role_label ?? '',
                    $t->cluster ?? 'N/A',
                    strtoupper($t->priority),
                    $t->status_label,
                    $t->due_datetime->format('Y-m-d H:i'),
                    !empty($t->evidence_attachments) ? count($t->evidence_attachments) : 0,
                    $t->completion_remarks ?? '',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    private function getReportData($type, Request $request, $dateFrom, $dateTo)
    {
        $query = StaffTask::with(['assignedTo', 'assignedBy'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo);

        if ($request->filled('cluster')) {
            $query->where('cluster', $request->cluster);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('assigned_by')) {
            $query->where('assigned_by', $request->assigned_by);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($type === 'completed_only') {
            $query->where('status', 'completed');
        } elseif ($type === 'overdue_only') {
            $query->whereNotIn('status', ['completed', 'cancelled'])->where('due_datetime', '<', Carbon::now());
        }

        $tasks = $query->latest('due_datetime')->get();

        return ['tasks' => $tasks];
    }
}
