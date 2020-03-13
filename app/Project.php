<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Revision;
use App\ProjectFinalFile;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = [
        'name',
        'creative_brief',
        'company',
        'status',
        'project_hash',
        'type',
        'website_url',
        'video_url',
        'created_by',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'first_open')->withTimestamps();
    }

    public function client()
    {
        return $this->belongsTo(User::class);
    }

    public function revisions()
    {
        return $this->hasMany(Revision::class);
    }

    public function finalLinks()
    {
        return $this->hasMany(ProjectFinalLink::class);
    }

    public function finalFiles()
    {
        return $this->hasMany(ProjectFinalFile::class);
    }

    public static function getActiveRevision($project_id)
    {
        try {
            $revision = Revision::where('project_id', $project_id)->orderBy('version', 'desc')->firstOrFail();
            return $revision;
        } catch (Illuminate\Database\QueryException $e) {
            return $e->getMessage();
        }
    }

    public static function getRevisionVersions($project_id)
    {
        try {
            $project = Project::findOrFail($project_id);
            $revisions = $project->revisions()->orderBy('created_at', 'desc')->get();
            return $revisions;
        } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $e->getMessage();
        }
    }

    public function unreadComments()
    {
        return $this->hasMany(UnreadComment::class, 'project_id');
    }

}
