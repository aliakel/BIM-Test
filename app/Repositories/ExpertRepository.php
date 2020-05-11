<?php
namespace BeInMedia\User\Repositories;

use BeInMedia\Core\Repositories\BaseRepository;
use Illuminate\Http\Request;

/**
 * Interface UserRepository.
 */
interface UserRepository extends BaseRepository
{

    /**
     * @param       $id
     * @param Request $input
     * @return mixed
     */
    public function update($id, $input);

    /**
     * @param      $input
     * @return bool
     */
    public function updatePassword($input);

}
