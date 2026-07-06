<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Material;
use App\Models\MaterialStock;
use App\Helpers\ApiResponse;

class MaterialController extends Controller
{
    // List active materials
    public function index()
    {
        $materials = Material::where('status', 'active')->get();
        return ApiResponse::success($materials, 'Active materials fetched');
    }

    // List recent stock transactions for authenticated user
    public function stocks(Request $request)
    {
        $stocks = MaterialStock::where('user_id', $request->user()->id)
            ->latest()
            ->take(10)
            ->get();
        return ApiResponse::success($stocks, 'User recent material stocks fetched');
    }
}
