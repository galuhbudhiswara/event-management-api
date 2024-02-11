<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationship;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{

    use CanLoadRelationship;

    private array $relations = ['user', 'attendees', 'attendee.user'];

    public function __construct() {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->authorizeResource(Event::class, 'events');
    }

    public function index()
    {
        $query = $this->loadRelationship(Event::query());
    
        return EventResource::collection(
            $query->latest()->paginate()
        );
    }

  

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $event = Event::create([
            ...$request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
            ]),
            'user_id' => $request->user()->id   
        ]);

        return new EventResource($this->loadRelationship($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return new EventResource($this->loadRelationship($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        // if (Gate::denies('update-event', $event)) {
        //     abort(403, 'Not authorized');
        // }

        // $this->authorize('update-event', $event);
        $event->update($request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
        ]),
    );
    return new EventResource($this->loadRelationship($event));      
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event,)
    {
        $event->delete();

        return response(status:204);
    }
}
