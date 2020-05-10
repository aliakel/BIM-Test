<?php

namespace BeInMedia\Http\Controllers;

use BeInMedia\Models\Expert;
use Illuminate\View\View;

class ExpertController extends BaseController
{
    /**
     * Get experts list.
     * @return View
     */
    public function index():View
    {
        $experts = Expert::all();
        /* Convert start and end working date from utc to user timezone */
        $experts->map(function ($expert) {
            $expert->start_time = $this->convertFromUtc($expert->start_time, $this->tz);
            $expert->end_time = $this->convertFromUtc($expert->end_time, $this->tz);
            $expert->working_hours = $expert->start_time . ' => ' . $expert->end_time;
        });
        return view('experts.list', compact('experts'));
    }

}
