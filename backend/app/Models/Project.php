<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'client_id', 
        'address_id', 
        'situation_id', 
        'type_id', 
        'created_by',
        'updated_by', 
        'created_at',
        'mcmv', 
        'square_meters', 
        'price'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function situation()
    {
        return $this->belongsTo(ProjectSituation::class);
    }

    public function type()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

