<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'statut',
        'client_id',
        'technicien_id',
        'images'
    ];

    protected $casts = [
        'images' => 'array'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
}
