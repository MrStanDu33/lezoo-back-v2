<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\EventResource;

class EventController extends Controller
{
    /**
     * Count resource number.
     *
     * @return \Illuminate\Http\Response
     */
    public function count() {
        try {
            return response(['count' => Event::all()->count()], 200);
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

            $events = (is_null($page) || is_null($range))
                ? Event::all()
                : Event::where('id', '>', $start_id)->where('id', '<', $end_id)->get();

            return response([ 'events' => EventResource::collection($events), 'message' => 'Retrieved successfully'], 200);
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
                'description' => 'nullable|string',
                'media' => 'nullable|file',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            if ($request->file('media')) {
                $uploadedFile = $request->file('media');
                $ext = $uploadedFile->extension();

                $filename = str_replace('.'.$ext, '', $uploadedFile->getClientOriginalName());
                $date = date('Y-m-d_H-i-s');
                $completeFilename = "{$date}_{$filename}.{$ext}";

                $test = $uploadedFile->storeAs('/uploadedFiles', $completeFilename, ['disk' => 'public']);
                $data['media'] = "{$request->getSchemeAndHttpHost()}/storage/uploadedFiles/{$completeFilename}";
            }

            $event = Event::create([...$data, "user_id" => $request->user()->id]);

            return response(['event' => new EventResource($event), 'message' => 'Created successfully'], 201);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show($event_id)
    {
        try {
            $event = Event::find($event_id);

            if ($event === null) {
                return response(['message' => 'Event not found'], 404);
            }

            return response(['event' => new EventResource($event), 'message' => 'Retrieved successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $event_id)
    {
        try {
            $event = Event::find($event_id);

            if ($event === null) {
                return response(['message' => 'Event not found'], 404);
            }
            $data = $request->all();

            $validator = Validator::make($data, [
                'title' => 'string|max:255',
                'description' => 'nullable|string',
                'media' => 'nullable|file',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            if ($request->file('media')) {
                $uploadedFile = $request->file('media');
                $ext = $uploadedFile->extension();

                $filename = str_replace('.'.$ext, '', $uploadedFile->getClientOriginalName());
                $date = date('Y-m-d_H-i-s');
                $completeFilename = "{$date}_{$filename}.{$ext}";

                $test = $uploadedFile->storeAs('/uploadedFiles', $completeFilename, ['disk' => 'public']);
                $data['media'] = "{$request->getSchemeAndHttpHost()}/storage/uploadedFiles/{$completeFilename}";
            }

            $event->update([...$data, "user_id" => $request->user()->id]);

            return response(['event' => new EventResource($event), 'message' => 'Update successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy($event_id)
    {
        try {
            $event = Event::find($event_id);

            if ($event === null) {
                return response(['message' => 'Event not found'], 404);
            }
            $event->delete();

            return response(['event' => $event]);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }
}
