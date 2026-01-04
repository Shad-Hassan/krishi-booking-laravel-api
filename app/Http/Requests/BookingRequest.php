<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Applicant Information
            'applicant_name_en' => ['required', 'string', 'max:255'],
            'applicant_name_bn' => ['nullable', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'spouse_name' => ['nullable', 'string', 'max:255'],

            // Present Address
            'present_address' => ['required', 'array'],
            'present_address.house_vill' => ['required', 'string', 'max:255'],
            'present_address.road_block' => ['nullable', 'string', 'max:255'],
            'present_address.post' => ['required', 'string', 'max:255'],
            'present_address.thana' => ['required', 'string', 'max:255'],
            'present_address.district' => ['required', 'string', 'max:255'],

            // Permanent Address
            'permanent_address' => ['required', 'array'],
            'permanent_address.house_vill' => ['required', 'string', 'max:255'],
            'permanent_address.road_block' => ['nullable', 'string', 'max:255'],
            'permanent_address.post' => ['required', 'string', 'max:255'],
            'permanent_address.thana' => ['required', 'string', 'max:255'],
            'permanent_address.district' => ['required', 'string', 'max:255'],

            'nationality' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'nid_passport' => [
                'required',
                'string',
                'max:50',
                Rule::unique('bookings', 'nid_passport')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'marriage_date' => ['nullable', 'date', 'before_or_equal:today'],
            'mobile_1' => ['required', 'string', 'max:20'],
            'mobile_2' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'tin' => ['nullable', 'string', 'max:50'],
            'profession' => ['required', Rule::in(['private_service', 'govt_service', 'business', 'others'])],
            'designation_address' => ['nullable', 'string', 'max:500'],

            // Nominee Information
            'nominee_name' => ['required', 'string', 'max:255'],
            'nominee_address' => ['required', 'array'],
            'nominee_address.house_vill' => ['required', 'string', 'max:255'],
            'nominee_address.road_block' => ['nullable', 'string', 'max:255'],
            'nominee_address.post' => ['required', 'string', 'max:255'],
            'nominee_address.thana' => ['required', 'string', 'max:255'],
            'nominee_address.district' => ['required', 'string', 'max:255'],
            'nominee_relation' => ['required', Rule::in(['spouse', 'son', 'daughter', 'father', 'mother', 'others'])],
            'nominee_nid' => ['nullable', 'string', 'max:50'],
            'nominee_dob' => ['nullable', 'date', 'before:today'],
            'nominee_mobile_1' => ['nullable', 'string', 'max:20'],
            'nominee_mobile_2' => ['nullable', 'string', 'max:20'],

            // Share Information
            'no_of_shares' => ['required', 'integer', 'min:1'],
            'category_ownership' => ['nullable', 'string', 'max:255'],
            'payment_mode' => ['required', Rule::in(['installment', 'at_a_time'])],

            // File Uploads
            'applicant_photo' => ['required', 'image', 'max:2048'], // max 2MB
            'nominee_photo' => ['nullable', 'image', 'max:2048'],
            'signature' => ['required', 'string'], // base64 encoded image

            // Terms
            'agreed_to_terms' => ['required', 'accepted'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nid_passport.unique' => 'You already have a booking with this NID/Passport number.',
            'applicant_photo.max' => 'Applicant photo must not exceed 2MB.',
            'nominee_photo.max' => 'Nominee photo must not exceed 2MB.',
            'agreed_to_terms.accepted' => 'You must agree to the terms and conditions.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            'no_of_shares.min' => 'You must purchase at least 1 share.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'applicant_name_en' => 'applicant name (English)',
            'applicant_name_bn' => 'applicant name (Bangla)',
            'present_address.house_vill' => 'present address house/village',
            'present_address.post' => 'present address post office',
            'present_address.thana' => 'present address thana',
            'present_address.district' => 'present address district',
            'permanent_address.house_vill' => 'permanent address house/village',
            'permanent_address.post' => 'permanent address post office',
            'permanent_address.thana' => 'permanent address thana',
            'permanent_address.district' => 'permanent address district',
            'nominee_address.house_vill' => 'nominee address house/village',
            'nominee_address.post' => 'nominee address post office',
            'nominee_address.thana' => 'nominee address thana',
            'nominee_address.district' => 'nominee address district',
        ];
    }
}
