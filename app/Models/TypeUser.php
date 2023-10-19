<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeUser extends Model
{
    use HasFactory;

    protected $table = 'type_users';

    protected $fillable = ['name'];

    public function getNotFoundMessage():string
    {
        return 'Tipo de usuario não encontrado';
    }

    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
