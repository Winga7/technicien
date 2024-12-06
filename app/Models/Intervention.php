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
        'statut',  // 'status' devient 'statut'
        'client_id',
        'technicien_id',
        'images'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function technicien() // 'technician' devient 'technicien'
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }
}
