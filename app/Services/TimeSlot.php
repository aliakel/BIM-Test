<?php

namespace BeInMedia\Services;

use BeInMedia\Http\Requests\AppointmentsRequest;
use BeInMedia\Models\Appointment;
use BeInMedia\Models\Expert;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TimeSlot
{
    protected $expert;
    protected $tz;
    protected $day;

    public function __construct($expert=null, $tz = null, $day = null)
    {
        $this->expert = $expert;
        $this->tz = $tz ? $tz : 'UTC';
        $this->day = $day;
    }

    /*
     * get available slots  for all duration
     * @return array
     */
    public function getTimeSlots()
    {
        $slots['15'] = $this->availableTimeSlots(15);
        $slots['30'] = $this->availableTimeSlots(30);
        $slots['45'] = $this->availableTimeSlots(45);
        $slots['60'] = $this->availableTimeSlots(60);
        return $slots;
    }

    /*
     * Calculate available slots in a specific day and for specific duration
     * @param int $duration
     * @return array
     */
    protected function availableTimeSlots($duration)
    {
        $available_slots = [];

        /* get user input day in utc*/
        $date = $this->day ? $this->setTimeZone($this->day, $this->tz, 'Y-m-d') :$this->setTimeZone(Carbon::now(), $this->tz, 'Y-m-d');
        /* get expert working start_time and end_time in visitor timezone*/
        $start_time = $this->setTimeZone($date . ' ' . $this->expert->start_time, $this->tz);
        $end_time = $this->setTimeZone($date . ' ' . $this->expert->end_time, $this->tz);

        /* Check if user input day equal current day*/
        if ($date == $this->setTimeZone(now(), $this->tz, 'Y-m-d')) {
            $now = Carbon::parse($this->setTimeZone(now(), $this->tz));
            /* Check current time after the end of expert working time return empty array of timeslots*/
            if ($now->gt($end_time)) {
                return $available_slots;
            }
            /* Check if the current time is after the beginning of expert working time
             * if true, set start time to current time plus 10 minutes (this for lost time when user choose book setting)
             */
            if ($now->gt($start_time->addMinutes(10))) {
                $start_time = $now;
            }
        }
        /* get all appointments which have been booked in user input day*/
        $appointments = Appointment::where('day', $date)
            ->whereExpertId($this->expert->id)
            ->OrderBy('from_time', 'asc')->get()->toArray();
        $count = count($appointments);
        /*
         * We will calculate available time slots as the following steps:
         * 1- iterate through $appointments
         * 2- calculate available slots between every tow Consecutive appointments
         * 3- if we at first appointment, we calculate slots between start expert working time and start time of this appointment
         * 4- if we at last appointment, we calculate slots between  end time of this appointment and end of expert working time
         * 5 else  we calculate slots between tow Consecutive appointments
         */
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $start_form = $this->setTimeZone($appointments[$i]['from_time'], $this->tz);
                $end_from = $this->setTimeZone($appointments[$i]['to_time'], $this->tz);
                /* we at last appointment */
                if ($i == ($count - 1) && ($end_from->lt($end_time))) {
                    $available_slots = $this->calculateRanges($end_from, $end_time, $duration, $available_slots);
                } /* we at last appointment */
                elseif ($i == 0 && $start_form->gt($start_time)) {
                    $available_slots = $this->calculateRanges($start_time, $start_form, $duration, $available_slots);
                } /* we between tow Consecutive appointments  */
                else {
                    $start_form = $this->setTimeZone($appointments[$i]['to_time'], $this->tz);
                    if (isset($appointments[$i + 1])) {
                        $end_from = $this->setTimeZone($appointments[$i + 1]['from_time'], $this->tz);
                    }
                    if ($end_from->gt($start_form)) {

                        $available_slots = $this->calculateRanges($start_form, $end_from, $duration, $available_slots);
                    }
                }
            }
        } /*
         * If there aren't any appointments in user input day
         * we calculate available slots between start expert working time and end expert working  time
         */
        else {

            $available_slots = $this->calculateRanges($start_time, $end_time, $duration, $available_slots);
        }
        return $available_slots;
    }

    /*
    * Calculate slots in a specific range of time
    * 1- we calculate different between start and end of range in minutes
    * 2- We divide $duration by result in step 1 then We round the result to the nearest smallest integer
    * 3- loop through result by step 2 and set slot start and end in format g:i A
    * @param Carbon $start
    * @param Carbon $end
    * @param int $duration
    * @param array $slots
    * @return array
    */

    protected function setTimeZone($date, $time_zone = null, $format = null)
    {
        $result = Carbon::parse($date);
        if ($time_zone) {
            $result = $result->setTimezone($time_zone);
        }
        if ($format) {
            $result = $result->format($format);
        }
        return $result;
    }

    /*
    * set time zone and format for specific date
    * @param $date
    * @param $time_zone
    * @param $format
    * @return mixed
    */

    protected function calculateRanges(Carbon $start, Carbon $end, $duration, & $slots)
    {
        $diff_in_minutes = $end->diffInMinutes($start);
        $num_of_slots = (int)floor($diff_in_minutes / $duration);

        if ($num_of_slots > 0) {

            for ($i = 0; $i < $num_of_slots; $i++) {
                $slots[] = [
                    'start' => $start->format('g:i A'),
                    'end' => $start->addMinutes($duration)->format('g:i A')
                ];

            }
        }
        return $slots;
    }

    public function checkIfSlotAvailable(AppointmentsRequest $request){
        $appointments = Appointment::where('day', $request->day)
            ->whereExpertId($request->expert_id)
            ->OrderBy('from_time', 'asc')->get()->toArray();

        $count=count($appointments);
        for ($i = 0; $i < $count; $i++) {
            $from=Carbon::parse($appointments[$i]['from_time']);
            $to=Carbon::parse($appointments[$i]['to_time']);
            if(Carbon::parse($request->from_time)->between($from, $to)
                || Carbon::parse($request->to_time)->between($from, $to)
            ){
                return false;
                break;
            }
        }
        return true;
    }
    /*
     * Check if slot is available or not
     * @param $request AppointmentsRequest
     * @return boolean
     */
    public function check(AppointmentsRequest $request){
        $from=$request->from_time;
        $to=$request->to_time;
        $result=Appointment::where('expert_id',$request->expert_id)
            ->where('day',$request->day)->get();
        for ($i=0;$i<count($result);$i++){
            if($from==$result[$i]->from_time && $to==$result[$i]->to_time){
                return true;break;
            }else{
                if($from>=$result[$i]->from_time && $to<=$result[$i]->to_time||
                    $from>=$result[$i]->to_time){
                    continue;
                }
            }

        }
        return false;
    }


}
