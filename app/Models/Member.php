<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Storage;

class Member extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'country_code_id',
        'phone',
        'email',
        'gender',
        'date_of_birth',
        'status',
        'profile_photo',
        'address',
        'branch_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullPhoneNumberAttribute(): ?string
    {
        return PhoneNumber::normalize($this->phone, $this->countryCode?->dial_code);
    }

    public function getDisplayPhoneAttribute(): ?string
    {
        return PhoneNumber::display($this->phone, $this->countryCode?->dial_code);
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo ? Storage::url($this->profile_photo) : null;
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function countryCode(): BelongsTo
    {
        return $this->belongsTo(CountryCode::class);
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_member')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function ministries(): BelongsToMany
    {
        return $this->belongsToMany(Ministry::class, 'ministry_member')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function leadershipRoles(): BelongsToMany
    {
        return $this->belongsToMany(LeadershipRole::class, 'member_leadership')
                    ->withPivot('assigned_at')
                    ->withTimestamps();
    }

    public function submittedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'submitted_by');
    }

    public function reviewedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'reviewed_by');
    }

    public function financeTransactions(): HasMany
    {
        return $this->hasMany(FinanceTransaction::class, 'recorded_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }
}
