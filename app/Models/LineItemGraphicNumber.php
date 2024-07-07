<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class LineItemGraphicNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_item_id',
        'graphicNumber',
    ];

    public function lineItem(): BelongsTo
    {
        return $this->belongsTo(LineItem::class);
    }
}
