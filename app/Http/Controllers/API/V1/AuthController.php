<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\HttpBadRequestException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\QueryException;
use App\Models\User;
use App\Models\PasswordReset;
use Carbon\Carbon;
use Exception;
use Validator;
use JWTAuth;
use Auth;
use Mail;
use Log;
use DB;

class AuthController extends Controller
{
    /**
     * Signs up a new user
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'firstName'     => 'required|max:255',
                'lastName'      => 'required|max:255',
                'email'         => 'required|email|max:255|unique:users',
                'password'      => 'required',
                'conf_password' => 'required|same:password'
            ]);

            if ($validator->fails()){

                $response = [
                    'status'    => false,
                    'message'   => implode(',',$validator->messages()->all())
                ];
                $responseCode = config('response.codes.200');

            } else {

                DB::beginTransaction();
                $this->createuserInstance($request);
                $response       = $this->loginAttempt($request);
                $responseCode   = (array_key_exists('status',$response) && $response['status']) ? config('response.codes.200') : config('response.codes.422');
            }

        } catch (HttpBadRequestException $httpBadRequestException) {

            $response = [
                'status'            => false,
                'error'             => config('response.messages.400'),
                'message'           => $httpBadRequestException->getMessage()
            ];
            $responseCode   = config('response.codes.400');

        } catch (QueryException $queryException) {

            $response = [
                'status'        => false,
                'error'         => config('response.messages.500'),
                'message'       => $queryException->getMessage()
            ];
            $responseCode = config('response.codes.500');

        } catch (ModelNotFoundException $modelNotFoundException) {

            $response = [
                'status'            => false,
                'error'             => config('response.messages.404'),
                'message'           => $modelNotFoundException->getMessage()
            ];
            $responseCode = config('response.codes.404');

        } catch (Exception $exception) {

            DB::rollBack();
            Log::error($exception->getMessage());
            $response = [
                'status'        => false,
                'error'         => config('response.messages.500'),
                'message'       => $exception->getMessage()
            ];
            $responseCode = config('response.codes.500');

        } finally {

            DB::commit();

        }
        return response()->json($response, $responseCode);
    }

    /**
     * Signs in an existing user
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'email'         => 'required|email|max:255',
                'password'      => 'required'
            ]);

            if ($validator->fails()){

                $response = [
                    'status'    => false,
                    'message'   => implode(',',$validator->messages()->all())
                ];
                $responseCode = config('response.codes.200');

            } else {
                $response       = $this->loginAttempt($request);
                $responseCode   = (array_key_exists('status',$response) && $response['status']) ? config('response.codes.200') : config('response.codes.422');
            }

        } catch (HttpBadRequestException $httpBadRequestException) {

            $response = [
                'status'            => false,
                'error'             => config('response.messages.400'),
                'message'           => $httpBadRequestException->getMessage()
            ];
            $responseCode = config('response.codes.400');

        } catch (ModelNotFoundException $modelNotFoundException) {

            $response = [
                'status'            => false,
                'error'             => config('response.messages.404'),
                'message'           => $modelNotFoundException->getMessage()
            ];
            $responseCode = config('response.codes.404');

        }  catch (Exception $exception) {

            Log::error($exception->getMessage());
            $response = [
                'status'        => false,
                'error'         => config('response.messages.500'),
                'message'       => $exception->getMessage()
            ];
            $responseCode = config('response.codes.500');
        }
        return response()->json($response, $responseCode);
    }

    /**
     * Signs out a existing user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            /*
             * When user request for signing out, invalidate JWT token
             */
            JWTAuth::invalidate(JWTAuth::getToken());

