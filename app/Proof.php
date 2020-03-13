<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Issue;
use Illuminate\Support\Facades\Auth;

class Proof extends Model
{
    protected $fillable = ['canvas_data', 'revision_id', 'project_files_id', 'status'];

    public function revision()
    {
        return $this->belongsTo(Revision::class);
    }

    public function projectFiles()
    {
        return $this->belongsTo(ProjectFile::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class)->with('files')->with('user');
    }

    public static function store($project_file)
    {
        try {
            $proof = new Proof();
            $proof->status = 'created';
            $proof->projectFiles()->associate($project_file);
            $proof->revision()->associate($project_file->revision);
            $proof->save();
            return $proof;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getByRevisionId($revision_id)
    {
        try {
            $proofs = Proof::where('revision_id', $revision_id)->get();
            return $proofs;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getByRevisionVersion($version)
    {
        try {
            $proofs = Proof::where('revision_id', $version)->get();
            return $proofs;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function unreadComments()
    {
        return $this->hasMany(UnreadComment::class, 'proof_id')->where('user_id', '=', Auth::user()->id);
    }


}
