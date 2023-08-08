<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeUser extends Model
{
    use HasFactory;

    protected $table='type_users';
    protected $fillable= ['name'];
    public function getNotFoundMessage():string
    {
        return 'Tipo de usuário não encontrado';
    }
}
