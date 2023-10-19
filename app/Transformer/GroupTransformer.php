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
            'representatives'    => $this->transformRepresentatives($group->representatives->toArray()),
            'members'            => $this->transformMembers($group->userMembers->toArray()),
        ];
    }

    protected function transformRepresentatives(array $representatives): array
    {
        $transformedRepresentatives = [];

        foreach ($representatives as $representative) {
            $transformedRepresentatives[] = [
                'id'        => $representative['id'],
                'name'      => $representative['name'],
                'email'     => $representative['email'],
                'type_user' => $representative['type_user']['name'],
            ];
        }

        return $transformedRepresentatives;
    }

    protected function transformMembers(array $members): array
    {
        $transformedMembers = [];

        foreach ($members as $member) {
            $transformedMembers[] = [
                'id'             => $member['pivot']['id'],
                'role'           => $member['pivot']['role'],
                'phone'          => $member['pivot']['phone'],
                'entry_date'     => $member['pivot']['entry_date'],
                'departure_date' => $member['pivot']['departure_date'],
                'created_at'     => $member['pivot']['created_at'],
                'updated_at'     => $member['pivot']['updated_at'],
                'user'           => [
                    'id'        => $member['id'],
                    'name'      => $member['name'],
                    'email'     => $member['email'],
                    'type_user' => $member['type_user']['name'],
                ],
            ];
        }

        return $transformedMembers;
    }
}
