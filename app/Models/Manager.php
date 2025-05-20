<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property-read int               $id
 * @property      string            $name
 * @property      string            $status
 * @property-read Carbon            $created_at
 * @property-read Carbon            $updated_at
 * @property-read Collection|Lead[] $leads
 */
class Manager extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    public function leads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
