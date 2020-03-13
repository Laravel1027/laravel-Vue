<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * API Resource for user
 * @package App\Http\Resources\API\V1
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'thumbnail' => $this->photo_url,
            'token' => $this->token->token,
        ];
    }
}
