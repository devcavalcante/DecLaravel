<?php

namespace App\Transformer;

use App\Models\Member;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class MemberTransformer extends TransformerAbstract
{
    public function transform(Member $member): array
    {
        return [
            'id'             => $member->id,
            'email'          => $member->email,
            'role'           => $member->role,
            'phone'          => $member->phone,
            'entry_date'     => $member->entry_date,
            'departure_date' => $member->departure_date,
            'created_at'     => $member->created_at,
            'updated_at'     => $member->updated_at,
            'user'           => $member->user ? $this->getUser($member->user) : null,
        ];
    }

    private function getUser(User $user): array
    {
        return [
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'type_user' => $user->typeUser->name,
        ];
    }
}
