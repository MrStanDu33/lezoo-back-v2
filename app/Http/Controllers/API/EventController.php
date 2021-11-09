<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\EventResource;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Count resource number.
     *
     * @return \Illuminate\Http\Response
     */
    public function count() {
        return Event::all()->count();
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

        $styles = (is_null($page) || is_null($range)) ? Event::all() : Event::where('id', '>', $start_id)->where('id', '<', $end_id)->get();

        return response([ 'events' => EventResource::collection($styles), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Return next event.
     *
     * @return \Illuminate\Http\Response
     */
    public function next() {
        return Event::where('end_date', '>', Carbon::now())->orderBy('end_date', 'asc')->first();
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $event = Event::create($data);

        return response(['event' => new EventResource($event), 'message' => 'Created successfully'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        return response(['event' => new EventResource($event), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        $event->update($request->all());

        return response(['event' => new EventResource($event), 'message' => 'Update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return response(['message' => 'Deleted']);
    }
}
