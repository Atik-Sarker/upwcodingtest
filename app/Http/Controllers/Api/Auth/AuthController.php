<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Use ApiResponse Trait in this repository
    use ApiResponse;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {

        $validator = Validator::make(request(['email', 'password']), [
            'password' => 'required',
            'email' => ['required', 'email', Rule::exists('users')->where('isActive', true)]
        ],
            $this->validationError()
        );
        if ($validator->fails()) {
            $response = array("status" => 203, "errors" => $validator->errors(), "data" =>  array('email' =>  request('email')));
            $this->errors(203, $response);
        }
        $credentials = request(['email', 'password']);
        if (! $token = auth('api')->attempt($credentials)) {
            $response = array("errors" => trans('auth.failed'), "data" => array('email' =>  request('email')));
            return $this->errors(203, $response);
        }
        $data = $this->respondWithToken($token);
        return $this->success(200,'login success', true, $data->original);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->guard('api')->user());
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard('api')->logout();

        return $this->success(200,'Successfully logged out', true);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'name' => $this->guard()->user()->name,
            'email' => $this->guard()->user()->email,
            'role' => $this->guard()->user()->user_role,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }


    /*
     *
     * custom  error message for login validation
     */
    protected function validationError(){
        return
            [
                'email.exists' => 'Please Activate your account!'
            ];
    }
}