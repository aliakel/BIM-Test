<?php

namespace BeInMedia\Services;

use BeInMedia\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TimeSlot
 * @package BeInMedia\Services
 */
class TimeSlot
{
    /**
     * @var Model
     */
    protected $expert;

    /**
     * @var string|null
     */
    protected $tz;

    /**
     * @var string|null
     */
    protected $day;

    /**
     * TimeSlot constructor.
     * @param Model $expert
     * @param string|null $tz
     * @param string|null $day
     */
    public function __construct(Model $expert, ?string $tz = null, ?string $day = null)
    {
        $this->expert = $expert;
        $this->tz = $tz ? $tz : 'UTC';
        $this->day = $day;
    }

    /*
     * get available slots  for all duration
     * @return array
     */
    public function getTimeSlots(): array
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
    protected function availableTimeSlots($duration): array
    {
        $available_slots = [];

        /* get user input day in utc*/
        $date = $this->day ? $this->setTimeZone($this->day, $this->tz, 'Y-m-d') : $this->setTimeZone(Carbon::now(), $this->tz, 'Y-m-d');
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
             * if true, set start time to current time plus 1 hour and set minute and seconds to zero (ex: if now is 2:15, working start time will be 3:00)
             */
            if ($now->gt($start_time)) {
                $start_time = $now->addHour()->minute(0)->second(0);
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


    /**
     * Calculate slots in a specific range of time
     * 1- we calculate different between start and end of range in minutes
     * 2- We divide $duration by result in step 1 then We round the result to the nearest smallest integer
     * 3- loop through result by step 2 and set slot start and end in format g:i A
     * @param $date
     * @param string|null $time_zone
     * @param string|null $format
     * @return Carbon|string
     */
    protected function setTimeZone($date, ?string $time_zone = null, ?string $format = null)
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

    /**
     * Set time zone and format for specific date
     * @param Carbon $start
     * @param Carbon $end
     * @param int $duration
     * @param array $slots
     * @return array
     */

    protected function calculateRanges(Carbon $start, Carbon $end, int $duration, array & $slots): array
    {
        $diff_in_minutes = $end->diffInMinutes($start);
        $num_of_slots = (int)floor($diff_in_minutes / $duration);

        if ($num_of_slots > 0) {

            for ($i = 0; $i < $num_of_slots; $i++) {
                $copy = $start->copy();
                $slot_copy = $start->copy();
                $slots[] = [
                    'start' => $copy->format('Y-m-d\TH:i:s.uP '),
                    'start_slot' => $slot_copy->format('g:iA'),
                    'end_slot' => $slot_copy->addMinutes($duration)->format('g:iA'),
                    'end' => $copy->addMinutes($duration)->format('Y-m-d\TH:i:s.uP '),
                    'slot' => $start->format('g:iA') . ' - ' . $start->addMinutes($duration)->format('g:iA')
                ];

            }
        }
        return $slots;
    }

}
