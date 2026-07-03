<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Committee;
use App\Http\Requests\CommitteeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommitteeController extends Controller
{
    /**
     * LIST COMMITTEES
     */
    public function index(Request $request)
    {
        $query = Committee::with('lotteries.winner');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'status' => true,
            'data' => $query->latest()->paginate(10)
        ]);
    }

    /**
     * CREATE
     */
    public function store(CommitteeRequest $request)
    {
        $committee = Committee::create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Committee Created',
            'data' => $committee
        ], 201);
    }

    /**
     * SHOW
     */
    public function show($id)
    {
        $committee = Committee::with('lotteries.winner')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $committee
        ]);
    }

    /**
     * UPDATE
     */
    public function update(CommitteeRequest $request, $id)
    {
        $committee = Committee::findOrFail($id);
        $committee->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Committee Updated',
            'data' => $committee
        ]);
    }

    /**
     * DELETE (SAFE)
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $committee = Committee::findOrFail($id);

            // Optional: check if has members
            if ($committee->members()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete committee with members'
                ], 400);
            }

            $committee->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Committee Deleted'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Delete failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * COLLECTION STATS (OPTIMIZED)
     */
    public function collectionStats($id)
    {
        $committee = Committee::findOrFail($id);

        $members = $committee->members()
            ->with(['installments' => function ($q) use ($id) {
                $q->where('committee_id', $id);
            }])
            ->get()
            ->map(function ($member) use ($committee) {

                $installments = $member->installments;

                $paid = $installments->where('status', 'paid');
                $pending = $installments->where('status', 'pending');

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'phone' => $member->phone,

                    'total_deposited' => $paid->sum('amount'),
                    'total_due' => $pending->sum('amount'),

                    'installments_paid' => $paid->count(),
                    'installments_remaining' => $pending->count(),

                    'total_installments' => $installments->count(),
                    'total_expected' => $installments->count() * $committee->amount
                ];
            });

        return response()->json([
            'status' => true,
            'committee' => [
                'id' => $committee->id,
                'name' => $committee->name,
                'amount' => $committee->amount,
                'duration' => $committee->duration
            ],
            'members' => $members
        ]);
    }
}