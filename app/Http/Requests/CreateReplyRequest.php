<?php

namespace App\Http\Requests;

use App\Exceptions\ThrottleException;
use App\Reply;
use App\Rules\SpamFree;
use App\Thread;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateReplyRequest extends FormRequest
{
    protected function failedAuthorization()
    {
        throw new ThrottleException(
            'You are posting too frequently. Please take a break :)'
        );
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     * ver
     */
    public function authorize()
    {
        return Gate::allows('create', new Reply);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => ['required', new SpamFree]
        ];
    }
}
