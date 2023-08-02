<?php

namespace App\Http\Controllers\Tienda;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Sale\Sale;
use Illuminate\Http\Request;
use App\Models\CoursesStudent;
use App\Models\Sale\SaleDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Ecommerce\Sale\SaleCollection;
use App\Http\Resources\Ecommerce\Course\CourseHomeResource;

class ProfileClientController extends Controller
{
    public function profile(Request $request)
    {
        $user = Auth::user();

        $enrolled_course_count = CoursesStudent::where('user_id', $user->id)->count();
        $active_course_count = CoursesStudent::where('user_id', $user->id)->where('clases_checkeds', "<>", NULL)->count();
        $terminated_course_count = CoursesStudent::where('user_id', $user->id)->where('state', 2)->count();

        $enrolled_course = CoursesStudent::where('user_id', $user->id)->get();
        $active_course = CoursesStudent::where('user_id', $user->id)->where('clases_checkeds', "<>", NULL)->get();
        $terminated_course = CoursesStudent::where('user_id', $user->id)->where('state', 2)->get();

        $sale_details = SaleDetail::whereHas("sale", function($q) use ($user){
            $q->where("user_id", $user->id);
        })
        ->orderBy("id", "desc")
        ->get();

        $sales = Sale::where("user_id", $user->id)->orderBy("id", "desc")->get();

        return response()->json([
            "user" => [
                "name" => $user->name,
                "surname" => $user->surname ?? '',
                "email" => $user->email,
                "profesion" => $user->profesion,
                "phone" => $user->phone,
                "descripcion" => $user->description,
                "avatar" => env("APP_URL") . "storage/" . $user->avatar,
            ],
            "enrolled_course_count" => $enrolled_course_count,
            "active_course_count" => $active_course_count,
            "terminated_course_count" => $terminated_course_count ?? 0,
            "enrolled_course" => $enrolled_course->map(function($course_student){
                $clases_checkeds = $course_student->clases_checked ? explode(",", $course_student->clases_checked) : [];
                return [
                    "id" => $course_student->id,
                    "clases_checkeds" => $clases_checkeds,
                    "porcentage" =>  round((sizeof($clases_checkeds)/$course_student->course->count_class)*100, 2),
                    "course" => CourseHomeResource::make($course_student->course),
                ];
            }),
            "active_course" => $active_course->map(function($course_student){
                $clases_checkeds = $course_student->clases_checked ? explode(",", $course_student->clases_checked) : [];
                return [
                    "id" => $course_student->id,
                    "clases_checkeds" => $clases_checkeds,
                    "porcentage" =>  round((sizeof($clases_checkeds)/$course_student->course->count_class)*100, 2),
                    "course" => CourseHomeResource::make($course_student->course),
                ];
            }),
            "terminated_course" => $terminated_course->map(function($course_student){
                $clases_checkeds = $course_student->clases_checked ? explode(",", $course_student->clases_checked) : [];
                return [
                    "id" => $course_student->id,
                    "clases_checkeds" => $clases_checkeds,
                    "porcentage" =>  round((sizeof($clases_checkeds)/$course_student->course->count_class)*100, 2),
                    "course" => CourseHomeResource::make($course_student->course),
                ];
            }),
            "sale_details" => $sale_details->map(function($sale_detail){
                return [
                    "id" => $sale_detail->id,
                    "review" => $sale_detail->review ?? 0,
                    "course" => [
                        "id" => $sale_detail->course->id,
                        "title" => $sale_detail->course->title,
                        "imagen" => env("APP_URL")."storage/".$sale_detail->course->imagen
                    ],
                    "created_at" => Carbon::parse($sale_detail->created_at)->format("Y-m-d h:i:s")
                ];
            }),
            "sales" => SaleCollection::make($sales)
        ]);
    }

    public function update_client(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);

        if($request->password){
            $request->merge(["password" => Hash::make($request->password)]);
        }

        if($request->hasFile('imagen')){
            if($user->avatar){
                Storage::delete($user->avatar);
            }
            $path = Storage::putFile("users",$request->file("imagen"));
            $request->merge(["avatar" => $path]);
            $request->merge(["avatar" => $path]);
        }

        $user->update($request->all());

        return response()->json([
            "message" => 200
        ]);
    }
}
