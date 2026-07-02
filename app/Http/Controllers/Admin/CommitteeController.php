<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Committee;
use App\Http\Requests\CommitteeRequest;

class CommitteeController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Committee::with('lotteries.winner');
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        return response()->json($query->get());
    }

    public function store(CommitteeRequest $request)
    {
        $committee = Committee::create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Committee Created',
            'data' => $committee
        ]);
    }

    public function show($id)
    {
        return response()->json(Committee::findOrFail($id));
    }

    public function update(CommitteeRequest $request, $id)
    {
        $committee = Committee::findOrFail($id);
        $committee->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Committee Updated'
        ]);
    }

    public function destroy($id)
    {
        Committee::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Committee Deleted'
        ]);
    }

    public function collectionStats($id)
    {
        $committee = Committee::findOrFail($id);
        
        $members = $committee->members()->get()->map(function ($member) use ($committee) {
            $installments = $member->installments()->where('committee_id', $committee->id)->get();
            
            $totalDeposited = $installments->where('status', 'paid')->sum('amount');
            $totalDue = $installments->where('status', 'pending')->sum('amount');
            $installmentsPaid = $installments->where('status', 'paid')->count();
            $installmentsRemaining = $installments->where('status', 'pending')->count();
            
            $totalExpected = $installments->count() * $committee->amount;
            $totalInstallmentsCount = $installments->count();
            
            return [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'phone' => $member->phone,
                'total_deposited' => $totalDeposited,
                'total_due' => $totalDue,
                'installments_paid' => $installmentsPaid,
                'installments_remaining' => $installmentsRemaining,
                'total_installments' => $totalInstallmentsCount,
                'total_expected' => $totalExpected
            ];
        });

        return response()->json([
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