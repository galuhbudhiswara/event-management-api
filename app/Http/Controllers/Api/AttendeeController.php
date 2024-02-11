<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationship;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AttendeeController extends Controller
{

    use CanLoadRelationship, HasApiTokens, HasFactory, Notifiable;
    private array $relations = ['user'];

    public function __construct() {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'update']);
    }

    public function index(Event $event)
    {
        $attendees = $this->loadRelationships(
            $event->attendees()->latest()
        );

        return AttendeeResource::collection(
            $attendees->paginate()
        );
    }

    public function store(Request $request, Event $event)
    {
        $attendee = $event->attendees()->create([
            'user_id' => 1
        ]);
        $attendee = $this->loadRelationships(
            $event->attendees()->create([
                'user_id' => 1
            ])
        );

        return new AttendeeResource($attendee);
    }

    public function show(Event $event, Attendee $attendee)
    {   
        return new AttendeeResource($this->loadRelationship($attendee));
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(Event $event, Attendee $attendee)
    {
        $this->authorize('delete-attende', [$event, $attendee]);
        $attendee->delete();

        return response(status: 204);
    }
}