            $response = [
                'status'    => true,
                'message'   => trans('messages.success.response.logout_success')
            ];
            $responseCode = config('response.codes.200');

        } catch (JWTException $JWTException) {

            $response = [
                'status'        => false,
                'error'         => trans('messages.errors.jwt.token_mismatch'),
                'message'       => $JWTException->getMessage()
            ];
            $responseCode = config('response.codes.401');

        } catch (Exception $exception) {

            Log::error($exception->getMessage());
            $response = [
                'status'        => false,
                'error'         => config('response.messages.500'),
                'message'       => $exception->getMessage()
            ];
            $responseCode = config('response.codes.500');
        }

        return response()->json($response, $responseCode);

    }

    /**
     * Forget password generate url
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forget_password(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'email'    => 'required|email|max:255'
            ]);

            if ($validator->fails()){

                $response = [
                    'status'    => false,
                    'message'   => implode(',',$validator->messages()->all())
                ];
                $responseCode = config('response.codes.200');

            } else {

                $user                   = User::where('email', $request->email)->firstOrFail();
                $token                  = str_random(64);
                DB::beginTransaction();
                $reset                  = new PasswordReset();
                $reset->email           = $user->email;
                $reset->token           = str_random(64);
                $reset->created_at      = Carbon::now();
                $reset->save();

                Mail::send('emails.reset-password', [
                    'firstName' => $user->first_name,
                    'lastName' => $user->last_name,
                    'url' => url('/') . '/reset-password/' . $user->email . '/' . $token
                ], function ($mail) use ($user) {

                    $mail->from('chatvago@tier5.us', trans('messages.email.app_name'));
                    $mail->to($user->email, trans('messages.email.app_name'))
                        ->subject(trans('messages.email.subject'));

                });
                $response = [
                    'status'    => true,
                    'message'   => trans('messages.success.response.forget_password.email_sent'),
                ];
                $responseCode = config('response.codes.200');

            }

        } catch (HttpBadRequestException $httpBadRequestException) {

            $response = [
                'status'            => false,
                'error'             => config('response.messages.400'),
                'message'           => $httpBadRequestException->getMessage()
            ];
            $responseCode = config('response.codes.400');

        } catch (ModelNotFoundException $modelNotFoundException) {

            $response = [
                'status'        => false,
                'error'         => config('response.messages.404'),
                'message'       => trans('messages.errors.response.forget_password.user_not_found')
            ];
            $responseCode = config('response.codes.404');

        } catch (Exception $exception) {

            DB::rollBack();
            Log::error($exception->getMessage());
            $response = [
                'status'        => false,
                'error'         => config('response.messages.500'),
                'message'       => $exception->getMessage()
            ];
            $responseCode = config('response.codes.500');

        } finally {

            DB::commit();

        }
        return response()->json($response, $responseCode);
    }
    /**
     * Reset Password if user Successfully fill up his reset password form
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset_password(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'token'         => 'required',
                'email'         => 'required|email|max:255',
                'password'      => 'required',
                'conf_password' => 'required|same:password'
            ]);

            if ($validator->fails()){

                $response = [
                    'status'    => false,
                    'message'   => implode(',',$validator->messages()->all())
                ];
                $responseCode = config('response.codes.200');

            } else {

                $resetRequest = PasswordReset::whereToken($request->input('token'))->firstOrFail();
                if (strtotime($resetRequest->created_at) > strtotime("-30 minutes")) {
                    $user           = User::whereEmail($request->input('email'))->firstOrFail();
                    $user->password = $request->input('password');
                    $user->update();
                    $otherResetRequests = PasswordReset::whereEmail($user->email)->get();
                    if (count($otherResetRequests)) {
                        foreach ($otherResetRequests as $otherResetRequest) {
                            $otherResetRequest->delete();
                        }
                    }
                    $response       = $this->loginAttempt($request);
                    $responseCode   = (array_key_exists('status',$response) && $response['status']) ? config('response.codes.200') : config('response.codes.422');
                } else {
                    $response = [
                        'status'    => false,
                        'error'     => trans('messages.errors.response.reset_password.token_expired')
                    ];
                    $responseCode = config('response.codes.401');
                }
            }

        } catch (HttpBadRequestException $httpBadRequestException) {

            $response = [
                'status'        =>  false,
                'error'         =>  config('response.messages.400'),
                'message'       =>  $httpBadRequestException->getMessage()
            ];
            $responseCode = config('response.codes.400');

        } catch (ModelNotFoundException $modelNotFoundException) {

            $response = [
                'status'        =>  false,
                'error'         =>  config('response.messages.400'),
                'message'       =>  preg_match('/(PasswordReset)/', $modelNotFoundException->getMessage()) ? "Reset password request not found." : "User not found."
            ];
            $responseCode = config('response.codes.404');

        } catch (Exception $exception) {

            Log::error($exception->getMessage());
            $response = [
                'status'        => false,
                'error'         => config('response.messages.500'),
                'message'       => $exception->getMessage()
            ];
            $responseCode = config('response.codes.500');
        }
        return response()->json($response, $responseCode);
    }

    /**
     * Creates a user instance
     * @param $request
     * @return User
     */
    protected function createuserInstance($request) {
        $user                   = new User();
        $user->first_name       = $request->firstName;
        $user->last_name        = $request->lastName;
        $user->email            = $request->email;
        $user->password         = $request->password;
        $user->save();
    }

    /**
     * Attempts a user login
     * @param $request
     * @param $user
     * @return array
     */
    public function loginAttempt($request) {
        $credentials = $request->only('email', 'password');
        if ($token = JWTAuth::attempt($credentials)) {
            $user = Auth::user();
            $user_details   =   [
                'token'         =>  $token,
                'id'            =>  $user->id,
                'first_name'    =>  $user->first_name,
                'last_name'     =>  $user->last_name,
                'email'         =>  $user->email
            ];
            return $response = [
                'status'    => true,
                'message'   => 'Welcome ' . $user->first_name . ' ' . $user->last_name . ".",
                'data'      => $user_details
            ];
        } else {
            return $response = [
                'status'        =>  false,
                'error'         =>  config('response.messages.422'),
                'message'       =>  trans('messages.errors.response.invalid_user')
            ];
        }
    }
}
