<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
    'client_id', 'user_id', 'area_id', 'status_id', 
    'priority_id', 'title', 'description', 
    'requested_at', 'due_date', 'completed_at'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // Relaciones inversas (Muchos a Uno)
    public function client() { return $this->belongsTo(Client::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function area() { return $this->belongsTo(Area::class); }
    public function status() { return $this->belongsTo(Status::class); }
    public function priority() { return $this->belongsTo(Priority::class); }

    // Relación con los comentarios (Uno a Muchos)
    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }
}
