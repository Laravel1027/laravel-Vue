<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Revision extends Model
{
    protected $fillable = ['version', 'project_id', 'status_revision'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function proofs()
    {
        return $this->hasMany(Proof::class)->with('projectFiles');
    }

    public static function store($data)
    {
        if ($data['project']->id > 0) {
            $revision = new Revision();
            $revision->version = $revision->setCurrentVersion($data['project']->id);
            $revision->status_revision = 'draft';
            $revision->project()->associate($data['project']);
            $revision->save();

            return $revision;
        }
    }

    /**
     * Remove last created revision
     * @param $project_id
     * @return mixed
     */
    public static function remove($project_id) {
        if ($project_id > 0) {
            try {
                $revision = Revision::getLastRevision($project_id);
                return $revision->delete();
            } catch (\Exception $e) {
                $e->getMessage();
            }
        }
    }

    public function setCurrentVersion($project_id)
    {
        $last_version = Revision::where('project_id', $project_id)->max('version');
        $last_version = (int)$last_version;
        $last_version += 1;
        return $last_version = (string)$last_version;
    }

    public static function getRevisionById($revision_id)
    {
        $revision = Revision::with('proofs.issues.comments', 'proofs.issues.unreadComments')->find($revision_id);
        return $revision;
    }

    public static function getLastRevision($project_id)
    {
        $revision = Revision::where('project_id', $project_id)->with('proofs.issues.comments')
            ->where('status_revision', '!=', 'approved')
            ->orderBy('version', 'desc')
            ->first();
        if ($revision != null) {
            return $revision;
        } else {
            $revision = Revision::where('project_id', $project_id)
                ->with('proofs.issues.comments')
                ->orderBy('created_at', 'desc')->first();
            return $revision;
        }
    }

    public static function checkOpenRevision($project_id)
    {
        $revision = Revision::where('project_id', $project_id)->where('status_revision', '=', 'approved')->first();
        return $revision;
    }

    public function unreadComments()
    {
        return $this->hasMany(UnreadComment::class, 'revision_id');
    }

}
