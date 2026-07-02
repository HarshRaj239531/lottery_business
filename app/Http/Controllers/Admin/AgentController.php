<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentTarget;
use App\Models\AgentCollection;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;

class AgentController extends Controller
{
    // Assign Target to an Agent
    public function assignTarget(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'target_type' => 'required|in:amount,count',
            'target_value' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $target = AgentTarget::create([
            'agent_id' => $request->agent_id,
            'admin_id' => auth()->id(),
            'target_type' => $request->target_type,
            'target_value' => $request->target_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'active',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Target successfully assigned to the agent.',
            'data' => $target
        ]);
    }

    // Get all targets for a specific agent or all agents
    public function getTargets(Request $request)
    {
        $query = AgentTarget::with('agent');
        if ($request->has('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }
        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    // Get all agent collections waiting for approval
    public function getCollections(Request $request)
    {
        $query = AgentCollection::with(['agent', 'member', 'installment.committee', 'loanInstallment.loan']);
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    // Admin approves the collection
    public function approveCollection(Request $request, $id)
    {
        $collection = AgentCollection::findOrFail($id);

        if ($collection->status === 'approved') {
            return response()->json(['status' => false, 'message' => 'Collection already approved.']);
        }

        $collection->update([
            'status' => 'approved',
        ]);

        // Update the related installment to paid
        if ($collection->collection_type === 'committee' && $collection->installment_id) {
            $installment = \App\Models\Installment::find($collection->installment_id);
            if ($installment) {
                $installment->update([
                    'status' => 'paid',
                    'paid_date' => now(),
                    'collected_by' => $collection->agent_id,
                ]);
            }
        } elseif ($collection->collection_type === 'loan' && $collection->loan_installment_id) {
            $loanInstallment = \App\Models\LoanInstallment::find($collection->loan_installment_id);
            if ($loanInstallment) {
                $loanInstallment->update([
                    'status' => 'paid',
                    'paid_date' => now(),
                    'collected_by' => $collection->agent_id,
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Collection approved and installment marked as paid.',
            'data' => $collection
        ]);
    }

    // Admin rejects the collection
    public function rejectCollection($id)
    {
        $collection = AgentCollection::findOrFail($id);
        $collection->update(['status' => 'rejected']);
        
        return response()->json([
            'status' => true,
            'message' => 'Collection rejected.'
        ]);
    }
}
