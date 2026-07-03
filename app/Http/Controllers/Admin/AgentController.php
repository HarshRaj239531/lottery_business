<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\AgentTarget;
use App\Models\AgentCollection;
use App\Models\Installment;
use App\Models\LoanInstallment;

class AgentController extends Controller
{
    /**
     * Assign Target to Agent
     */
    public function assignTarget(Request $request)
    {
        $validated = $request->validate([
            'agent_id'     => 'required|exists:users,id',
            'target_type'  => 'required|in:amount,count',
            'target_value' => 'required|numeric|min:1',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
        ]);

        $target = AgentTarget::create([
            'agent_id'     => $validated['agent_id'],
            'admin_id'     => Auth::id(),
            'target_type'  => $validated['target_type'],
            'target_value' => $validated['target_value'],
            'start_date'   => $validated['start_date'],
            'end_date'     => $validated['end_date'],
            'status'       => 'active',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Target assigned successfully',
            'data'    => $target
        ], 201);
    }

    /**
     * Get Targets
     */
    public function getTargets(Request $request)
    {
        $query = AgentTarget::with('agent');

        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        return response()->json([
            'status' => true,
            'data'   => $query->latest()->get()
        ]);
    }

    /**
     * Get Collections
     */
    public function getCollections(Request $request)
    {
        $query = AgentCollection::with([
            'agent',
            'member',
            'installment.committee',
            'loanInstallment.loan'
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'status' => true,
            'data'   => $query->latest()->get()
        ]);
    }

    /**
     * Approve Collection (SECURE + TRANSACTION)
     */
    public function approveCollection($id)
    {
        DB::beginTransaction();

        try {
            $collection = AgentCollection::lockForUpdate()->findOrFail($id);

            if ($collection->status === 'approved') {
                return response()->json([
                    'status' => false,
                    'message' => 'Already approved'
                ], 400);
            }

            $collection->update([
                'status' => 'approved'
            ]);

            // Committee Installment
            if ($collection->collection_type === 'committee' && $collection->installment_id) {

                $installment = Installment::find($collection->installment_id);

                if ($installment && $installment->status !== 'paid') {
                    $installment->update([
                        'status'       => 'paid',
                        'paid_date'    => now(),
                        'collected_by' => $collection->agent_id,
                    ]);
                }
            }

            // Loan Installment
            if ($collection->collection_type === 'loan' && $collection->loan_installment_id) {

                $loanInstallment = LoanInstallment::find($collection->loan_installment_id);

                if ($loanInstallment && $loanInstallment->status !== 'paid') {
                    $loanInstallment->update([
                        'status'       => 'paid',
                        'paid_date'    => now(),
                        'collected_by' => $collection->agent_id,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Approved successfully',
                'data' => $collection
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject Collection
     */
    public function rejectCollection($id)
    {
        $collection = AgentCollection::findOrFail($id);

        if ($collection->status === 'approved') {
            return response()->json([
                'status' => false,
                'message' => 'Cannot reject approved collection'
            ], 400);
        }

        $collection->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Collection rejected'
        ]);
    }
}