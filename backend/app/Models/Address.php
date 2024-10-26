<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'street', 'number', 'city', 'latitude', 'longitude', 'client_id', 'created_by', 'updated_by',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
