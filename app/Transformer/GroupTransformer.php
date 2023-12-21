<?php

namespace App\Transformer;

use App\Models\Group;
use League\Fractal\TransformerAbstract;

class GroupTransformer extends TransformerAbstract
{
    public function transform(Group $group): array
    {
        return [
            'id'                 => $group->id,
            'entity'             => $group->entity,
            'organ'              => $group->organ,
            'council'            => $group->council,
            'acronym'            => $group->acronym,
            'team'               => $group->team,
            'unit'               => $group->unit,
            'email'              => $group->email,
            'office_requested'   => $group->office_requested,
            'office_indicated'   => $group->office_indicated,
            'internal_concierge' => $group->internal_concierge,
            'observations'       => $group->observations,
            'created_at'         => $group->created_at,
            'updated_at'         => $group->updated_at,
            'created_by'         => [
                'id'        => $group->user->id,
                'name'      => $group->user->name,
                'email'     => $group->user->email,
                'type_user' => $group->user->typeUser->name,
            ],
            'type_group'         => [
                'id'   => $group->typeGroup->id,
                'name' => $group->typeGroup->name,
                'type' => $group->typeGroup->type_group,
            ],
            'representative'    => [
                'id'    => $group->representative->id,
                'email' => $group->representative->email,
            ],
            'members'            => $this->transformMembers($group->members->toArray()),
        ];
    }

    protected function transformMembers(array $members): array
    {
        $transformedMembers = [];

        foreach ($members as $member) {
            $transformedMembers[] = [
                'id'             => $member['id'],
                'email'          => $member['email'],
                'role'           => $member['role'],
                'phone'          => $member['phone'],
                'entry_date'     => $member['entry_date'],
                'departure_date' => $member['departure_date'],
                'created_at'     => $member['created_at'],
                'updated_at'     => $member['updated_at'],
            ];
        }

        return $transformedMembers;
    }
}
