<?php

namespace App\Http\Controllers\Admin\Coupon;

use Illuminate\Http\Request;
use App\Models\Coupon\Coupon;
use App\Models\Course\Course;
use App\Models\Course\Category;
use App\Models\Coupon\CouponCourse;
use App\Http\Controllers\Controller;
use App\Models\Coupon\CouponCategory;
use App\Http\Resources\Course\Coupon\CouponResource;
use App\Http\Resources\Course\Coupon\CouponCollection;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        $search = $request->search;
        $state = $request->state;
        $coupons = Coupon::filterAdvance($search, $state)->orderBy("id", "desc")->get();

        return response()->json([
            "message" => 200,
            "coupons" => CouponCollection::make($coupons)
        ]);
    }

    public function config()
    {
        $categories = Category::where("categorie_id", NULL)->orderBy("id", "desc")->get();
        $courses = Course::where("state", 2)->orderBy("id", "desc")->get();
        return response()->json([
            "categories" => $categories?->map(function($category){
                return [
                    "id" => $category->id,
                    "name" => $category->name,
                    "imagen" => env("APP_URL")."storage/".$category->imagen
                ];
            }),
            "courses" => $courses?->map(function($course){
                return [
                    "id" => $course->id,
                    "title" => $course->title,
                    "imagen" => env("APP_URL")."storage/".$course->imagen
                ];
            })
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $EXISTS = Coupon::where("code", $request->code)->first();
        if($EXISTS){
            return response()->json([
                "message" => 403,
                "message_text" => "EL CÓDIGO DEL CUPON YA EXISTE"
            ]);
        }

        
        // return response()->json($request->except(['courses_selecteds', 'categories_selecteds']));
        $coupon = Coupon::create($request->except(['courses_selecteds', 'categories_selecteds']));

        if($request->type_coupon == 1){ // es course
            foreach($request->courses_selecteds as $course){
                CouponCourse::create([
                    "coupon_id" => $coupon->id,
                    "course_id" => $course["id"],
                ]);
            }
        }

        if($request->type_coupon == 2){ // es categorie
            foreach($request->categories_selecteds as $categorie){
                CouponCategory::create([
                    "coupon_id" => $coupon->id,
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
        $coupon = Coupon::findOrFail($id);

        return response()->json([
            "coupon" => CouponResource::make($coupon)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $EXISTS = Coupon::where("id", "<>", $id)->where("code", $request->code)->first();
        if($EXISTS){
            return response()->json([
                "message" => 403,
                "message_text" => "EL CÓDIGO DEL CUPON YA EXISTE"
            ]);
        }
        $coupon = Coupon::findOrFail($id);
        $coupon->update($request->except(['courses_selecteds', 'categories_selecteds']));

        foreach($coupon->courses as $course){
            $course->delete();
        }

        foreach($coupon->categories as $categorie){
            $categorie->delete();
        }

        if($request->type_coupon == 1){ // es course

            foreach($request->courses_selecteds as $course){
                CouponCourse::create([
                    "coupon_id" => $coupon->id,
                    "course_id" => $course["id"],
                ]);
            }
        }

        if($request->type_coupon == 2){ // es categorie
            foreach($request->categories_selecteds as $categorie){
                CouponCategory::create([
                    "coupon_id" => $coupon->id,
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
        $coupon = Coupon::findOrFail($id);

        $coupon->delete();

        return response()->json([
            "message" => 200
        ]);
    }
}
