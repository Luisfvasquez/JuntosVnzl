<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CountryAid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountryAidController extends Controller
{
    public function index(Request $request)
    {
        $aids = CountryAid::paginate($request->query('per_page', 15));
        return response()->json([
            'success' => true,
            'data' => $aids
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_name' => 'required|string|max:100',
            'iso_code' => 'required|string|size:3|unique:country_aids,iso_code',
            'aid_type' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $aid = CountryAid::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Ayuda internacional registrada',
            'data' => $aid
        ], 201);
    }
}
