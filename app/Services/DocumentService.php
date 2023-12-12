<?php

namespace App\Services;

use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Repositories\Interfaces\GroupRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function __construct(
        protected DocumentRepositoryInterface $documentRepository,
        protected GroupRepositoryInterface $groupRepository
    ) {
    }

    public function create(string $groupId, array $data): Model
    {
        $this->groupRepository->findById($groupId);

        $file = Arr::get($data, 'file');
        $uploadedFile = $this->upload($file);

        Arr::set($data, 'file', $uploadedFile);
        Arr::set($data, 'name', $file->getClientOriginalName());
        Arr::set($data, 'file_size', $this->calculateMb($uploadedFile));

        $data = array_merge($data, ['group_id' => $groupId]);
        return $this->documentRepository->create($data);
    }

    public function edit(string $id, array $data): Model
    {
        $file = Arr::get($data, 'file');

        if (!empty($file)) {
            $uploadedFile = $this->upload($file);
            Arr::set($data, 'file', $uploadedFile);
            Arr::set($data, 'name', $file->getClientOriginalName());
            Arr::set($data, 'file_size', $this->calculateMb($uploadedFile));
        }

        return $this->documentRepository->update($id, $data);
    }

    public function delete(string $groupId, string $documentId): void
    {
        $this->groupRepository->findById($groupId);
        $document = $this->documentRepository->findById($documentId);
        Storage::delete($document->file);
        $this->documentRepository->delete($documentId);
    }


    private function upload(UploadedFile $file): string
    {
        return Storage::disk('local')->put('documents', $file);
    }

    private function calculateMb(string $uploadedFile): float
    {
        $fileSize = Storage::disk('local')->size($uploadedFile);
        $mb = $fileSize / (1024 * 1024);
        return round($mb, 2);
    }
}
