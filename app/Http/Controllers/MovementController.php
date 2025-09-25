<?php

namespace App\Http\Controllers;

use App\Models\DetailSale;
use App\Models\Movement;
use App\Models\Product;
use App\Models\Sale;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fechaInicio = $request->input('fechaInicio');
        $fechaFin = $request->input('fechaFin');
        try {
            $query = Product::leftJoin('movements','movements.product_id', '=', 'products.id')
            ->select('products.name','products.id', 'products.price')
            ->SelectRaw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) AS ingresos')
            ->SelectRaw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) AS egresos')
            ->SelectRaw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) - SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) AS saldo');

            if ($fechaInicio && $fechaFin) {
                $query->whereBetween('movements.date', [$fechaInicio, $fechaFin]);
            } elseif ($fechaInicio) {
                $query->where('movements.date', '>=', $fechaInicio);
            } elseif ($fechaFin) {
                $query->where('movements.date', '<=', $fechaFin);
            }

            $query->groupBy('products.name', 'products.id');
            
            $movements = $query->get();
            return response()->json(['movements' => $movements]);
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
            
            $products = $request->input('products');
            $amounts = $request->input('amounts');
            $type = $request->input('type');
            $validator =  Validator::make($request->all(), [
                'amounts' => ['required', 'array'],
                'amounts.*' => ['numeric', 'min:1',  'verificar_existencias:' . implode(';', $products) . ',' . implode(';', $amounts) . ','. $type],
                'type' => ['required', 'numeric','in:1,2'],
                'products' => ['required', 'array'],
                'products.*' => ['exists:products,id'],
                'date' => ['required', 'date']

            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $data = $request->all();
 
            $data['user_id'] = auth()->id();
            $returnData = null;

            if($type == 2) {
                $sale = Sale::create([
                    'date' => $data['date'],
                    'user_id' => $data['user_id'],
                ]);
    
                foreach ($products as $key => $product) {
                    $detail = DetailSale::create([
                        'product_id' => $product,
                        'sale_id' => $sale->id,
                        'amount' => $amounts[$key]
                    ]);
                }
                $returnData = $sale;
            }

            foreach ($products as $key => $product) {
                Movement::create([
                    'product_id' => $product,
                    'type' => $type,
                    'amount' => $amounts[$key],
                    'date' => $data['date'],
                    'user_id' => $data['user_id'],
                ]);
            }
           
            return response()->json($returnData, 200);
    
            // return response()->json(['data' => $creado], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage() ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movement $movement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movement $movement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movement $movement)
    {
        //
    }
}
