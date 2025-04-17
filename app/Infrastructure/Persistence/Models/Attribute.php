<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'type',
        'description',
        'is_required',
        'entity_type_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Get the entity type that this attribute belongs to.
     */
    public function entityType()
    {
        return $this->belongsTo(EntityType::class);
    }

    /**
     * Get the attribute values for this attribute.
     */
    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
