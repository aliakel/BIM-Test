<?php

namespace BeInMedia\Http\Controllers;

use BeInMedia\Events\NewAppointmentEvent;
use BeInMedia\Http\Requests\AppointmentsRequest;
use BeInMedia\Models\Appointment;
use BeInMedia\Models\Expert;
use BeInMedia\Services\TimeSlot;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BookingController extends BaseController
{




    /**
     * Show the application posts index.
     */
    public function index(): View
    {

        return view('appointment.list', [
            'appointments' => Appointment::whereUserId(auth()->id())
                ->orWhere('expert_id', auth()->id())
                ->with(['user', 'expert'])
                ->get(),
            'timezone' => $this->tz
        ]);
    }


    /**
     * Store a newly created resource in storage.
     * @param AppointmentsRequest $request
     * @return JsonResponse
     */
    public function store(AppointmentsRequest $request): JsonResponse
    {
        $appointment = Appointment::create($request->only(['day', 'duration', 'from_time', 'to_time', 'user_id', 'expert_id']));
        $time_slots=new TimeSlot($appointment->expert,null,$appointment->day);
        $slots = $time_slots->getTimeSlots();
        event(new NewAppointmentEvent($appointment->toArray(), $slots));
        return response()->json(['status' => 'success', 'message' => 'Your appointment has been saved successfully', 'data' => route('appointments.index')]);
    }

    /**
     * Remove the specified resource from storage.
     * @param Appointment $appointment
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Appointment $appointment): JsonResponse
    {
        $appointment->delete();

        return response()->json(['status' => 'success', 'data' => route('appointments.index')]);
    }

    /**
     * Create new appointment view.
     * @param int $id identifier of expert
     * @return View | JsonResponse
     */
    public function book($id)
    {
        $expert = Expert::findOrFail($id);
        $time_slots=new TimeSlot($expert,$this->tz,request('date'));
        $slots = $time_slots->getTimeSlots();

        if (request()->isMethod('POST')) {
            return response()->json(['data' => $slots, 'status' => 'success']);
        }
        $other = [
            'user_id' => auth()->id(),
            'expert_id' => $id,
            'visitor_tz' => config('timezones.' . $this->tz, 'UTC'),
            'timezone' => $this->tz
        ];
        return view('appointment.book', compact('expert', 'slots', 'other'));
    }
}
