<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeGroup extends Model
{
    use HasFactory;

    protected $table = 'type_groups';

    protected $fillable = ['name', 'type_group'];

    public function getNotFoundMessage(): string
    {
        return 'Tipo de grupo não encontrado';
    }
}
