<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Count resource number.
     *
     * @return \Illuminate\Http\Response
     */
    public function count() {
        return User::all()->count();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT);
        $range = filter_input(INPUT_GET, "range", FILTER_SANITIZE_NUMBER_INT);

        $start_id = $range * $page;
        $end_id = $start_id + $range + 1;

        $users = (is_null($page) || is_null($range)) ? User::all() : User::where('id', '>', $start_id)->where('id', '<', $end_id)->get();

        return response([ 'users' => UserResource::collection($users), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $validatedData['password'] = Hash::make($request->password);

        $user = User::create($validatedData);

        return response(['user' => $user], 201);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'This User does not exist, check your details'], 400);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
    }

    public function logout(Request $request) {
        $request->user()->token()->revoke();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response(['user' => new UserResource($user), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $user->update($data);

        return response(['user' => new UserResource($user), 'message' => 'Update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response(['message' => 'Deleted']);
    }

    /**
     * Send a mail to reset password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(Request $request)
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

    public function resetPassword(Request $request) {

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
}
