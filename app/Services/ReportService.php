<?php

namespace App\Services;

use App\Repositories\Interfaces\GroupRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ZipArchive;

class ReportService
{
    public function __construct(
        protected GroupRepositoryInterface $groupRepository
    ) {
    }

    public function uploadById(string $id, array $filters): string
    {
        $group = $this->groupRepository->findById($id);
        $data = [
            'representative' => $group->representative->email,
            'manager'        => $group->user->name,
            'membersCount'   => $group->members->count(),
            'members'        => $group->members,
            'group'          => $group,
            'typeGroup'      => $group->typeGroup,
        ];

        $pdf = Pdf::loadView('pdf.report', $data);
        $filename = 'relatorio_' . time() . '.pdf';

        $pdf->save(storage_path('app/pdf/' . $filename));

        $zipName = sprintf('documento%s.zip', $group->id);
        $documents = $group->documents->toArray();
        $fileDocuments = array_column($documents, 'file');
        $meetings = $group->meetings->toArray();
        $fileMeetings = array_column($meetings, 'ata');
        $fileNames = array_merge($fileDocuments, $fileMeetings, ['pdf/' . $filename]);

        return Arr::get($filters, 'withFiles') ? $this->downloadZip($fileNames, $zipName) : $this->formatFileName($filename);
    }

    public function uploadMany(array $filters): string
    {
        $status = Arr::get($filters, 'status');
        $startDate = Arr::get($filters, 'start_date');
        $endDate = Arr::get($filters, 'end_date');

        $groups = $this->getGroupsByFilter($status, $startDate, $endDate);

        $data = [
            'groups' => $groups,
        ];

        $pdf = Pdf::loadView('pdf.reportMany', $data);
        $filename = 'relatorio_' . time() . '.pdf';

        $pdf->save(storage_path('app/pdf/' . $filename));

        return $this->formatFileName($filename);
    }

    protected function downloadZip(array $fileNames, string $zipName): string
    {
        $zipFilePath = storage_path("app/zip/{$zipName}");

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::OVERWRITE|ZipArchive::CREATE) === true) {
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

    private function formatFileName(string $fileName): string
    {
        return storage_path("app/pdf/". $fileName);
    }

    private function getGroupsByFilter(?string $status, ?string $startDate, ?string $endDate)
    {
        $groups = $this->groupRepository->listAll();

        if (!is_null($status)) {
            $groups = $this->groupRepository->findByFilters(['status' => $status]);
        }

        if (!is_null($startDate)) {
            $groups = $this->groupRepository->findWhereBetween($startDate, $endDate);
        }

        if (!is_null($status) && !is_null($startDate)) {
            $groups = $this->groupRepository->findWhereBetweenWithFilters($startDate, $endDate, $status);
        }

        if ($groups->isEmpty()) {
            throw new NotFoundHttpException('Não encontrado nenhum grupo para o relatório.');
        }

        return $groups;
    }
}
