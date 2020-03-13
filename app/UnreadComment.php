<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnreadComment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'revision_id',
        'issue_id',
        'comment_id',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function revision()
    {
        return $this->belongsTo(Project::class);
    }

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

}
