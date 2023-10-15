<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'role',
        'phone',
        'user_id',
        'entry_date',
        'departure_date',
    ];

    protected $table = 'members';

    protected $casts = [
        'entry_date' => 'datetime',
        'departure_date' => 'datetime'
    ];

    public function getNotFoundMessage(): string
    {
        return 'Membro não encontrado';
    }
}
