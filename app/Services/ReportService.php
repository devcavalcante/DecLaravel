<?php

namespace App\Services;

use App\Repositories\Interfaces\GroupRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use ZipArchive;

class ReportService
{
    public function __construct(
        protected GroupRepositoryInterface $groupRepository
    ) {
    }

    public function upload(string $id, array $filters)
    {
        $group = $this->groupRepository->findById($id);
        $data = [
            'representative' => $group->representative->email,
            'manager' => $group->user->name,
            'membersCount' => $group->members->count(),
            'members' => $group->members,
            'group' => $group,
            'typeGroup' => $group->typeGroup
        ];

        $pdf = Pdf::loadView('pdf.report', $data);
        $filename = 'relatorio_' . time() . '.pdf';

        if (!file_exists(storage_path('app/pdf'))) {
            mkdir(storage_path('app/pdf'), 0775, true);
        }

        $pdf->save(storage_path('app/pdf/' . $filename));

        $documents = $group->documents->toArray();
        $fileDocuments = array_column($documents, 'file');
        $meetings = $group->meetings->toArray();
        $fileMeetings = array_column($meetings, 'ata');
        $fileNames = array_merge($fileDocuments, $fileMeetings, ['pdf/' . $filename]);

        if(Arr::get($filters, 'withFiles')){
            $zipFilePath = $this->downloadZip($fileNames);
            return $zipFilePath;
        }

        return $filename;
    }

    protected function downloadZip(array $fileNames)
    {
        // Nome do arquivo zip
        $zipFileName = 'docs.zip';    
        $zipFilePath = storage_path("app/zip/{$zipFileName}");

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::OVERWRITE|ZipArchive::CREATE) === TRUE) {
            foreach ($fileNames as $fileName) {
                $filePath = storage_path('app/' . $fileName);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $fileName);
                }
            }
            $zip->close();
        }

        return $zipFilePath;
    }
}
