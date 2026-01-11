<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(['admin', 'manager', 'staff']);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, Customer::class);
    }

    /**
     * Get donations for this user.
     * Donations can be linked via customer_id (through customer) or by email match.
     * This is a query scope method, not a relationship.
     */
    public function getDonations()
    {
        $customer = $this->customer;
        $customerId = $customer?->id;
        
        return Donation::where(function ($query) use ($customerId) {
            if ($customerId) {
                $query->where('customer_id', $customerId);
            }
            $query->orWhere('email', $this->email);
        });
    }

    /**
     * Get orders for this user.
     * Orders can be linked via customer_id (through customer) or by customer email match.
     * This handles cases where guest checkout created a separate customer record.
     * This is a query scope method, not a relationship.
     */
    public function getOrders()
    {
        $customer = $this->customer;
        $customerId = $customer?->id;
        
        return Order::where(function ($query) use ($customerId) {
            if ($customerId) {
                $query->where('customer_id', $customerId);
            }
            // Also find orders for customers with the same email (guest checkout scenario)
            $query->orWhereHas('customer', function ($q) {
                $q->where('email', $this->email);
            });
        });
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->where('is_read', false)->latest();
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}
