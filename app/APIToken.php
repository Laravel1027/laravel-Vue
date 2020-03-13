<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class APIToken extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'api_tokens';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the user that the token belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
