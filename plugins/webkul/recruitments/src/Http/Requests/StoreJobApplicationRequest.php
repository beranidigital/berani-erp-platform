<?php

namespace Webkul\Recruitment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'email', 'max:255'],
            'phone'               => ['nullable', 'string', 'max:50'],
            'linkedin_profile'    => ['nullable', 'url', 'max:255'],
            'availability_date'   => ['nullable', 'date', 'after_or_equal:today'],
            'salary_expectation'  => ['nullable', 'numeric', 'min:0'],
            'cover_letter'        => ['nullable', 'string'],
            'notes'               => ['nullable', 'string'],
            'resume'              => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'               => $this->string('name')->trim()->value(),
            'email'              => $this->string('email')->trim()->value(),
            'phone'              => $this->string('phone')->trim()->value(),
            'linkedin_profile'   => $this->string('linkedin_profile')->trim()->value(),
            'cover_letter'       => $this->string('cover_letter')->value(),
            'notes'              => $this->string('notes')->value(),
        ]);
    }
}
