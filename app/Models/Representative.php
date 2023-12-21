<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Representative extends Model
{
    use HasFactory;

    protected $table = 'representatives';

    public $timestamps = true;

    protected $fillable = [
        'email',
        'user_id',
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
