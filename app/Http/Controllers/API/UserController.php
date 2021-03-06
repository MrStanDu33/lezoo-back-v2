<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Count resource number.
     *
     * @return \Illuminate\Http\Response
     */
    public function count() {
        try {
            return response(['count' => User::all()->count()], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function find_all() {
        try {
            $page = filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT);
            $range = filter_input(INPUT_GET, "range", FILTER_SANITIZE_NUMBER_INT);

            $start_id = $range * $page;
            $end_id = $start_id + $range + 1;

            $users = (is_null($page) || is_null($range))
                ? User::all()
                : User::where('id', '>', $start_id)->where('id', '<', $end_id)->get();

            return response([ 'users' => UserResource::collection($users)], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:55',
                'email' => 'email|required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            $user_exists = User::where('email', $request->get('email'))->get()->count();

            if ($user_exists !== 0) {
                return response(['message' => 'Email address already used.'], 409);
            }

            $hashed_password = Hash::make($request->get('password'));

            $user = User::create([
                ...$request->all(),
                'password' => $hashed_password,
            ]);

            return response(['user' => $user], 201);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:55',
                'email' => 'email|required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            $user_exists = User::where('email', $request->get('email'))->get()->count();

            if ($user_exists !== 0) {
                return response(['message' => 'Email address already used.'], 409);
            }

            $hashed_password = Hash::make($request->get('password'));

            $user = User::create([
                ...$request->all(),
                'password' => $hashed_password,
            ]);

            auth()->attempt([
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $access_token = auth()->user()->createToken('authToken')->accessToken;

            return response([
                'token' => $access_token,
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    public function login(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'email|required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            $user_exists = User::where('email', $request->get('email'))->get()->count();

            if ($user_exists !== 1) {
                return response(['message' => 'No user match this email'], 404);
            }

            if (!auth()->attempt($request->all())) {
                return response(['message' => 'Email or password incorrect'], 401);
            }

            $token = auth()->user()->createToken('authToken')->accessToken;

            return response(['user' => auth()->user(), 'token' => $token], 201);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    public function logout(Request $request) {
        try {
            $request->user()->token()->revoke();
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function find_one($user_id) {
        try {
            $user = User::find($user_id);

            if ($user === null) {
                return response(['message' => 'User not found'], 404);
            }

            return response(['user' => new UserResource($user), 'message' => 'Retrieved successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id) {
        try {
            $user = User::find($user_id);

            if ($user === null) {
                return response(['message' => 'User not found'], 404);
            }
            $data = $request->all();

            $validator = Validator::make($data, [
                'name' => 'max:55',
                'email' => 'email',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            $users_with_asked_email = User::where('email', $request->get('email'))->get()->count();

            if ($users_with_asked_email !== 0) {
                return response(['message' => 'Email address already used.'], 409);
            }

            $user->update($data);

            return response(['user' => new UserResource($user), 'message' => 'Update successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function delete($user_id) {
        try {
            $user = User::find($user_id);

            if ($user === null) {
                return response(['message' => 'User not found'], 404);
            }
            $user->delete();

            return response(['user' => $user]);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Send a mail to reset password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function forgot_password(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'email' => 'email|required',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $user = User::where('email', $data['email']);

        if (!$user) {
            return response(
                ['message' => 'Given email is not linked to any users'],
                404
            );
        }

        $token = \Str::random(32);
        try {
            \DB::table('password_resets')->insert([
                'email' => $data['email'],
                'token' => $token
            ]);

            \Mail::send('emails.forgotPassword', ['token' => $token], function ($message) use ($data) {
                $message->to($data['email']);
                $message->subject('Reset your password !');
            });

            return response(['message' => 'email sent'], 201);
        } catch (\Exception $exception) {
            return response(
                ['message' => $exception->getMessage()],
                400
            );
        }
    }

    /**
     * Reset user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function reset_password(Request $request) {

        $data = $request->all();

        $validator = Validator::make($data, [
            'token' => 'string|required|len:32',
            'password' => 'required|confirmed'
        ]);

        if (!$passwordResets = \DB::table('password_resets')->where('token', $data['token'])->first()) {
            return response(
                ['message' => 'Invalid token'],
                400
            );
        }

        $user = User::where('email', $passwordResets->email)->first();
        $user->password = Hash::make($data['password']);
        $user->save();

        \DB::table('password_resets')->where('token', $data['token'])->delete();

        return response(
            ['message' => 'Password updated successfully'],
            200
        );
    }

    /**
     * Find my user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function me (Request $request) {
        try {
            $user_id = $request->user()->id;

            $user = User::where('id', $user_id)->first();

            if ($user === null) {
                return response(['error' => 'User not found.'], 404);
            }

            return ['user' => $user];
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }
}
