<?php

namespace App\Http\Controllers\Tienda;

use App\Mail\SaleEmail;
use App\Models\Sale\Cart;
use App\Models\Sale\Sale;
use Illuminate\Http\Request;
use App\Models\CoursesStudent;
use App\Models\Sale\SaleDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->request->add(["user_id" => Auth::user()->id]);
        $sale = Sale::create($request->all());

        $carts = Cart::where("user_id", Auth::user()->id)->get();

        foreach($carts as $cart){
            $new_detail = [];
            $new_detail = $cart->toArray();
            unset($new_detail['user_id']);
            $new_detail["sale_id"] = $sale->id;
            SaleDetail::create($new_detail ?? []);
            CoursesStudent::create([
                "course_id" => $new_detail['course_id'],
                "user_id" => Auth::user()->id,
            ]);
        }
        // AQUÍ IRÍA EL CÓDIGO PARA ENVIO DE CORREO
        Mail::to($sale->user->email)->send(new SaleEmail($sale));
        return response()->json([
            "message" => 200,
            "message_text" => "LOS CURSOS SE HAN ADQUIRIDO CORRECTAMENTE"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
