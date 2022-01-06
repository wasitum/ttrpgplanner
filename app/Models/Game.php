<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, Relations\BelongsTo, Relations\BelongsToMany, Relations\HasMany, SoftDeletes};

class Game extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'label',
        'user_id',
        'date',
        'max_players',
        'description',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'max_players' => 'integer',
    ];

    protected $dates = [
        'date',
    ];

    protected $with = [
        'user',
        'players',
        'possibledates',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function possibledates(): HasMany
    {
        return $this->hasMany(Possibledate::class)->orderBy('date');
    }
}
