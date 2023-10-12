<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeGroup extends Model
{
    use HasFactory;

    protected $table = 'type_groups';

    protected $fillable = ['name', 'type_group'];

    public function getNotFoundMessage(): string
    {
        return 'Tipo de grupo não encontrado';
    }

    public function group(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
