<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ArtistResource;

class ArtistController extends Controller
{
    /**
     * Count resource number.
     *
     * @return \Illuminate\Http\Response
     */
    public function count() {
        try {
            $page = filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT);
            $range = filter_input(INPUT_GET, "range", FILTER_SANITIZE_NUMBER_INT);

            $start_id = $range * $page;
            $end_id = $start_id + $range + 1;

            $artists = (is_null($page) || is_null($range))
                ? Artist::all()
                : Artist::where('id', '>', $start_id)->where('id', '<', $end_id)->get();

            return response([ 'artists' => ArtistResource::collection($artists)], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
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

        $artists = (is_null($page) || is_null($range)) ? Artist::all() : Artist::where('id', '>', $start_id)->where('id', '<', $end_id)->get();

        return response([ 'artists' => ArtistResource::collection($artists), 'message' => 'Retrieved successfully'], 200);
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
            'photo_id' => 'nullable|exists:photos',
            'name' => 'required|string|max:255',
            'social_link' => 'nullable|url|max:255',
            'label' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $artist = Artist::create($data);

        return response(['artist' => new ArtistResource($artist), 'message' => 'Created successfully'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function show(Artist $artist)
    {
        return response(['artist' => new ArtistResource($artist), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Artist $artist)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'social_link' => 'nullable|url|max:255',
            'label' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $artist->update($data);

        return response(['artist' => new ArtistResource($artist), 'message' => 'Update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Artist $artist)
    {
        $artist->delete();

        return response(['message' => 'Deleted']);
    }
}
