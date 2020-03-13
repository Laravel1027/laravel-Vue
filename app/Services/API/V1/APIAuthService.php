<?php

namespace App\Services\API\V1;


use App\APIToken;
use App\Contracts\API\V1\IAPIAuthService;
use App\User;
use Ramsey\Uuid\Uuid;

class APIAuthService implements IAPIAuthService
{
    protected $userModel;
    protected $tokenModel;

    public function __construct(User $userModel, APIToken $tokenModel)
    {
        $this->userModel = $userModel;
        $this->tokenModel = $tokenModel;
    }

    /**
     * Get user by ID
     * @param $id
     * @return mixed
     */
    public function getUserById($id)
    {
        $user = $this->userModel->findOrFail($id);

        if (!$user->token) {
            return $this->createToken($user);
        }

        return $user;
    }

    /**
     * Create new token for user
     * @param $user
     * @return mixed
     */
    public function createToken($user)
    {
        do {
            $token = str_random(60);
        } while ($this->tokenModel->where("token", "=", $token)->first() instanceof $this->tokenModel);

        $user->token()->create([
            'id' => Uuid::uuid4(),
            'user_id' => $user->id,
            'name' => 'Extension Token',
            'token' => $token,
            'metadata' => '',
            'transient' => false,
            'expires_at' => null,
        ]);

        return $user->load('token');
    }

    /**
     * Get user by token
     * @param $token
     * @return mixed|null
     */
    public function getUserByToken($token)
    {
        $userToken = $this->tokenModel->where('token', $token)->first();

        if ($userToken) {
            return $userToken->user;
        }

        return null;
    }
}