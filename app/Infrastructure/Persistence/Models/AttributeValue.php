<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attribute_id',
        'entity_type',
        'entity_id',
        'value',
    ];

    /**
     * Get the attribute that this value belongs to.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get the typed value based on the attribute type
     * 
     * @return mixed
     */
    public function getTypedValue()
    {
        $attributeType = $this->attribute->type;

        return match ($attributeType) {
            'integer' => (int)$this->value,
            'decimal' => (float)$this->value,
            'boolean' => (bool)$this->value,
            'date', 'datetime' => new \DateTime($this->value),
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }
}
