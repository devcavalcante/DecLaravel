<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    protected $with = ['typeGroup', 'user', 'representatives', 'userMembers'];

    public function getNotFoundMessage(): string
    {
        return 'Grupo não encontrado';
    }

    public function representatives(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_has_representatives', 'group_id', 'user_id')->withTimestamps();
    }

    public function userMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'members', 'group_id', 'user_id')
            ->withTimestamps()
            ->withPivot('id', 'role', 'phone', 'entry_date', 'departure_date', 'created_at', 'updated_at');
    }

    public function typeGroup(): BelongsTo
    {
        return $this->belongsTo(TypeGroup::class, 'type_group_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id', 'id');
    }

    public function document(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function meeting(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function activity(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function note(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
