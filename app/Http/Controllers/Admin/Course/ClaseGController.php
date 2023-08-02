<?php

namespace App\Http\Controllers\Admin\Course;

use Illuminate\Http\Request;
use Owenoj\LaravelGetId3\GetId3;
use Vimeo\Laravel\Facades\Vimeo;
use App\Models\Course\CourseClase;
use App\Http\Controllers\Controller;
use App\Models\Course\CourseClaseFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Course\Clases\CourseClaseResource;
use App\Http\Resources\Course\Clases\CourseClaseCollection;

class ClaseGController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $clases = CourseClase::where("course_section_id", $request->course_section_id)->orderBy("id", "asc")->get();

        return response()->json([
            "clases" => CourseClaseCollection::make($clases)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $clase = CourseClase::create($request->except("files"));

        foreach($request->file("files") as $key => $file){
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();
            $name_file = $file->getClientOriginalName();
            $data = [];
            if(in_array(strtolower($extension), ["jpeg", "bmp", "jpg", "png"])){
                $data = getimagesize($file);
            }
            $path = Storage::putFile("clases_files", $file);

            $clase_file = CourseClaseFile::create([
                "course_clase_id" => $clase->id,
                "name_file" => $name_file,
                "size" => $size,
                "resolution" => $data ? $data[0]." X ".$data[1] : NULL,
                "file" => $path,
                "type" => $extension
            ]);
        }

        return response()->json(["clase" => CourseClaseResource::make($clase)]);
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

        $courseClase = CourseClase::findOrFail($id);
        $courseClase->vimeo_id = $vimeo_id;
        $courseClase->time = date("H:i:s", $time);
        $courseClase->save();

        return response()->json([
            "link_video" => 'https://player.vimeo.com/video/'.$vimeo_id
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
        $clase = CourseClase::FindOrFail($id);
        $clase->update($request->all());
        return response()->json([
            "clase" => CourseClaseResource::make($clase)
        ]);
    }

    public function addFiles(Request $request)
    {
        $clase = CourseClase::findOrFail($request->course_clase_id);
        foreach($request->file("files") as $key => $file){
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();
            $name_file = $file->getClientOriginalName();
            $data = [];
            if(in_array(strtolower($extension), ["jpeg", "bmp", "jpg", "png"])){
                $data = getimagesize($file);
            }
            $path = Storage::putFile("clases_files", $file);

            $clase_file = CourseClaseFile::create([
                "course_clase_id" => $clase->id,
                "name_file" => $name_file,
                "size" => $size,
                "resolution" => $data ? $data[0]." X ".$data[1] : NULL,
                "file" => $path,
                "type" => $extension
            ]);
        }

        return response()->json([
            "clase" => CourseClaseResource::make($clase)
        ]);
    }

    public function removeFiles(string $id)
    {
        $course_clase_file = CourseClaseFile::findOrFail($id);
        $course_clase_file->delete();

        return response()->json([
            "message" => 200
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $clase = CourseClase::findOrFail($id);
        $clase->delete();
        return response()->json([
            "message" => 200
        ]);
    }
}
