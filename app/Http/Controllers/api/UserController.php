<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        $user = $request->user(); // Or $request->user()

        $userData = User::with('userDetail')->find($user->id);  

        if (!$user) {
            return response([
                'status' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        return response([
            'status' => true,
            'message' => 'User data retrieved successfully.',
            'data' => $userData,
        ], 200);
    }


    public function setProfile(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            // 'first_name' => 'string|max:255',
            // 'last_name' => 'string|max:255',
            // 'email' => 'email',
            // 'username' => 'string|max:255',
            'profile_pic' => 'image',
            'mobile_number' => 'required|string', // Add validation for mobile_number if necessary
            'short_detail' => 'string|nullable',
            'long_detail' => 'string|nullable',
        ]);

        \Log::info($request->all());

        try {
            // Create new user
            // $user = User::create([
            //     'first_name' => $request->first_name,
            //     'last_name' => $request->last_name,
            //     'email' => $request->email,
            //     'username' => $request->username,
            // ]);

            $user = $request->user();

            // Prepare to store user detail
            $userDetail = new UserDetail(); // Assuming you have a UserDetail model
            $userDetail->user_id = $user->id; // Assuming user_id is the foreign key in user_detail
            $userDetail->mobile_number = $request->mobile_number;
            $userDetail->short_detail = $request->short_detail;
            $userDetail->long_detail = $request->long_detail;

            $folder_path = "uploads/{$user->folder_name}";

            if ($request->hasFile('profile_pic')) {
                if ($userDetail->profile_image_url != '') {
                    Storage::disk('public')->delete($userDetail->profile_image_url);
                }

                $file = $request->file('profile_pic');
                $filename = time() . "_" . $file->getClientOriginalName();
                $filepath = $file->storeAs("{$folder_path}/profile_image", $filename, 'public');

                $userDetail->profile_image_url = $filepath;
            }

            // Save user details
            $userDetail->save();

            // Load user details
            $data = $user->load('userDetail');

            return response([
                'status' => true,
                'message' => 'Profile created successfully.',
                'data' => $data,
            ], 201); // 201 Created

        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => "Something went wrong!",
                'error' => $e->getMessage(),
            ], 400);
        }
    }


    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'profile_pic' => 'image',
            'mobile_number' => 'string', // Add validation for mobile_number if necessary
            'short_detail' => 'string|nullable',
            'long_detail' => 'string|nullable',
        ]);

        try {
            $user = $request->user();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->save();

            $user_detail = $user->userDetail;
            $folder_path = "uploads/{$user->folder_name}";

            if ($request->hasFile('profile_pic')) {
                if ($user_detail->profile_image_url != '') {
                    Storage::disk('public')->delete($user_detail->profile_image_url);
                }

                $file = $request->file('profile_pic');
                $filename = time() . "_" . $file->getClientOriginalName();
                $filepath = $file->storeAs("{$folder_path}/profile_image", $filename, 'public');

                $user_detail->profile_image_url = $filepath;
            }

            if (!$user_detail) {
                return response([
                    'status' => false,
                    'message' => 'User detail not found.',
                ], 404);
            }

            $user_detail->mobile_number = $request->mobile_number;
            $user_detail->short_detail = $request->short_detail;
            $user_detail->long_detail = $request->long_detail;
            $user_detail->save();

            $data = $user->load('userDetail');

            // if (!$user_detail->save()) {
            //     return response([
            //         'status' => false,
            //         'message' => 'Something went wrong!',
            //         'data' => $data
            //     ], 400);
            // }

            return response([
                'status' => true,
                'message' => 'Success, Profile Updated Successfully.',
                'data' => $data,
            ], 200);

        } catch (Exception $e) {
            return response([
                'status' => false,
                'message' => "Something went wrong, while updating the profile",
                'data' => $e
            ], 400);
        }
    }
}
