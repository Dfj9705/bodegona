<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
     /**
     * Display a movements
     */
    public function movements(Request $request)
    {
        $fechaInicio = $request->input('fechaInicio');
        $fechaFin = $request->input('fechaFin');
        $products = Product::with('movements')
        ->whereHas('movements', function ($query) use ($fechaInicio, $fechaFin) {
            if ($fechaInicio && $fechaFin) {
                $query->whereBetween('movements.date', [$fechaInicio, $fechaFin]);
            } elseif ($fechaInicio) {
                $query->where('movements.date', '>=', $fechaInicio);
            } elseif ($fechaFin) {
                $query->where('movements.date', '<=', $fechaFin);
            }
        })
        ->get();
            
        return response()->json($products);
    }
     /**
     * Display sales
     */
    public function sales(Request $request)
    {
        $year = $request->input('year');
        
        $salesPerMonth = Sale::select(
            DB::raw('EXTRACT(MONTH FROM date) as month'),
            DB::raw('COUNT(*) as total_sales')
        )
        ->whereYear('date', $year)
        ->groupBy(DB::raw('month'))
        ->get();

        $salesData = array_fill(1, 12, 0);

        foreach ($salesPerMonth as $row) {
            $month = $row->month;
            $totalSales = $row->total_sales;
            $salesData[$month] = $totalSales;
        }

        $formattedData = [];
        for ($i = 1; $i <= 12; $i++) {
            $formattedData['months'][] = date('F', mktime(0, 0, 0, $i, 1));
            $formattedData['amount'][] = $salesData[$i];

        }
        return response()->json($formattedData);
    }
}
