<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ReportController extends Controller
{
    public function receipt(Request $request)
    {
        $id = $request->input('id');
        $sale = Sale::find($id);
        $imagePath = public_path('images/bodegona_logo.png');
        $pdf = PDF::loadView('pdf.receipt', compact('sale'), [], [
            'show_watermark_image' => TRUE,
            'watermark_image_path' => $imagePath,
            'default_font_size' => '12',
            'default_font' => 'arial',
            'margin_top' => 10,
            'orientation' => 'L',
            'format' => 'A5'
        ]);

        return $pdf->stream('document.pdf');
    }
}
