<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StampCorrectionRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules()
    {
        return [
            'attendance_id' => ['required', 'integer', 'exists:attendances,id'],
            'target_date' => ['required', 'date'],
            'corrected_start_time' => ['nullable', 'date_format:H:i'],
            'corrected_end_time'   => ['nullable', 'date_format:H:i'],
            'breaks.*.start'       => ['nullable', 'date_format:H:i'],
            'breaks.*.end'         => ['nullable', 'date_format:H:i'],
            'note'                 => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'note.required' => '備考を記入してください。',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $start  = request('corrected_start_time');
            $end    = request('corrected_end_time');
            $breaks = request('breaks', []);

            if ($start && $end && $start > $end) {
                $validator->errors()->add('corrected_start_time', '出勤時間が不適切な値です');
            }

            foreach ($breaks as $i => $break) {
                if (!empty($break['start']) && $end && $break['start'] > $end) {
                    $validator->errors()->add("breaks.$i.start", '休憩時間が不適切な値です');
                }
            }

            foreach ($breaks as $i => $break) {
                if (!empty($break['end']) && $end && $break['end'] > $end) {
                    $validator->errors()->add("breaks.$i.end", '休憩時間もしくは退勤時間が不適切な値です');
                }
            }
        });
    }
}