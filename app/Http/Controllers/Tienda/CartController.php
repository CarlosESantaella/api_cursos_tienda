<?php

namespace App\Http\Controllers\Tienda;

use App\Models\Sale\Cart;
use Illuminate\Http\Request;
use App\Models\Coupon\Coupon;
use App\Models\CoursesStudent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Ecommerce\Cart\CartResource;
use App\Http\Resources\Ecommerce\Cart\CartCollection;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::guard('api')->user();

        $carts = Cart::where("user_id", $user->id)->get();

        return response()->json([
            "carts" => CartCollection::make($carts)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $user = Auth::guard('api')->user();

        //verifica si el curso ya pertenece al estudiante
        $have_course = CoursesStudent::where([
            ["user_id", $user->id],
            ["course_id", $request->course_id],
        ])->first();

        //verifica si el curso ya existe en el carrito
        $exists_cart = Cart::where([
            ["user_id", $user->id],
            ["course_id", $request->course_id],
        ])->first();
        if ($exists_cart) {
            return response()->json([
                "message" => 403,
                "message_text" => "EL CURSO YA EXISTE EN EL CARRITO DE COMPRAS"
            ]);
        }

        if($have_course){
            return response()->json([
                "message" => 403,
                "message_text" => "YA HAS ADQUIRIDO ESTE CURSO"
            ]);
        }

        $request->request->add(["user_id" => $user->id]);
        $cart = Cart::create($request->all());

        return response()->json([
            "cart" => CartResource::make($cart)
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
        $cart = Cart::findOrFail($id);

        $cart->delete();

        return response()->json([
            "message" => 200
        ]);
    }

    public function apply_coupon(Request $request)
    {
        $cupon = Coupon::where("code", $request->code)->where("state", 1)->first();
        if (!$cupon) {
            return response()->json([
                "message" => 403,
                "message_text" => "EL COUPON INGRESADO NO EXISTE",
            ]);
        }

        $carts = Cart::where("user_id", Auth::guard('api')->user()->id)->get();

        foreach ($carts as $cart) {
            if ($cupon->type_coupon == 1) {
                $exists_course_cupon = false;
                foreach ($cupon->courses as $course) {
                    if ($course->course_id == $cart->course_id) {
                        $exists_course_cupon = true;
                    }
                }
                if ($exists_course_cupon) {
                    $total = 0;
                    if ($cupon->type_discount == 1) {
                        $total = $cart->precio_unitario - ($cart->precio_unitario * ($cupon->discount * 0.01));
                    }
                    if ($cupon->type_discount == 2) {
                        $total = $cart->precio_unitario - ($cupon->discount * 0.01);
                    }
                    $cart->update([
                        "type_discount" => $cupon->type_discount,
                        "discount" => $cupon->discount,
                        "type_campaing" => NULL,
                        "code_coupon" => $cupon->code,
                        "code_discount" => NULL,
                        "total" => $total,
                    ]);
                }
            }

            if ($cupon->type_coupon == 2) { //cupon de tipo categoría
                $exists_course_cupon = false;
                foreach ($cupon->categories as $categorie) {
                    if ($categorie->categorie_id == $cart->course->categorie_id) {
                        $exists_course_cupon = true;
                    }
                }
                if ($exists_course_cupon) {
                    $total = 0;
                    if ($cupon->type_discount == 1) {
                        $total = $cart->precio_unitario - ($cart->precio_unitario * ($cupon->discount * 0.01));
                    }
                    if ($cupon->type_discount == 2) {
                        $total = $cart->precio_unitario - ($cupon->discount * 0.01);
                    }
                    $cart->update([
                        "type_discount" => $cupon->type_discount,
                        "discount" => $cupon->discount,
                        "type_campaing" => NULL,
                        "code_coupon" => $cupon->code,
                        "code_discount" => NULL,
                        "total" => $total,
                    ]);
                }
            }
        }

        $carts = Cart::where("user_id", Auth::guard('api')->user()->id)->get();

        return response()->json([
            "cart" => CartCollection::make($carts)
        ]);
    }
}
