<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\User;

class MemberController extends Controller
{
    // 🔍 Search Member for Collection
    public function search(Request $request)
    {
        $query = $request->input('query'); // Can be phone or name or id

        if (!$query) {
            return ApiResponse::error('Search query is required', 400);
        }

        // Find member with role 'member' matching phone or name
        $members = User::role('member')
            ->where(function($q) use ($query) {
                $q->where('phone', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%")
                  ->orWhere('id', $query);
            })
            ->with([
                'committees' => function($q) {
                    $q->where('committee_user.status', 'active');
                },
                'installments' => function($q) {
                    $q->where('status', 'pending')->with('committee');
                },
                'loans' => function($q) {
                    $q->where('status', 'active')->with(['installments' => function($sq) {
                        $sq->where('status', 'pending');
                    }]);
                }
            ])
            ->get();

        if ($members->isEmpty()) {
            return ApiResponse::error('No members found', 404);
        }

        return ApiResponse::success($members, 'Members fetched');
    }
}
