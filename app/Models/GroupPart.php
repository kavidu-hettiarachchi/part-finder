<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id',
        'partNumber',
        'orgCode',
        'partName',
        'partLanguage',
        'modifier',
        'modifierLanguage',
        'serviceabilityIndicator',
        'alternatePartType',
        'hasAlternate',
        'isCCRPart',
    ];

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }
}
