<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $brands = Brand::all();
            return response()->json(['brands' => $brands]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage() ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator =  Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $creado = Brand::create($request->all());
            return response()->json(['data' => $creado], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage() ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        try {
            $validator =  Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
         
            $brand->fill($request->all());
            $actualizado = $brand->save();

            return response()->json(['data' => $actualizado], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage() ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        try{
            $brand->delete();
            return response()->json($brand, 200);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage() ], 500);
        }
    }
}
