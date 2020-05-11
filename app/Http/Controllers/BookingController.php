<?php

namespace BeInMedia\Http\Controllers;

use BeInMedia\Events\NewAppointmentEvent;
use BeInMedia\Http\Requests\AppointmentsRequest;
use BeInMedia\Models\Appointment;
use BeInMedia\Repositories\AppointmentRepository;
use BeInMedia\Repositories\ExpertRepository;
use BeInMedia\Services\TimeSlot;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Class BookingController
 * @package BeInMedia\Http\Controllers
 */
class BookingController extends BaseController
{
    /**
     * @var AppointmentRepository
     */
    public $appointmentRepo;

    /**
     * @var ExpertRepository
     */
    protected $expertRepo;

    /**
     * BookingController constructor.
     * @param AppointmentRepository $appointmentRepo
     * @param ExpertRepository $expertRepo
     */
    public function __construct(AppointmentRepository $appointmentRepo,
                                ExpertRepository $expertRepo)
    {
        parent::__construct();
        $this->appointmentRepo=$appointmentRepo;
        $this->expertRepo=$expertRepo;
    }


    /**
     * Show the application posts index.
     * @return View
     */
    public function index(): View
    {

        return view('appointment.list', [
            'appointments' => $this->appointmentRepo->getAppointmentsList(),
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
        $not_available=$this->appointmentRepo->checkSlotAvailability($request);

        if($not_available){
           $expert=$this->expertRepo->find($request->expert_id);
           $tz=$this->tz;
        }else{
            $appointment = $this->appointmentRepo->create($request->only(['day', 'duration', 'from_time', 'to_time', 'user_id', 'expert_id']));
            $expert=$appointment->expert;
            $tz=null;
        }

        $time_slots=new TimeSlot($expert,$tz,$request->day);
        $slots = $time_slots->getTimeSlots();

        if($not_available){
            return response()->json(['status' => 'warning', 'message' => 'This timeslot is not available, please select another', 'data' => $slots]);
        }else{
            /* Broadcast new slots to all users who book now on same date of same expert */
            event(new NewAppointmentEvent($appointment->toArray(), $slots));
            return response()->json(['status' => 'success', 'message' => 'Your appointment has been saved successfully', 'data' => route('appointments.index')]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param Appointment $appointment
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Appointment $appointment): JsonResponse
    {
        $this->appointmentRepo->destroy($appointment);

        return response()->json(['status' => 'success', 'data' => route('appointments.index')]);
    }

    /**
     * Create new appointment view.
     * @param int $id identifier of expert
     * @return View | JsonResponse
     */
    public function book($id)
    {
        if($id==auth()->id())return abort(404);

        $expert = $this->expertRepo->findOrFail($id);

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

        $expert->start_time = $this->convertFromUtc($expert->start_time, $this->tz);
        $expert->end_time = $this->convertFromUtc($expert->end_time, $this->tz);
        $expert->working_hours = $expert->start_time . ' => ' . $expert->end_time;

        return view('appointment.book', compact('expert', 'slots', 'other'));
    }
}
