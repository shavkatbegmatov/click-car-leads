<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property-read int           $id
 * @property      string        $name
 * @property      string        $phone
 * @property      string        $car_model
 * @property      string        $status
 * @property      string|null   $note
 * @property      int|null      $manager_id
 * @property-read Manager|null  $manager
 * @property-read Carbon        $created_at
 * @property-read Carbon        $updated_at
 */
class Lead extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'car_model',
        'status',
        'note',
        'manager_id',
    ];

    public function manager(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }
}
