<?php
namespace BeInMedia\Repositories;

use BeInMedia\Http\Requests\AppointmentsRequest;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface AppointmentRepository.
 */
interface AppointmentRepository extends BaseRepository
{
    /**
     * @param int|null $user
     * @return Collection
     */
    public function getAppointmentsList(?int $user=null): Collection;

    /**
     * @param AppointmentsRequest $request
     * @return bool
     */
    public function checkSlotAvailability(AppointmentsRequest $request): bool;
}
