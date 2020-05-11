<?php
namespace BeInMedia\User\Repositories\Eloquent;

use BeInMedia\Core\Repositories\Eloquent\EloquentBaseRepository;
use BeInMedia\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;


/**
 * Class EloquentUserRepository.
 */
class EloquentUserRepository extends EloquentBaseRepository implements UserRepository
{

    /**
     * @param      $input
     * @return bool
     * @throws \Exception
     */
    public function updatePassword($input)
    {
        $user = $this->find(auth()->id());

        if (Hash::check($input['old_password'], $user->password)) {
            $user->update(['password'=>bcrypt($input['password'])]);
            return true;
        }
        return false;
    }

}
