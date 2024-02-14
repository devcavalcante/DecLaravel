<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'phone',
        'user_id',
        'entry_date',
        'departure_date',
        'email',
    ];

    protected $table = 'members';

    protected $casts = [
        'entry_date'     => 'datetime',
        'departure_date' => 'datetime',
    ];

    public function getNotFoundMessage(): string
    {
        return 'Membro não encontrado';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'members_has_groups', 'member_id', 'group_id');
    }
}
