<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCandidate() ?? false;
    }

    public function rules(): array
    {
        return [
            'job_id' => ['required', 'integer', 'exists:job_posts,id'],
        ];
    }
}