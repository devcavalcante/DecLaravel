<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use Illuminate\Http\Request;
use App\Services\ReportService;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {
    }

    public function downloadById(ReportRequest $request, string $id): BinaryFileResponse
    {
        $filename = $this->reportService->uploadById($id, $request->get('filters'));
        $headers = [];

        if (preg_match('/\.zip$/', $filename)) {
            $headers = ["Content-Type" => "application/zip"];
        }

        return response()->download($filename, null, $headers)->deleteFileAfterSend();
    }

    public function download(ReportRequest $request): BinaryFileResponse
    {
        $filename = $this->reportService->uploadMany($request->get('filters'));
        $headers = [];

        if (preg_match('/\.zip$/', $filename)) {
            $headers = ["Content-Type" => "application/zip"];
        }

        return response()->download($filename, null, $headers)->deleteFileAfterSend();
    }
}
