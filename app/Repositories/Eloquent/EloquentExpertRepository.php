<?php
namespace BeInMedia\Repositories\Eloquent;

use BeInMedia\Models\Expert;
use BeInMedia\Repositories\ExpertRepository;


/**
 * Class EloquentExpertRepository.
 */
class EloquentExpertRepository extends EloquentBaseRepository implements ExpertRepository
{
    /*
     *
     */
    public function __construct(Expert $model)
    {
        parent::__construct($model);
    }
}
