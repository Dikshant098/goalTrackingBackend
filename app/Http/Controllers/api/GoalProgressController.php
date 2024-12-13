<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\GoalProgress;
use Illuminate\Http\Request;

class GoalProgressController extends Controller
{
    public function updateProgress($goalId, Request $request) 
    {
        $validated = $request->validate([
            'progress' => 'required|numeric|min:0|max:100',
        ]);
    
        try {
            // Update or create the progress record
            $progress = GoalProgress::updateOrCreate(
                ['goal_id' => $goalId], // Match based on goal_id
                ['progress_percentage' => $validated['progress']] // Update progress_percentage
            );
    
            // Fetch the goal to update its status
            $goal = Goal::find($goalId);
    
            if ($goal) {
                $currentDate = now();
                $goalEndDate = new \DateTime($goal->end_date);
                $progressPercentage = (float) $validated['progress'];
    
                if ($progressPercentage === 100) {
                    $goal->status = 'completed';
                } elseif ($goalEndDate < $currentDate && $progressPercentage < 100) {
                    $goal->status = 'failed';
                } else {
                    $goal->status = $request->progressStatus ?? $goal->status; // Preserve or use provided status
                }
    
                $goal->save();
            }
    
            return response([
                'status' => true,
                'message' => 'Goal progress updated successfully',
                'data' => $goal,
            ], 200);
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    


    public function getProgress($goalId)
    {
        $progress = GoalProgress::where('goal_id', $goalId)->first();

        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => 'Progress not found for this goal',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $progress,
        ]);
    }

    public function deleteProgress($goalId)
    {
        $progress = GoalProgress::where('goal_id', $goalId)->first();

        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => 'Progress not found for this goal',
            ], 404);
        }

        $progress->delete();

        return response()->json([
            'success' => true,
            'message' => 'Goal progress deleted successfully',
        ]);
    }
}
