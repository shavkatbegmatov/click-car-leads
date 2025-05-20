<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    public mixed $status;
    protected $fillable = [
        'name', 'phone', 'car_model', 'status', 'note', 'manager_id'
    ];

    public function manager(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }
}
