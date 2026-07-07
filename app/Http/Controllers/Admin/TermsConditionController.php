<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsCondition;
use Illuminate\Http\Request;

class TermsConditionController extends Controller
{
    /**
     * Get the latest Terms & Conditions.
     */
    public function show()
    {
        $terms = TermsCondition::latest()->first();
        if (!$terms) {
            // Create a default one if none exists
            $terms = TermsCondition::create([
                'title' => 'Terms & Conditions',
                'content' => 'Please enter Terms & Conditions here.'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $terms
        ]);
    }

    /**
     * Update the Terms & Conditions.
     */
    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string'
        ]);

        $terms = TermsCondition::latest()->first();
        if (!$terms) {
            $terms = new TermsCondition();
        }

        $terms->title = $request->title;
        $terms->content = $request->content;
        $terms->save();

        return response()->json([
            'status' => true,
            'message' => 'Terms & Conditions Updated',
            'data' => $terms
        ]);
    }
}
