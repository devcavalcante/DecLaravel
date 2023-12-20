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
            'user'           => $member->user ? [
                'id'        => $member->user->id,
                'name'      => $member->user->name,
                'email'     => $member->user->email,
                'type_user' => $member->user->typeUser->name,
            ] : null,
        ];
    }
}
