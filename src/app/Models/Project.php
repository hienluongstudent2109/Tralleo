<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'workspace_id',
        'name',
        'description',
        'created_by'
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function columns()
    {
        return $this->hasMany(Column::class);
    }
}
