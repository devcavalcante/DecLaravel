<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberHasGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'member_id',
    ];

    protected $table = 'members_has_groups';

    public function getNotFoundMessage(): string
    {
        return 'Membro não encontrado';
    }
}
