<?php

namespace App\Http\Controllers\API\V1;


use App\Contracts\API\V1\IAPIProjectService;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\ErrorResource;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Resources\API\V1\UserResource;

/**
 * Class AuthController
 * Login by API request
 * @package App\Http\Controllers\API\V1
 */
class AuthController extends Controller
{
    use AuthenticatesUsers;

    protected $apiService;

    public function __construct(IAPIProjectService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Login user with email and password
     * @param Request $request
     * @return UserResource|\Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        //Correct email and password
        if ($this->guard()->validate($this->credentials($request))) {
            $user = $this->guard()->getLastAttempted();

            if (!$user->token) {
                try {
                    $user = $this->apiService->createToken($user);
                } catch (\Exception $e) {
                    report($e);

                    $response = [
                        'status' => 'Error: API exception error occurred',
                        'code' => 400,
                        'message' => $e->getMessage(),
                    ];

                    return new ErrorResource((object) $response);
                }
            }

            return new UserResource($user);
        }

        //Invalid email or password
        return $this->sendFailedLoginResponse();
    }

    /**
     * Send failed login response.
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendFailedLoginResponse()
    {
        $response = [
            'status' => 'Validation Error',
            'code' => 422,
            'message' => 'Invalid email or password'
        ];

        return new ErrorResource((object) $response);
    }
}