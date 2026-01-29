<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserListResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::with('roles')
            ->filter($request->only(['active', 'search']), ['first_name', 'last_name', 'email'])
            ->sort([$request->input('sort_field', 'created_at') => $request->input('sort_direction', 'desc')])
            ->paginateResults($request->input('per_page', 10));

        $totalActive = User::where('active', true)->count();
        $totalInactive = User::where('active', false)->count();

        return UserListResource::collection($users)
            ->additional([
                'meta' => [
                    'total_active_users' => $totalActive,
                    'total_inactive_users' => $totalInactive,
                ],
            ])
            ->response();
    }

    public function show(User $user): JsonResponse
    {
        return (new UserResource($user->load('addresses', 'orders')))->response();
    }

    public function ban(User $user): JsonResponse
    {
        return $this->updateUserStatus($user, false);
    }

    public function unban(User $user): JsonResponse
    {
        return $this->updateUserStatus($user, true);
    }

    private function updateUserStatus(User $user, bool $isActive): JsonResponse
    {
        $user->update(['is_active' => $isActive]);
        return (new UserResource($user->load('addresses', 'orders')))->response();
    }
}
