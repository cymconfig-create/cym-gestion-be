<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Event\EventService;
use App\Services\Event\CreateEvent\CreateEventService;
use App\Services\Event\UpdateEvent\UpdateEventService;
use App\Services\Event\DeleteEvent\DeleteEventService;

class EventController extends Controller
{
    private $createEventService;
    private $updateEventService;
    private $deleteEventService;
    private $eventService;

    public function __construct(
        EventService $eventService,
        CreateEventService $createEventService,
        UpdateEventService $updateEventService,
        DeleteEventService $deleteEventService
    ) {
        $this->createEventService = $createEventService;
        $this->updateEventService = $updateEventService;
        $this->deleteEventService = $deleteEventService;
        $this->eventService = $eventService;
    }

    public function all()
    {
        return $this->eventService->all();
    }

    public function find($event_id)
    {
        return $this->eventService->find($event_id);
    }

    public function findBy($colum, $event_id)
    {
        return $this->eventService->findBy($colum, $event_id);
    }

    public function findByAll($colum, $value)
    {
        return $this->eventService->findByAll($colum, $value);
    }

    public function create(Request $request)
    {
        return $this->createEventService->create($request);
    }

    public function update(Request $request, $event_id)
    {
        return $this->updateEventService->update($request, $event_id);
    }

    public function delete($event_id)
    {
        return $this->deleteEventService->delete($event_id);
    }
}
