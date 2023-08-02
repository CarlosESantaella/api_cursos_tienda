<?php

namespace App\Http\Controllers\Tienda;

use App\Models\Sale\Review;
use Illuminate\Http\Request;
use App\Models\Coupon\Coupon;
use App\Models\Coupon\CouponCourse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Coupon\CouponCategory;
use App\Http\Resources\Course\Coupon\CouponResource;

class ReviewController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge(['user_id' => Auth::user()->id]);
        $review = Review::create($request->all());

        return response()->json([
            "message" => 200,
            "review" => $review
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $coupon = Coupon::findOrFail($id);

        // return response()->json([
        //     "coupon" => CouponResource::make($coupon)
        // ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $review = Review::findOrFail($id);
        $review->update($request->all());

        return response()->json([
            "message" => 200,
            "review" => $review
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = Review::findOrFail($id);

        $review->delete();

        return response()->json([
            "message" => 200
        ]);
    }
}
