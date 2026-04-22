<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
