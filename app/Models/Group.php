<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    protected $fillable = [
        'entity',
        'organ',
        'council',
        'acronym',
        'team',
        'unit',
        'email',
        'office_requested',
        'office_indicated',
        'internal_concierge',
        'observations',
        'type_group_id',
        'creator_user_id',
    ];

    protected $hidden = [
        'type_group_id',
        'creator_user_id',
    ];

    protected $with = ['typeGroup', 'user', 'representative'];

    public function getNotFoundMessage(): string
    {
        return 'Grupo nao encontrado';
    }

    public function representative(): HasMany
    {
        return $this->hasMany(GroupHasRepresentative::class);
    }

    public function typeGroup(): BelongsTo
    {
        return $this->belongsTo(TypeGroup::class, 'type_group_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id', 'id');
    }
}
