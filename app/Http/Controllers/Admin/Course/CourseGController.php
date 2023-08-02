<?php

namespace App\Http\Controllers\Admin\Course;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Models\Course\Category;
use Owenoj\LaravelGetId3\GetId3;
use Vimeo\Laravel\Facades\Vimeo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Course\CourseGResource;
use App\Http\Resources\Course\CourseGCollection;

class CourseGController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $state = $request->state;

        $categories = Course::filterAdvance($search, $state)->orderby("id", "desc")->get();
        // $categories = Course::orderby("id", "desc")->get();

        return response()->json([
            "courses" => CourseGCollection::make($categories),
        ]);
    }

    public function config()
    {
        $categories = Category::where("category_id", '=', null)->orderBy("id", "desc")->get();
        $subcategories = Category::where("category_id","<>", null)->orderBy("id", "desc")->get();

        $instructores = User::where("is_instructor", 1)->orderBy("id", "asc")->get();

        return response()->json([
            "categories" => $categories,
            "subcategories" => $subcategories,
            "instructores" => $instructores?->map(function($user){
                return [
                    "id" => $user->id,
                    "full_name" => $user->name.' '.$user->surname,
                ];
            })
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $exists = Course::where('title', $request->title)->first();
        if($exists){
            return response()->json(["message" => 403, "message_text" => "YA EXISTE UN CURSO CON ESTE TÍTULO"]);
        }
        if ($request->hasFile("portada")) {
            $path = Storage::putFile("categories", $request->file("portada"));
            $request->request->add(["imagen" => $path]);
        }
        
        $request->request->add(["slug" => Str::slug($request->title)]);
        $request->request->add(["requirements" => $request->requirements]);
        $request->request->add(["who_is_it_for" => $request->who_is_it_for]);
        
        $course = Course::create($request->except("portada"));

        return response()->json(["course" => CourseGResource::make($course)]);
    }

    public function uploadVideo(Request $request, $id)
    {
        $time = 0;

        // //instantiate class with file
        $track = new GetId3($request->file('video'));

        // //get playtime
        $time = $track->getPlaytimeSeconds();
        $video = $request->file('video');
        Vimeo::request('/me/videos', ['per_page' => 10], 'GET');

        $response = Vimeo::connection()->upload($video, [
            'name' => pathinfo($video->getClientOriginalName(), PATHINFO_BASENAME),
            'timeout' => 520
        ]);

        $vimeo_id = pathinfo($response, PATHINFO_FILENAME);

        $course = Course::findOrFail($id);
        $course->vimeo_id = $vimeo_id;
        $course->time = date("H:i:s", $time);
        $course->save();

        return response()->json([
            "link_video" => 'https://player.vimeo.com/video/'.$vimeo_id
        ]);
    }


    public function show($id)
    {
        $course = Course::findOrFail($id);

        return response()->json([
            "course" => CourseGResource::make($course)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $exists = Course::where('id', '<>', $id)->where('title', $request->title)->first();
        if($exists){
            return response()->json(["message" => 403, "message_text" => "ERROR DE VALIDACIÓN"]);
        }
        $course = Course::findOrFail($id);
        if ($request->hasFile("portada")) {
            if ($course->imagen) {
                Storage::delete($course->imagen);
            }
            $path = Storage::putFile("categories", $request->file("portada"));
            $request->request->add(["imagen" => $path]);
        }

        $request->request->add(["slug" => Str::slug($request->title)]);
        $request->request->add(["requirements" => $request->requirements]);
        $request->request->add(["who_is_it_for" => $request->who_is_it_for]);

        $course->update($request->except('portada'));

        return response()->json(["course" => CourseGResource::make($course)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return response()->json(["message" => 200]);
    }
}
