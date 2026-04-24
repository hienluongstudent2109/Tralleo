<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'position'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
