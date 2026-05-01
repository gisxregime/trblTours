<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardLikesController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->role === 'tourist', 403);

        $likedTours = $user->likedTours()
            ->with(['marketplaceGuide:id,name,full_name,role,status'])
            ->orderByPivot('created_at', 'desc')
            ->paginate(12);

        $likedTours->getCollection()->transform(function (Tour $tour): Tour {
            $tour->setAttribute('liked_by_current_user', true);

            return $tour;
        });

        return view('dashboards.likes', [
            'likedTours' => $likedTours,
        ]);
    }

    public function toggle(Request $request, Tour $tour): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->role === 'tourist', 403);

        $alreadyLiked = $user->likedTours()->whereKey($tour->id)->exists();
        $liked = ! $alreadyLiked;

        if ($alreadyLiked) {
            $user->likedTours()->detach($tour->id);
            $statusMessage = 'Tour removed from Likes.';
        } else {
            $user->likedTours()->syncWithoutDetaching([$tour->id]);
            $statusMessage = 'Tour added to Likes.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'tour_id' => $tour->id,
                'liked' => $liked,
                'message' => $statusMessage,
            ]);
        }

        $redirectTarget = (string) $request->input('redirect', 'back');

        if ($redirectTarget === 'likes') {
            return redirect()->route('dashboard.likes')->with('status', $statusMessage);
        }

        return back()->with('status', $statusMessage);
    }
}
