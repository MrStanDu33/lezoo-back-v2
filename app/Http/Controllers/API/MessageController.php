<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\MessageResource;

class MessageController extends Controller
{
    /**
     * Count resource number.
     *
     * @return \Illuminate\Http\Response
     */
    public function count() {
        try {
            return response(['count' => Message::all()->count()], 200);
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

            $messages = (is_null($page) || is_null($range))
                ? Message::all()
                : Message::where('id', '>', $start_id)->where('id', '<', $end_id)->get();

            return response([ 'messages' => MessageResource::collection($messages), 'message' => 'Retrieved successfully'], 200);
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
                'description' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            $message = Message::create([...$data, "user_id" => $request->user()->id]);

            return response(['message' => new MessageResource($message), 'message' => 'Created successfully'], 201);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show($message_id)
    {
        try {
            $message = Message::find($message_id);

            if ($message === null) {
                return response(['message' => 'Message not found'], 404);
            }

            return response(['message' => new MessageResource($message), 'message' => 'Retrieved successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $message_id)
    {
        try {
            $message = Message::find($message_id);

            if ($message === null) {
                return response(['message' => 'Message not found'], 404);
            }
            $data = $request->all();

            $validator = Validator::make($data, [
                'description' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response(['errors' => $validator->messages()->get('*')], 400);
            }

            $message->update([...$data, "user_id" => $request->user()->id]);

            return response(['message' => new MessageResource($message), 'message' => 'Update successfully'], 200);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy($message_id)
    {
        try {
            $message = Message::find($message_id);

            if ($message === null) {
                return response(['message' => 'Message not found'], 404);
            }
            $message->delete();

            return response(['message' => $message]);
        } catch (\Exception $e) {
            return response(['error' => $e ? $e : 'An error has occurred'], 500);
        }
    }
}
