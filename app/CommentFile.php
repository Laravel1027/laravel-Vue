<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentFile extends Model
{
    protected $fillable = [
        'comment_id',
        'path',
        'thumb_path',
        'user_id',
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
