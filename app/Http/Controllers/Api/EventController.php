<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use Illuminate\Http\Request;
use App\Models\Event;
class EventController extends Controller
{
   use CanLoadRelationships;
    
    public function index()
    {
        $relation =['user', 'attendees', 'attendees.user'];
        $query = $this->loadRelationships(Event::query(), $relation);

        return EventResource::collection(
           $query->latest()->paginate()
        );
    }
    
     protected function shouldIncludeRelation(string $relation): bool
     {
        $include = request()->query('include', '');
        if (empty($include)) {
            return false;
        }

        $relations = array_map('trim', explode(',', $include));
        return in_array($relation, $relations);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
        ]);

        $validated['user_id'] = 1;
        $event = Event::create($validated);
        return new EventResource($event->load('user', 'attendees'));
    }


    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('user', 'attendees');
        return new EventResource($event);    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
         $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after_or_equal:start_time',
        ]);

        $event->update($validated);
        $event->load('user', 'attendees');
        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return response(status: 204);
    }
}