<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ResidentResource;

class ResidentController extends Controller
{
    /**
     * Count resource number.
     *
     * @return \Illuminate\Http\Response
     */
    public function count() {
        try {
            return response(['count' => Resident::all()->count()], 200);
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

            $residents = (is_null($page) || is_null($range))
                ? Resident::all()
                : Resident::where('id', '>', $start_id)->where('id', '<', $end_id)->get();

            return response([ 'residents' => ResidentResource::collection($residents), 'message' => 'Retrieved successfully'], 200);
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

            $resident = Resident::create([...$data, "user_id" => $request->user()->id]);

            return response(['resident' => new ResidentResource($resident), 'message' => 'Created successfully'], 201);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Resident  $resident
     * @return \Illuminate\Http\Response
     */
    public function show($resident_id)
    {
        try {
            $resident = Resident::find($resident_id);

            if ($resident === null) {
                return response(['message' => 'Resident not found'], 404);
            }

            return response(['resident' => new ResidentResource($resident), 'message' => 'Retrieved successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resident  $resident
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $resident_id)
    {
        try {
            $resident = Resident::find($resident_id);

            if ($resident === null) {
                return response(['message' => 'Resident not found'], 404);
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

            $resident->update([...$data, "user_id" => $request->user()->id]);

            return response(['resident' => new ResidentResource($resident), 'message' => 'Update successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resident  $resident
     * @return \Illuminate\Http\Response
     */
    public function destroy($resident_id)
    {
        try {
            $resident = Resident::find($resident_id);

            if ($resident === null) {
                return response(['message' => 'Resident not found'], 404);
            }
            $resident->delete();

            return response(['resident' => $resident]);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }
}
