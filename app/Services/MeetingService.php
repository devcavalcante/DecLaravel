<?php

namespace App\Services;

use App\Repositories\Interfaces\GroupRepositoryInterface;
use App\Repositories\Interfaces\MeetingRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class MeetingService
{
    public function __construct(
        protected MeetingRepositoryInterface $meetingRepository,
        protected GroupRepositoryInterface $groupRepository
    ) {
    }

    public function create(string $groupId, array $data): Model
    {
        $this->groupRepository->findById($groupId);

        $file = Arr::get($data, 'ata');
        $uploadedFile = $this->upload($file);

        Arr::set($data, 'ata', $uploadedFile);

        $data = array_merge($data, ['group_id' => $groupId]);
        return $this->meetingRepository->create($data);
    }

    public function edit(string $id, array $data): Model
    {
        $file = Arr::get($data, 'ata');

        if (!empty($file)) {
            Arr::set($data, 'ata', $this->upload($file));
        }

        return $this->meetingRepository->update($id, $data);
    }

    private function upload(UploadedFile $file): string
    {
        $fileName = uniqid() . '_' . $file->getClientOriginalName();

        Storage::disk('local')->put('atas/' . $fileName, file_get_contents($file));

        return 'atas/' . $fileName;
    }

    public function listAll(string $groupId)
    {
        $group = $this->groupRepository->findById($groupId);

        return $group->meetings;
    }
}
