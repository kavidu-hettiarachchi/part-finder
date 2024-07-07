<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends Model
{
    use HasFactory;

    protected $fillable = [
        'iePublicationDate',
        'ieUpdateDate',
        'ieControlNumber',
        'mediaNumber',
        'isExpandedMiningProduct',
    ];

    public function captions(): HasMany
    {
        return $this->hasMany(Caption::class);
    }

    public function groupParts(): HasMany
    {
        return $this->hasMany(GroupPart::class);
    }

    public function smcsCodes(): HasMany
    {
        return $this->hasMany(SmcsCode::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function imageIdentifiers(): HasMany
    {
        return $this->hasMany(ImageIdentifier::class);
    }

    public function referencedCaptionParts(): HasMany
    {
        return $this->hasMany(ReferencedCaptionPart::class);
    }
}

