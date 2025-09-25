<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = Product::join('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('images', 'products.id', '=', 'images.product_id')
            ->select('products.*', 'brands.name as brand_name')
            ->selectRaw('COUNT(images.id) as images')
            ->groupBy('products.id','brand_name')
            ->get();
            return response()->json(['products' => $products]);
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
                'price' => ['required', 'decimal:2'],
                'brand_id' => ['required', 'exists:brands,id'],
                // 'images' =>['image','mimes:jpeg,jpg,png','max:32000'] 
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $creado = Product::create($request->all());
            $idProducto = $creado->id;
            // $images = $request->file('images');
            // if($request->hasFile('images')){
            //     $extension = $request->file('images')->extension();
            //     $name = time() . '.' . $extension;
            //     $stored = Storage::disk('digitalocean')->putFileAs('products', $request->file('images'), $name , 'public');
            //     $url = Storage::disk('digitalocean')->url($name);
            //     $imagenBD = new Image();
            //     $imagenBD->url = $url;
            //     $imagenBD->product_id = $idProducto;
            //     $imagenBD->save();
            // }
    
            return response()->json(['data' => $creado], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage() ], 500);
        }
       
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Product $product)
    {
        $fechaInicio = $request->input('fechaInicio');
        $fechaFin = $request->input('fechaFin');
        try{
            $product->load(['brand','movements' => function ($query) use ($fechaInicio, $fechaFin) {
                if ($fechaInicio && $fechaFin) {
                    $query->whereBetween('movements.date', [$fechaInicio, $fechaFin]);
                } elseif ($fechaInicio) {
                    $query->where('movements.date', '>=', $fechaInicio);
                } elseif ($fechaFin) {
                    $query->where('movements.date', '<=', $fechaFin);
                }
            },'images', 'sales.sale']);
            return response()->json($product);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage() ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            $validator =  Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'price' => ['required', 'decimal:2'],
                'brand_id' => ['required', 'exists:brands,id'],
                // 'images.*' =>['image','mimes:jpeg,jpg,png','max:32000'] 
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            // $images = $request->file('images');
            $product->fill($request->all());
            $actualizado = $product->save();
            $idProducto = $product->id;
            // if($request->hasFile('images')){
            //     $images = $request->file('images');
            //     foreach($images as $image){

            //         $nombreAleatorio = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
            //         $rutaImagen = $image->storeAs('public/images', $nombreAleatorio);
            //         $imagenBD = new Image();
            //         $imagenBD->url = $rutaImagen;
            //         $imagenBD->product_id = $idProducto;
            //         $imagenBD->save();
                   
            //     }
            // }
    
            return response()->json(['data' => $actualizado], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage() ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try{
            $images = $product->images;
            foreach ($images as $image){
                if (Storage::exists($image->url)) {
                    $image->delete();
                    Storage::delete($image->url);
                
                } 
            }
            $product->delete();
            return response()->json($product, 200);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage() ], 500);
        }
    }
    
    /**
     * Imagenes del producto
     */
    public function images(Product $product)
    {
        $images = [];
        $i = 0;
        foreach($product->images as $img){
            $images[$i]['url'] = Storage::url($img->url);
            $images[$i]['id'] = $img->id;
            $i++;
        }

        return response()->json($images);
    }

    public function deleteImage(Image $image)
    {
        try{

            if (Storage::exists($image->url)) {
                $image->delete();
                Storage::delete($image->url);
               
            } 
            return response()->json($image);
        }catch(Exception $e){
            return response()->json(['error' => $e->getMessage() ], 500);
        }

    }
}
