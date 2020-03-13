<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Issue extends Model
{
    protected $fillable = ['title', 'user_id', 'proof_id', 'project_files_id', 'group'];

    protected $appends = ['user'];
   
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proof()
    {
        return $this->belongsTo(Proof::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->with('files')->with('user');
    }

    public function files()
    {
        return $this->hasMany(IssueFile::class);
    }

    public function getUserAttribute()
    {
        return $this->user();
    }

    public function unreadComments()
    {
        return $this->hasMany(UnreadComment::class, 'issue_id')->where('user_id', '=', Auth::user()->id);
    }

}
