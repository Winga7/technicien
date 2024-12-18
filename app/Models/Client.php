<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'prenom', 'email', 'telephone', 'addresse'];

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
}
