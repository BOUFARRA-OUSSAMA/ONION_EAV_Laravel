<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Application\Services\EAVService;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $roleCode): bool
    {
        return $this->roles()->where('code', $roleCode)->exists();
    }

    /**
     * Get an attribute value
     *
     * @param string $attributeCode
     * @return mixed|null
     */
    public function getAttribute($attributeCode)
    {
        // If it's a model attribute, use the parent method
        if (
            array_key_exists($attributeCode, $this->attributes) ||
            $this->hasGetMutator($attributeCode) ||
            $this->isRelation($attributeCode)
        ) {
            return parent::getAttribute($attributeCode);
        }

        // Skip EAV attributes if ID is not set
        if (!isset($this->attributes['id']) || empty($this->attributes['id'])) {
            return null;
        }

        // Otherwise, try to get it as an EAV attribute
        try {
            $eavService = app(EAVService::class);
            return $eavService->getAttributeValue('user', $this->attributes['id'], $attributeCode);
        } catch (\Exception $e) {
            // Fail gracefully
            return null;
        }
    }

    /**
     * Set an attribute value
     *
     * @param string $attributeCode
     * @param mixed $value
     * @return void
     */
    public function setAttribute($attributeCode, $value)
    {
        // If it's a model attribute, use the parent method
        if (
            array_key_exists($attributeCode, $this->attributes) ||
            $this->hasSetMutator($attributeCode) ||
            $this->isRelation($attributeCode)
        ) {
            return parent::setAttribute($attributeCode, $value);
        }

        // Skip EAV attributes if ID is not set
        if (!isset($this->attributes['id']) || empty($this->attributes['id'])) {
            // Store for later processing if needed
            $this->attributes[$attributeCode] = $value;
            return;
        }

        // Otherwise, set it as an EAV attribute
        try {
            $eavService = app(EAVService::class);
            $eavService->setAttributeValue('user', $this->attributes['id'], $attributeCode, $value);
        } catch (\Exception $e) {
            // Fail gracefully
        }
    }

    /**
     * Get the entity type code
     * 
     * @return string
     */
    public function getEntityTypeCode(): string
    {
        return 'user';
    }
}