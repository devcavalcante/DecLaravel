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
        $fileNames = array_column($documents, 'file');

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

        // Cria um novo arquivo zip
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::OVERWRITE|ZipArchive::CREATE) === TRUE) {
            // dd('a');
            // Adiciona cada arquivo ao arquivo zip
            foreach ($fileNames as $fileName) {
                $filePath = storage_path('app/' . $fileName);
                if (file_exists($filePath)) {
                    preg_match('/[^\/]+$/', $fileName, $matches);
                    $zip->addFile($filePath, $matches[0]);
                }
            }
            $zip->close();
        }

        return $zipFilePath;
    
        // Verifica se o arquivo zip foi criado com sucesso
        // if (file_exists($zipFilePath)) {
        //     // Faz o download do arquivo zip
        //     return response()->download($zipFilePath)->deleteFileAfterSend(true);
        // } else {
        //     // Se ocorrer algum erro, redireciona de volta à página anterior
        //     return back()->with('error', 'Erro ao criar o arquivo zip.');
        // }
    }
}
