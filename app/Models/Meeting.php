<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
{
    use HasFactory;

    protected $table = 'meetings';

    protected $fillable = [
        'content',
        'summary',
        'ata',
        'groups_id',
    ];

    public function getNotFoundMessage(): string
    {
        return 'Usuario nao encontrado';
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
