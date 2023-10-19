<?php

namespace App\Transformer;

use App\Models\Member;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class MemberTransformer extends TransformerAbstract
{
    public function transform(User|Member $member): array
    {
        return $member instanceof User ? $this->getTransformUser($member) : $this->getTransformMember($member);
    }

    private function getTransformUser(User $user): array
    {
        return [
            'id' => $user->pivot->id,
            'role' => $user->pivot->role,
            'phone' => $user->pivot->phone,
            'entry_date' => $user->pivot->entry_date,
            'departure_date' => $user->pivot->departure_date,
            'created_at' => $user->pivot->created_at,
            'updated_at' => $user->pivot->updated_at,
            'group_id' => $user->pivot->group_id,
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'type_user' => $user->typeUser->name,
            ],
        ];
    }

    private function getTransformMember(Member $member): array
    {
        $user = $member->user;

        return [
            'id' => $member->id,
            'role' => $member->role,
            'phone' => $member->phone,
            'entry_date' => $member->entry_date,
            'departure_date' => $member->departure_date,
            'created_at' => $member->created_at,
            'updated_at' => $member->updated_at,
            'group_id' =>$member->group_id,
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'type_user' => $user->typeUser->name,
            ],
        ];
    }
}
