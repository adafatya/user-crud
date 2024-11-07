<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

function badRequestResponse($message)
{
    return response()->json([
        'status' => 400,
        'message' => $message,
        'data' => [],
    ], 400);
}

class UserController extends Controller
{
    public function getUsersData(Request $request)
    {
        $limit = $request->query('limit');
        $page = $request->query('page');

        if ($limit == null) {
            return badRequestResponse("Limit is required!");
        }

        if ($limit < 1) {
            return badRequestResponse("Limit must be positive!");
        }

        if ($page == null) {
            return badRequestResponse("Page is required!");
        }

        if ($page < 1) {
            return badRequestResponse("Page must be positive!");
        }

        $usersCache = Redis::get('users:'.$limit.':'.$page);
        if ($usersCache != null) {
            return response()->json([
                'status' => 200,
                'message' => "Successfully get data from cache!",
                'data' => json_decode($usersCache),
            ]);
        }

        $users = User::with(['membershipStatus' => function ($query) {
            $query->select('id', 'name');
        }])->paginate($limit);

        Redis::set('users:'.$limit.':'.$page, json_encode($users));
        
        return response()->json([
            'status' => 200,
            'message' => "Successfully get data!",
            'data' => $users,
        ]);
    }

    public function addUser(Request $request)
    {
        $email = $request->input('email');
        $name = $request->input('name');
        $password = $request->input('password');
        $age = $request->input('age');
        $membershipStatusId = $request->input('membership_status_id');

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
            'name' => 'required|string',
            'age' => 'required|integer|min:18',
            'membership_status_id' => 'required'
        ]);

        if ($validator->fails()) {
            return badRequestResponse($validator->errors());
        }

        // Membership status validation
        $membershipStatusIds = DB::table('membership_statuses')->pluck('id')->toArray()    ;
        if (!in_array($membershipStatusId, $membershipStatusIds)) {
            return badRequestResponse("Membership status is invalid");
        }

        $user = new User;
        
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->name = $name;
        $user->age = $age;
        $user->membership_status_id = $membershipStatusId;
        $user->role = "user";

        $user->save();

        return response()->json([
            'status' => 201,
            'message' => 'Successfully saved data!',
            'data' => $user
        ], 201);
    }

    public function getUserDataById(string $id)
    {
        $userId = intval($id);
        $user = User::where('id', $userId)->first();

        return response()->json([
            'status' => 200,
            'message' => "Succesfully get data!",
            'data' => $user,
        ]);
    }

    public function updateUser(Request $request, string $id)
    {
        $email = $request->input('email');
        $name = $request->input('name');
        $password = $request->input('password');
        $age = $request->input('age');
        $membershipStatusId = $request->input('membership_status_id');

        $userId = intval($id);
        $user = User::where('id', $userId)->first();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'name' => 'required|string',
            'age' => 'required|integer|min:18',
            'membership_status_id' => 'required'
        ]);

        if ($validator->fails()) {
            return badRequestResponse($validator->errors());
        }

        // Membership status validation
        $membershipStatusIds = DB::table('membership_statuses')->pluck('id')->toArray()    ;
        if (!in_array($membershipStatusId, $membershipStatusIds)) {
            return badRequestResponse("Membership status is invalid");
        }
        
        if ($email != $user->email) {
            $validator = Validator::make($request->all(), [
                'email' => 'unique:users',
            ]);
    
            if ($validator->fails()) {
                return badRequestResponse("Email already registered!");
            }
        }
        
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->name = $name;
        $user->age = $age;
        $user->membership_status_id = $membershipStatusId;

        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'Successfully saved data!',
            'data' => $user,
        ]);
    }

    public function deleteUserById(string $id)
    {
        $userId = intval($id);
        User::destroy($userId);

        return response()->json([
            'status' => 200,
            'message' => 'Successfully deleted data!',
            'data' => [],
        ]);
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return badRequestResponse($validator->errors());
        }

        $user = User::where('email', $email)->first();
        if ($user == null) {
            return badRequestResponse("Email not found!");
        }

        if (!Hash::check($password, $user->password)) {
            return badRequestResponse("Wrong password!");
        }

        $token = auth()->tokenById($user->id);

        return response()->json([
            'status' => 200,
            'message' => "Successfully logged in!",
            'data' => [
                'token' => $token,
                'token_type' => 'bearer'
            ],
        ]);
    }
}
