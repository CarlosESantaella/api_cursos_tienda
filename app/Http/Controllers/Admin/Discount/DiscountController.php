<?php

namespace App\Http\Controllers\Admin\Discount;

use Illuminate\Http\Request;
use App\Models\Discount\Discount;
use App\Http\Controllers\Controller;
use App\Models\Discount\DiscountCourse;
use App\Models\Discount\DiscountCategorie;
use App\Http\Resources\Discount\DiscountResource;
use App\Http\Resources\Discount\DiscountCollection;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $state = $request->state;
        // $coupons = Coupon::filterAdvance($state)->orderBy("id", "desc")->get();
        $discounts = Discount::orderBy("id", "desc")->get();

        return response()->json([
            "message" => 200,
            "discounts" => DiscountCollection::make($discounts)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //discount_type 1 es course y 2 es categoría
        
        if ($request->discount_type == 1) {
            foreach ($request->courses_selecteds as $course) {
                $IS_DISCOUNT_START_DATE = Discount::where("type_campaing", $request->type_campaing)
                    ->whereHas("courses", function ($query) use ($course) {
                        return $query->where("course_id", $course["id"]);
                    })
                    ->whereBetween("start_date", [$request->start_date, $request->end_date])
                    ->first();

                $IS_DISCOUNT_END_DATE = Discount::where("type_campaing", $request->type_campaing)
                    ->whereHas("courses", function ($query) use ($course) {
                        return $query->where("course_id", $course["id"]);
                    })
                    ->whereBetween("end_date", [$request->start_date, $request->end_date])
                    ->first();

                if ($IS_DISCOUNT_START_DATE || $IS_DISCOUNT_END_DATE) {
                    return response()->json([
                        "message" => 403,
                        "message_text" => "EL CURSO " . $course["title"] . " YA SE ENCUENTRA EN UNA CAMPAÑA DE DESCUENTO " . ($IS_DISCOUNT_START_DATE ? $IS_DISCOUNT_START_DATE->id : '') . ($IS_DISCOUNT_END_DATE ? "/" . $IS_DISCOUNT_END_DATE->id : '')
                    ]);
                }
            }
        }

        if ($request->discount_type == 2) {
            foreach ($request->categories_selecteds as $categorie) {
                $IS_DISCOUNT_START_DATE = Discount::where("type_campaing", $request->type_campaing)
                    ->whereHas("categories", function ($query) use ($categorie) {
                        return $query->where("categorie_id", $categorie["id"]);
                    })
                    ->whereBetween("start_date", [$request->start_date, $request->end_date])
                    ->first();

                $IS_DISCOUNT_END_DATE = Discount::where("type_campaing", $request->type_campaing)
                    ->whereHas("categories", function ($query) use ($categorie) {
                        return $query->where("categorie_id", $categorie["id"]);
                    })
                    ->whereBetween("end_date", [$request->start_date, $request->end_date])
                    ->first();

                if ($IS_DISCOUNT_START_DATE || $IS_DISCOUNT_END_DATE) {
                    return response()->json([
                        "message" => 403,
                        "message_text" => "EL CATEGORÍA " . $categorie["name"] . " YA SE ENCUENTRA EN UNA CAMPAÑA DE DESCUENTO " . ($IS_DISCOUNT_START_DATE ? $IS_DISCOUNT_START_DATE->id : '') . ($IS_DISCOUNT_END_DATE ? "/" . $IS_DISCOUNT_END_DATE->id : '')
                    ]);
                }
            }
        }
        $request->request->add(["code" => uniqid()]);
        // return response()->json($request->except(['courses_selecteds', 'categories_selecteds']));
        $discount = Discount::create($request->except(['courses_selecteds', 'categories_selecteds']));

        if ($request->discount_type == 1) { // es course
            foreach ($request->courses_selecteds as $course) {
                DiscountCourse::create([
                    "discount_id" => $discount->id,
                    "course_id" => $course["id"],
                ]);
            }
        }
        if ($request->discount_type == 2) { // es categorie
            foreach ($request->categories_selecteds as $categorie) {
                DiscountCategorie::create([
                    "discount_id" => $discount->id,
                    "categorie_id" => $categorie["id"],
                ]);
            }
        }

        return response()->json([
            "message" => 200,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $discount = Discount::findOrFail($id);

        return response()->json([
            "discount" => DiscountResource::make($discount)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //discount_type 1 es course y 2 es categoría
        if ($request->discount_type == 1) {
            foreach ($request->courses_selecteds as $course) {
                $IS_DISCOUNT_START_DATE = Discount::where('id', '<>', $id)->where("type_campaing", $request->type_campaing)
                    ->whereHas("courses", function ($query) use ($course) {
                        return $query->where("course_id", $course["id"]);
                    })
                    ->whereBetween("start_date", [$request->start_date, $request->end_date])
                    ->first();

                $IS_DISCOUNT_END_DATE = Discount::where('id', '<>', $id)->where("type_campaing", $request->type_campaing)
                    ->whereHas("courses", function ($query) use ($course) {
                        return $query->where("course_id", $course["id"]);
                    })
                    ->whereBetween("end_date", [$request->start_date, $request->end_date])
                    ->first();

                if ($IS_DISCOUNT_START_DATE || $IS_DISCOUNT_END_DATE) {
                    return response()->json([
                        "message" => 403,
                        "message_text" => "EL CURSO " . $course["title"] . " YA SE ENCUENTRA EN UNA CAMPAÑA DE DESCUENTO " . ($IS_DISCOUNT_START_DATE ? $IS_DISCOUNT_START_DATE->id : '') . ($IS_DISCOUNT_END_DATE ? "/" . $IS_DISCOUNT_END_DATE->id : '')
                    ]);
                }
            }
        }
        if ($request->discount_type == 2) {
            foreach ($request->categories_selecteds as $categorie) {
                $IS_DISCOUNT_START_DATE = Discount::where('id', '<>', $id)->where("type_campaing", $request->type_campaing)
                    ->whereHas("categories", function ($query) use ($categorie) {
                        return $query->where("categorie_id", $categorie["id"]);
                    })
                    ->whereBetween("start_date", [$request->start_date, $request->end_date])
                    ->first();

                $IS_DISCOUNT_END_DATE = Discount::where('id', '<>', $id)->where("type_campaing", $request->type_campaing)
                    ->whereHas("categories", function ($query) use ($categorie) {
                        return $query->where("categorie_id", $categorie["id"]);
                    })
                    ->whereBetween("end_date", [$request->start_date, $request->end_date])
                    ->first();

                if ($IS_DISCOUNT_START_DATE || $IS_DISCOUNT_END_DATE) {
                    return response()->json([
                        "message" => 403,
                        "message_text" => "EL CATEGORÍA " . $categorie["name"] . " YA SE ENCUENTRA EN UNA CAMPAÑA DE DESCUENTO " . ($IS_DISCOUNT_START_DATE ? $IS_DISCOUNT_START_DATE->id : '') . ($IS_DISCOUNT_END_DATE ? "/" . $IS_DISCOUNT_END_DATE->id : '')
                    ]);
                }
            }
        }

        // $request->request->add(["code" => uniqid()]);
        $discount = Discount::findOrFail($id);

        $discount->update($request->except(['courses_selecteds', 'categories_selecteds', 'discount']));

        foreach($discount->courses as $course){
            $course->delete();
        }

        foreach($discount->categories as $categorie){
            $categorie->delete();
        }

        if ($request->discount_type == 1) { // es course
            foreach ($request->courses_selecteds as $course) {
                DiscountCourse::create([
                    "discount_id" => $discount->id,
                    "course_id" => $course["id"],
                ]);
            }
        }

        if ($request->discount_type == 2) { // es categorie
            foreach ($request->categories_selecteds as $categorie) {
                DiscountCategorie::create([
                    "discount_id" => $discount->id,
                    "categorie_id" => $categorie["id"],
                ]);
            }
        }

        return response()->json([
            "message" => 200,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();
        return response()->json([
            "message" => 200,
        ]);
    }
}
