<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use Illuminate\Http\Request;
use App\Services\ReportService;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {
    }

    public function downloadById(Request $request, string $id)
    {
        $request->validate(['filters.withFiles' => 'required']);
        $filename = $this->reportService->upload($id, $request->get('filters'));
        $filePath = storage_path("app/pdf/{$filename}");
        return response()->download($filename);
    }
}
