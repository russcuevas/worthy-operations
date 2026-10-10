<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Operations Mission Report | 627 Admin</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 24px; color: #1E293B; }
        .report-header { text-align: center; border-bottom: 2px solid #075998; padding-bottom: 16px; margin-bottom: 20px; }
        .report-title { font-size: 20px; font-weight: 800; color: #075998; margin: 0; }
        .report-sub { font-size: 13px; color: #64748B; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 12px; }
        th, td { border: 1px solid #CBD5E1; padding: 8px 10px; text-align: left; }
        th { background: #F1F5F9; font-weight: 700; color: #0F172A; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700; }
    </style>
</head>
<body onload="window.print()">
    <div class="report-header">
        <h1 class="report-title">627 OPERATIONS TASK REPORT</h1>
        <div class="report-sub">Generated on {{ date('M d, Y h:i A') }} | Period: {{ $dateFrom }} to {{ $dateTo }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Directive Title</th>
                <th>Assigned By</th>
                <th>Assigned To</th>
                <th>Cluster</th>
                <th>Deadline</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['tasks'] as $t)
                <tr>
                    <td><code>{{ $t->task_code }}</code></td>
                    <td><strong>{{ $t->title }}</strong></td>
                    <td>{{ $t->assignedBy->name }}</td>
                    <td>{{ $t->assignedTo->name }} ({{ $t->assignedTo->role_label }})</td>
                    <td>{{ $t->cluster ?? 'N/A' }}</td>
                    <td>{{ $t->due_datetime->format('M d, Y h:i A') }}</td>
                    <td>{{ $t->status_label }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
