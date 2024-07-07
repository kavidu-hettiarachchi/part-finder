<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id',
        'noteCodes',
        'partNumber',
        'orgCode',
        'partName',
        'partNameLanguage',
        'serviceabilityIndicator',
        'partSequenceNumber',
        'parentage',
        'quantity',
        'ieSystemControlNumber',
        'disambiguation',
        'mediaNumber',
        'componentId',
        'comments',
        'referenceNumber',
        'alternatePartType',
        'modifier',
        'modifierLanguage',
        'isCCRPart',
        'hasAlternate',
    ];

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function imageIdentifiers(): HasMany
    {
        return $this->hasMany(ImageIdentifier::class);
    }

    public function graphicNumbers(): HasMany
    {
        return $this->hasMany(LineItemGraphicNumber::class);
    }
}
