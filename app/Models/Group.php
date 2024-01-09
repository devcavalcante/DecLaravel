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
        'status',
        'type_group_id',
        'representative_id',
        'creator_user_id',
    ];

    protected $hidden = [
        'type_group_id',
        'creator_user_id',
    ];

    protected $with = ['typeGroup', 'user', 'representative', 'members'];

    public function getNotFoundMessage(): string
    {
        return 'Grupo não encontrado';
    }

    public function representative(): BelongsTo
    {
        return $this->belongsTo(Representative::class, 'representative_id');
    }

    public function typeGroup(): BelongsTo
    {
        return $this->belongsTo(TypeGroup::class, 'type_group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
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

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'members_has_groups', 'group_id', 'member_id');
    }
}
