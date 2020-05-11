<?php

namespace BeInMedia\Http\Controllers;

use BeInMedia\Repositories\ExpertRepository;
use Illuminate\View\View;

/**
 * Class ExpertController
 * @package BeInMedia\Http\Controllers
 */
class ExpertController extends BaseController
{
    /**
     * @var ExpertRepository
     */
    protected $expertRepo;

    /**
     * ExpertController constructor.
     * @param ExpertRepository $expertRepo
     */
    public function __construct(ExpertRepository $expertRepo)
    {
        parent::__construct();
        $this->expertRepo=$expertRepo;
    }

    /**
     * Get experts list.
     * @return View
     */
    public function index():View
    {
        $experts = $this->expertRepo->all();
        /* Convert start and end working date from utc to user timezone */
        $experts->map(function ($expert) {
            $expert->start_time = $this->convertFromUtc($expert->start_time, $this->tz);
            $expert->end_time = $this->convertFromUtc($expert->end_time, $this->tz);
            $expert->working_hours = $expert->start_time . ' => ' . $expert->end_time;
        });
        return view('experts.list', compact('experts'));
    }

}
