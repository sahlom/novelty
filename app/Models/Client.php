<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
    'razon_social', 'contacto', 'rfc', 'tel', 'email', 
    'csf', 'opinion_cumplimiento', 'fiel', 'fiel_vigencia', 
    'csd', 'csd_vigencia'
    ];

    protected $casts = [
    'fiel_vigencia' => 'date',
    'csd_vigencia' => 'date',
    ];

    // Relaciones Uno a Muchos
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
