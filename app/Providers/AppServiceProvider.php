<?php

namespace App\Providers;

use App\Models\Movement;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('verificar_existencias', function ($attribute, $value, $parameters, $validator){
            $divisor = explode ('.',$attribute);
            $indice =  $divisor[1];
            $productos =  explode(';',$parameters[0]);
            $cantidades = explode(';',$parameters[1]); 
            $type = $parameters[2]; 

        

        
            if (!$this->verificarExistencias($productos[$indice], $cantidades[$indice], $type)) {
                return false;
            }
            
        
            return true;
            // $type = $parameters[1];
            // if($type == 2){
            //     $product_id = $parameters[0];
            //     $amount = $value;
            //     $ingresos = Movement::where('product_id', $product_id)->where('type', 1)->sum('amount');
            //     $egresos = Movement::where('product_id', $product_id)->where('type', 2)->sum('amount');

            //     $disponible = $ingresos - $egresos;

            //     return $disponible >= $amount; 
            // }
            // return true;
        }, "No hay suficientes existencias para realizar esta acción");

       
        
    }

    public function verificarExistencias($product, $amount, $type) {
            if($type == 2){
                $ingresos = Movement::where('product_id', $product)->where('type', 1)->sum('amount');
                $egresos = Movement::where('product_id', $product)->where('type', 2)->sum('amount');

                $disponible = $ingresos - $egresos;
                return $disponible >= $amount; 
            }
            return true;
    }


}
