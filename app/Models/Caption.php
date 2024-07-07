<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Caption extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id',
        'name',
        'language',
        'orgCode',
        'kitsLinkedMediaNumber',
    ];

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function referencedCaptionParts(): HasMany
    {
        return $this->hasMany(ReferencedCaptionPart::class);
    }
}
