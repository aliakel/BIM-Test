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
            'from_time' => 'required',
            'to_time' => 'required',
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

        /*Merge new start_time and end_time (in UTC) with other user inputs */
        $this->merge([
            'day' => Carbon::parse($this->input('from_time'))->format('Y-m-d')
        ]);

    }
}
