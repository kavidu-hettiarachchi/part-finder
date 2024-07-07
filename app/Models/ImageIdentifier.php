<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageIdentifier extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id',
        'line_item_id',
        'imageId',
    ];

    public function part():BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function lineItem(): BelongsTo
    {
        return $this->belongsTo(LineItem::class);
    }
}
