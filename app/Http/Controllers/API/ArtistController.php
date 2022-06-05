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
            return response(['count' => Artist::all()->count()], 200);
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

            $artists = (is_null($page) || is_null($range))
                ? Artist::all()
                : Artist::where('id', '>', $start_id)->where('id', '<', $end_id)->get();

            return response([ 'artists' => ArtistResource::collection($artists), 'message' => 'Retrieved successfully'], 200);
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
                'avatar' => 'nullable|file|mimes:jpg,bmp,png',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|url|max:255',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            if ($request->file('avatar')) {
                $uploadedFile = $request->file('avatar');
                $ext = $uploadedFile->extension();

                $filename = str_replace('.'.$ext, '', $uploadedFile->getClientOriginalName());
                $date = date('Y-m-d_H-i-s');
                $completeFilename = "{$date}_{$filename}.{$ext}";

                $test = $uploadedFile->storeAs('/uploadedFiles', $completeFilename, ['disk' => 'public']);
                $data['avatar'] = "{$request->getSchemeAndHttpHost()}/storage/uploadedFiles/{$completeFilename}";
            }

            $artist = Artist::create([...$data, "user_id" => $request->user()->id]);

            return response(['artist' => new ArtistResource($artist), 'message' => 'Created successfully'], 201);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function show($artist_id)
    {
        try {
            $artist = Artist::find($artist_id);

            if ($artist === null) {
                return response(['message' => 'Artist not found'], 404);
            }

            return response(['artist' => new ArtistResource($artist), 'message' => 'Retrieved successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $artist_id)
    {
        try {
            $artist = Artist::find($artist_id);

            if ($artist === null) {
                return response(['message' => 'Artist not found'], 404);
            }
            $data = $request->all();

            $validator = Validator::make($data, [
                'avatar' => 'nullable|file|mimes:jpg,bmp,png',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'link' => 'nullable|url|max:255',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            if ($request->file('avatar')) {
                $uploadedFile = $request->file('avatar');
                $ext = $uploadedFile->extension();

                $filename = str_replace('.'.$ext, '', $uploadedFile->getClientOriginalName());
                $date = date('Y-m-d_H-i-s');
                $completeFilename = "{$date}_{$filename}.{$ext}";

                $test = $uploadedFile->storeAs('/uploadedFiles', $completeFilename, ['disk' => 'public']);
                $data['avatar'] = "{$request->getSchemeAndHttpHost()}/storage/uploadedFiles/{$completeFilename}";
            }

            $artist->update([...$data, "user_id" => $request->user()->id]);

            return response(['artist' => new ArtistResource($artist), 'message' => 'Update successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\Response
     */
    public function destroy($artist_id)
    {
        try {
            $artist = Artist::find($artist_id);

            if ($artist === null) {
                return response(['message' => 'Artist not found'], 404);
            }
            $artist->delete();

            return response(['artist' => $artist]);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }
}
