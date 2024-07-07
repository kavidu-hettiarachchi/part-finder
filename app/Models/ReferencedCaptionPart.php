<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferencedCaptionPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'caption_id',
        'partNumber',
        'orgCode',
        'ieSystemControlNumber',
        'disambiguation',
        'componentId',
    ];

    public function caption(): BelongsTo
    {
        return $this->belongsTo(Caption::class);
    }
}

