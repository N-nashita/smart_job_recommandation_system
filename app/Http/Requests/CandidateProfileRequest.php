<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CandidateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCandidate() ?? false;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email,'.$this->user()->id],
            'phone'      => ['nullable', 'string', 'max:20', 'unique:users,phone,'.$this->user()->id],
            'category'   => ['nullable', 'string', 'max:255'],
            'experience' => ['nullable', 'string', 'max:30'],
            'degree'     => ['nullable', 'string', 'max:50'],
            'institution'=> ['nullable', 'string', 'max:255'],
            'major'      => ['nullable', 'string', 'max:255'],
            'grad_year'  => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'cgpa'       => ['nullable', 'numeric', 'min:0', 'max:9.99'],

            // CV fields
            'summary'        => ['nullable', 'string', 'max:1000'],
            'location'       => ['nullable', 'string', 'max:150'],
            'linkedin'       => ['nullable', 'url', 'max:255'],
            'github'         => ['nullable', 'url', 'max:255'],
            'portfolio'      => ['nullable', 'url', 'max:255'],
            'languages'      => ['nullable', 'string', 'max:200'],
            'certifications' => ['nullable', 'string', 'max:1000'],
            'skills'         => ['nullable', 'string', 'max:500'],

            'experiences'               => ['nullable', 'array', 'max:10'],
            'experiences.*.company'     => ['nullable', 'string', 'max:120'],
            'experiences.*.title'       => ['nullable', 'string', 'max:120'],
            'experiences.*.start'       => ['nullable', 'string', 'max:30'],
            'experiences.*.end'         => ['nullable', 'string', 'max:30'],
            'experiences.*.description' => ['nullable', 'string', 'max:600'],

            'projects'                  => ['nullable', 'array', 'max:10'],
            'projects.*.title'          => ['nullable', 'string', 'max:120'],
            'projects.*.link'           => ['nullable', 'string', 'max:255', 'starts_with:http://,https://'],
            'projects.*.description'    => ['nullable', 'string', 'max:600'],

            'educations'               => ['nullable', 'array', 'max:10'],
            'educations.*.degree'      => ['nullable', 'string', 'max:120'],
            'educations.*.institution' => ['nullable', 'string', 'max:150'],
            'educations.*.major'       => ['nullable', 'string', 'max:120'],
            'educations.*.year'        => ['nullable', 'string', 'max:20'],
            'educations.*.result'      => ['nullable', 'string', 'max:40'],

            'photo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ];
    }
}