<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    use HasFactory;

    // Relaciones Uno a Muchos
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
