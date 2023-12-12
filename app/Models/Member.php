<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'role',
        'phone',
        'user_id',
        'group_id',
        'entry_date',
        'departure_date',
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

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
