<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'statut',
        'client_id',
        'ticket_id',
        'images'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    // Mutator pour les images
    public function setImagesAttribute($value)
    {
        $this->attributes['images'] = is_array($value) ? json_encode($value) : $value;
    }

    // Accessor pour les images
    public function getImagesAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return $value ?? [];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function techniciens()
    {
        return $this->belongsToMany(User::class, 'intervention_technicien', 'intervention_id', 'technicien_id');
    }
}
