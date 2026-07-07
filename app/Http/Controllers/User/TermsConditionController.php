<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\TermsCondition;

class TermsConditionController extends Controller
{
    /**
     * Get the latest Terms & Conditions.
     */
    public function index()
    {
        $terms = TermsCondition::latest()->first();
        if (!$terms) {
            return ApiResponse::error('Terms & Conditions not found', 404);
        }
        return ApiResponse::success($terms, 'Terms & Conditions fetched successfully');
    }
}
