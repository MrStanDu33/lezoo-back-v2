<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\AlbumResource;

class AlbumController extends Controller
{
    /**
     * Count resource number.
     *
     * @return \Illuminate\Http\Response
     */
    public function count() {
        try {
            return response(['count' => Album::all()->count()], 200);
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
        try {
            $page = filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT);
            $range = filter_input(INPUT_GET, "range", FILTER_SANITIZE_NUMBER_INT);

            $start_id = $range * $page;
            $end_id = $start_id + $range + 1;

            $albums = (is_null($page) || is_null($range))
                ? Album::all()
                : Album::where('id', '>', $start_id)->where('id', '<', $end_id)->get();

            return response([ 'albums' => AlbumResource::collection($albums), 'message' => 'Retrieved successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            $album = Album::create([...$data, "user_id" => $request->user()->id]);

            return response(['album' => new AlbumResource($album), 'message' => 'Created successfully'], 201);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function show($album_id)
    {
        try {
            $album = Album::find($album_id);

            if ($album === null) {
                return response(['message' => 'Album not found'], 404);
            }

            return response(['album' => new AlbumResource($album), 'message' => 'Retrieved successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $album_id)
    {
        try {
            $album = Album::find($album_id);

            if ($album === null) {
                return response(['message' => 'Album not found'], 404);
            }
            $data = $request->all();

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            $album->update([...$data, "user_id" => $request->user()->id]);

            return response(['album' => new AlbumResource($album), 'message' => 'Update successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function destroy($album_id)
    {
        try {
            $album = Album::find($album_id);

            if ($album === null) {
                return response(['message' => 'Album not found'], 404);
            }
            $album->delete();

            return response(['album' => $album]);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }
}
