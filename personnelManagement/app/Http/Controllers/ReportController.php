<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Application;
use Carbon\Carbon;


class ReportController extends Controller
{
   public function applicants($format, Request $request)
{
    $status = $request->get('status', 'all');
    $range = $request->get('range', 'monthly');
    $jobId = $request->get('job_id');

    // ✅ Query applicants
    $query = Application::with(['user', 'job']);
    if ($status !== 'all') {
        $query->where('status', $status);
    }
    if ($jobId) {
        $query->where('job_id', $jobId);
    }
    $applications = $query->get();

    if ($format === 'pdf') {
        $pdf = Pdf::loadView('reports.applicants', [
            'applications' => $applications,
            'status' => $status,
            'range' => $range,
        ])->setPaper('a4', 'portrait');

        // ✅ Dynamic filename
        $filename = 'Applicants_' . ucfirst($status) . '_' . now()->format('M-d-Y') . '.pdf';

        return $pdf->download($filename);
    }

    abort(404, 'Unsupported format');
}

}
