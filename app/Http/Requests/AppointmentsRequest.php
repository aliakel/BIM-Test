<?php

namespace BeInMedia\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AppointmentsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'from' => 'required',
            'to' => 'required',
            'day' => 'required|date',
            'duration' => 'required|numeric',
            'user_id' => ['required', 'exists:users,id'],
            'expert_id' => ['required', 'exists:experts,id'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        /*Parse user book_date to get timezone offset*/
        $book_date = Carbon::parse($this->input('day'));
        $timezone_offset = $book_date->getOffsetString();

        /*Prepare user start_time and get_time*/
        $from_time_input = Carbon::parse($this->input('from'))->format('H:i:s');
        $to_time_input = Carbon::parse($this->input('to'))->format('H:i:s');

        /*Set start_time' and end_time' timezone to UTC */
        $to = Carbon::parse($book_date->format('Y-m-d') . ' ' . $to_time_input . ' ' . $timezone_offset)->setTimezone('UTC');
        $from = Carbon::parse($book_date->format('Y-m-d') . ' ' . $from_time_input . ' ' . $timezone_offset)->setTimezone('UTC');
        /*Merge new start_time and end_time (in UTC) with other user inputs */
        $this->merge([
            'day' => $from->format('Y-m-d')
        ]);
        $this->merge([
            'to_time' => $to
        ]);
        $this->merge([
            'from_time' => $from
        ]);

    }
}
