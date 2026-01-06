<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guarantor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loan_id',
        'full_name',
        'email',
        'phone',
        'address',
        'relationship',
        'id_type',
        'id_number',
        'id_front_path',
        'id_back_path',
        'passport_picture_path',
    ];

    /**
     * Get the loan that this guarantor belongs to.
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Accessor for full file URLs.
     */
    public function getIdFrontUrlAttribute(): ?string
    {
        return $this->id_front_path ? asset('storage/' . $this->id_front_path) : null;
    }

    public function getIdBackUrlAttribute(): ?string
    {
        return $this->id_back_path ? asset('storage/' . $this->id_back_path) : null;
    }

    public function getPassportPictureUrlAttribute(): ?string
    {
        return $this->passport_picture_path ? asset('storage/' . $this->passport_picture_path) : null;
    }

    /**
     * Get formatted ID type.
     */
    public function getFormattedIdTypeAttribute(): string
    {
        return match($this->id_type) {
            'ghana_card' => 'Ghana Card',
            'voters_id' => "Voter's ID",
            'drivers_license' => "Driver's License",
            'passport' => 'Passport',
            'national_id' => 'National ID',
            default => ucfirst(str_replace('_', ' ', $this->id_type))
        };
    }

    /**
     * Get formatted relationship.
     */
    public function getFormattedRelationshipAttribute(): string
    {
        return match($this->relationship) {
            'parent' => 'Parent',
            'spouse' => 'Spouse',
            'sibling' => 'Sibling',
            'friend' => 'Friend',
            'colleague' => 'Colleague',
            'relative' => 'Relative',
            'other' => 'Other',
            default => ucfirst($this->relationship)
        };
    }
}