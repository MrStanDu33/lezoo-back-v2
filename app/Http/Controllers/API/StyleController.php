<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Style;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\StyleResource;

class StyleController extends Controller
{
    /**
     * Count resource number.
     *
     * @return \Illuminate\Http\Response
     */
    public function count() {
        try {
            return response(['count' => Style::all()->count()], 200);
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

            $styles = (is_null($page) || is_null($range))
                ? Style::all()
                : Style::where('id', '>', $start_id)->where('id', '<', $end_id)->get();

            return response([ 'styles' => StyleResource::collection($styles), 'message' => 'Retrieved successfully'], 200);
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
                'title' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            $style = Style::create([...$data, "user_id" => $request->user()->id]);

            return response(['style' => new StyleResource($style), 'message' => 'Created successfully'], 201);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Style  $style
     * @return \Illuminate\Http\Response
     */
    public function show($style_id)
    {
        try {
            $style = Style::find($style_id);

            if ($style === null) {
                return response(['message' => 'Style not found'], 404);
            }

            return response(['style' => new StyleResource($style), 'message' => 'Retrieved successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Style  $style
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $style_id)
    {
        try {
            $style = Style::find($style_id);

            if ($style === null) {
                return response(['message' => 'Style not found'], 404);
            }
            $data = $request->all();

            $validator = Validator::make($data, [
                'title' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            $style->update([...$data, "user_id" => $request->user()->id]);

            return response(['style' => new StyleResource($style), 'message' => 'Update successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Style  $style
     * @return \Illuminate\Http\Response
     */
    public function destroy($style_id)
    {
        try {
            $style = Style::find($style_id);

            if ($style === null) {
                return response(['message' => 'Style not found'], 404);
            }
            $style->delete();

            return response(['style' => $style]);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }
}
