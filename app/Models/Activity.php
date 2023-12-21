<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';
    protected $fillable = ['name', 'description', 'group_id', 'start_date', 'end_date', 'done_at'];

    public function getNotFoundMessage(): string
    {
        return 'Atividade não encontrada';
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
