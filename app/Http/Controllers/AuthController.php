<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\Project;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }

    public function authenticateByUserId(Request $request)
    {
       /*  try { */
        $project_hash = $request->input('project_hash');
        if ($project_hash) {
            $project = Project::where('project_hash', $project_hash)->first();
            if ($project) {
                if (Auth::check()) {
                    $this->guard()->logout();
                    session()->flush();
                }

                return Auth::loginUsingId($project->client_id);
            }
        }

           
            // attempt to authenticate using the user object
           /*  if (!$token = JWTAuth::fromUser($user_client)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            } */
        /* } catch (\Exception $e) { */
            // something went wrong whilst attempting to encode the token
           /*  return response()->json(['error' => 'could_not_create_token'], 500); */
     /*    } */

        // all good so return the token
        /* return response()->json(compact('token')); */
    }

    public function check()
    {
        if (Auth::check()) {
            return response(['authenticated' => true]);
        } else {
            return response(['authenticated' => false]);
        }

       /*  try {
            JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response(['authenticated' => false]);
        }

        return response(['authenticated' => true]); */
    }

    public function logout()
    {
        try {
            $token = JWTAuth::getToken();

            if ($token) {
                JWTAuth::invalidate($token);
            }
        } catch (JWTException $e) {
            return response()->json($e->getMessage(), 401);
        }

        return response()->json(['message' => 'Log out success'], 200);
    }

    public function getCurrentRole($project_id)
    {
        if ($project_id > 0) {
            $project = Project::find($project_id);
            if ($project) {
                foreach ($project->users as $key => $user) {
                    if ($user->id == Auth::user()->id) {
                        $result['user_role'] = $user->pivot->role;
                        $result['user_id'] = $user->id;
                        return ApiResponse::success('', $result);
                    }
                }
            }
        }
        return ApiResponse::error('001', 'User role not found');
    }
}
