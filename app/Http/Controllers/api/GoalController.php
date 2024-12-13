<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{

    public function getAllGoals(Request $request)
    {
        $user = $request->user();

        $goals = Goal::with('progress')->where('user_id', $user->id)->get();

        if (!$goals) {
            return response([
                'status' => false,
                'message' => 'No Goals Found',
            ], 401);
        }

        return response([
            'status' => true,
            'message' => 'Goals data retrieved successfully.',
            'data' => $goals,
        ], 200);
    }

    public function setGoal(Request $request)
    {
        $user = $request->user();

        $goal = new Goal();
        $goal->user_id = $user->id;
        $goal->title = $request->title;
        $goal->description = $request->description;
        $goal->start_date = $request->start_date;
        $goal->end_date = $request->end_date;
        $goal->start_time = $request->start_time;
        $goal->end_time = $request->end_time;
        $goal->status = 'pending';
        $goal->save();

        return response([
            'status' => true,
            'message' => "Goal Created Successfully",
            'Data' => $goal,
        ]);
    }

    public function updateGoal(Request $request, $goal_id)
    {
        $validated = $request->validate([
            'goal_title' => 'required|string|max:255',
            'goal_description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $goal = Goal::find($goal_id);

        if (!$goal) {
            return response([
                'status' => true,
                'message' => 'Goal not found.',
            ], 400);
        }

        $goal->title = $request->goal_title;
        $goal->description = $request->goal_description;
        $goal->start_date = $request->start_date;
        $goal->end_date = $request->end_date;
        $goal->start_time = $request->start_time;
        $goal->end_time = $request->end_time;
        // $goal->status = 'pending';
        $goal->save();

        return response([
            'status' => true,
            'message' => 'Goal Data Updated successfully.',
            'data' => $goal,
        ], 200);
    }


    public function deleteGoal($goal_id)
    {
        $goal = Goal::find($goal_id);

        if (!$goal) {
            return response([
                'status' => false,
                'message' => 'Goal not found!',
            ], 400);
        }

        $goal->delete();

        return response([
            'status' => true,
            'message' => 'Goal deleted successfully.',
        ], 200);
    }

}
