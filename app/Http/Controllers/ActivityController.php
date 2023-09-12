<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        $completedTasks = $user->tasks()->whereHas('activities', function ($query) {
            $query->where('type', 5);
        })
            ->with(['activities' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Activities/index', [
            'completedTasks' => fn () => $completedTasks,
        ]);
    }
}
