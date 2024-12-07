<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'phone',
        'created_by',
        'updated_by',
        'admission_date',
        'demission_date',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'employees_projects');
    }

    public function responsibleProjects()
    {
        return $this->hasMany(Project::class, 'responsible_id');
    }
}