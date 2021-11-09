<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PhotoResource;

class PhotoController extends Controller
{
    /**
     * Count resource number.
     *
     * @return \Illuminate\Http\Response
     */
    public function count() {
        return Photo::all()->count();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT);
        $range = filter_input(INPUT_GET, "range", FILTER_SANITIZE_NUMBER_INT);

        $start_id = $range * $page;
        $end_id = $start_id + $range + 1;

        $photos = (is_null($page) || is_null($range)) ? Photo::all() : Photo::where('id', '>', $start_id)->where('id', '<', $end_id)->get();

        return response([ 'photos' => PhotoResource::collection($photos), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'url' => 'required|url|max:255',
            'alt' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $photo = Photo::create($data);

        return response(['photo' => new PhotoResource($photo), 'message' => 'Created successfully'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function show(Photo $photo)
    {
        return response(['photo' => new PhotoResource($photo), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Photo $photo)
    {
        $photo->update($request->all());

        return response(['photo' => new PhotoResource($photo), 'message' => 'Update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Photo $photo)
    {
        $photo->delete();

        return response(['message' => 'Deleted']);
    }
}
