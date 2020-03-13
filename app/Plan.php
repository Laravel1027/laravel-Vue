<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'stripe_plan_id',
        'stripe_product_id',
        'name',
        'description',
        'price',
        'teams_count',
        'teams_members_count',
    ];
}
