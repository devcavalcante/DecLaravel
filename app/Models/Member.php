<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'role',
        'phone',
        'user_id',
    ];

    protected $casts = [
        'entry_date' => 'datetime',
    ];

    public function getNotFoundMessage(): string
    {
        return 'Membro não encontrado';
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
