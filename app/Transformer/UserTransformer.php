<?php

namespace App\Transformer;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user): array
    {
        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'creator_user_id'   => $user->creator_user_id,
            'created_at'        => $user->created_at,
            'updated_at'        => $user->updated_at,
            'deleted_at'        => $user->deleted_at,
            'file_url'          => $user->file_url,
            'type_user'         => $user->typeUser->name,
        ];
    }
}
