<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectFinalLink extends Model
{
    protected $fillable = ['project_id', 'url'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
