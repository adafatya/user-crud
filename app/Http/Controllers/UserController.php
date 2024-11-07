<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

function badRequestResponse(string $message)
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
        if ($limit == null || $limit <= 0) {
            $limit = 20;
        }

        $users = User::paginate($limit);
        
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

        // Email validation
        if ($email == null || $email == "") {
            return badRequestResponse("Email is required!");
        }

        $pattern = "/^[\w\.-]+@([\w\-]+\.)+[a-zA-Z]{2,4}$/";
        if (!preg_match($pattern, $email)) {
            return badRequestResponse("Email is invalid!");
        }

        $emails = DB::table('users')->pluck('email')->toArray();
        if (in_array($email, $emails)) {
            return badRequestResponse("Email already registered!");
        }

        // Password validation
        if ($password == null || $password == "") {
            return badRequestResponse("Password is required");
        }

        if (gettype($password) != "string") {
            return badRequestResponse("Password is invalid");
        }
        
        // Name validation
        if ($name == null || $name == "") {
            return badRequestResponse("Name is required");
        }

        if (gettype($name) != "string") {
            return badRequestResponse("Name is invalid");
        }

        // Age validation
        if ($age == null) {
            return badRequestResponse("Age is required");
        }

        if (gettype($age) != "integer") {
            return badRequestResponse("Age is invalid");
        }

        if ($age < 18) {
            return badRequestResponse("Minimum age is 18!");
        }

        // Membership status validation
        if ($membershipStatusId == null) {
            return badRequestResponse("Membership status is required!");
        }

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

        // Email validation
        if ($email == null || $email == "") {
            return badRequestResponse("Email is required!");
        }

        $pattern = "/^[\w\.-]+@([\w\-]+\.)+[a-zA-Z]{2,4}$/";
        if (!preg_match($pattern, $email)) {
            return badRequestResponse("Email is invalid!");
        }

        $emails = DB::table('users')->pluck('email')->toArray();
        if ($email != $user->email && in_array($email, $emails)) {
            return badRequestResponse("Email already registered!");
        }

        // Password validation
        if ($password == null || $password == "") {
            return badRequestResponse("Password is required");
        }

        if (gettype($password) != "string") {
            return badRequestResponse("Password is invalid");
        }
        
        // Name validation
        if ($name == null || $name == "") {
            return badRequestResponse("Name is required");
        }

        if (gettype($name) != "string") {
            return badRequestResponse("Name is invalid");
        }

        // Age validation
        if ($age == null) {
            return badRequestResponse("Age is required");
        }

        if (gettype($age) != "integer") {
            return badRequestResponse("Age is invalid");
        }

        if ($age < 18) {
            return badRequestResponse("Minimum age is 18!");
        }

        // Membership status validation
        if ($membershipStatusId == null) {
            return badRequestResponse("Membership status is required!");
        }

        $membershipStatusIds = DB::table('membership_statuses')->pluck('id')->toArray()    ;
        if (!in_array($membershipStatusId, $membershipStatusIds)) {
            return badRequestResponse("Membership status is invalid");
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
}
