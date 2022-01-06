<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, Relations\BelongsTo, Relations\BelongsToMany, SoftDeletes};

class Possibledate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'game_id',
    ];

    protected $with = [
        'users',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
