<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
class TypeUser extends Model{
    use HasFactory;
=======

class TypeUser extends Model
{
>>>>>>> origin/DEC-45-backend-crud-tipos-de-usuarios
    protected $table='type_users';
    protected $fillable= ['name'];
    public function getNotFoundMessage():string
    {
        return 'Tipo de usuário não encontrado';
    }
}
