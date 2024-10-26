<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name', 'cpf', 'phone', 'email', 'birth_date', 'created_by', 'updated_by',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
