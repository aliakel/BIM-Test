<?php

namespace BeInMedia\Repositories\Eloquent;

use BeInMedia\Http\Requests\AppointmentsRequest;
use BeInMedia\Models\Appointment;
use BeInMedia\Repositories\AppointmentRepository;
use Illuminate\Database\Eloquent\Collection;


/**
 * Class EloquentAppointmentRepository.
 */
class EloquentAppointmentRepository extends EloquentBaseRepository implements AppointmentRepository
{

    /**
     * AppointmentRepository constructor.
     * @param Appointment $model
     */
    public function __construct(Appointment $model)
    {
        parent::__construct($model);
    }

    /**
     * @param int|null $user
     * @return Collection
     */
    public function getAppointmentsList(?int $user=null): Collection
    {
        $user = $user ?? auth()->id();
        return $this->model->whereUserId($user)
                    ->orWhere('expert_id', $user)
                    ->with(['user', 'expert'])->get();
    }

    /**
     * Check if slot is available or not
     * @param AppointmentsRequest $request
     * @return bool
     */
    public function checkSlotAvailability(AppointmentsRequest $request): bool
    {
        $from=$request->from_time;
        $to=$request->to_time;
        $result=$this->getByAttributes(['expert_id'=>$request->expert_id,'day'=>$request->day]);
        for ($i=0;$i<count($result);$i++){
            if($from==$result[$i]->from_time && $to==$result[$i]->to_time){
                return true;
                break;
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
