<?php

namespace App\Http\Requests\Subscriptions;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSubscriptionRequest extends FormRequest
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
            'subscriber_id' => ['required', 'integer', Rule::exists(User::class, 'id')],
            'target_id' => ['required', 'integer', Rule::exists(User::class, 'id')],
        ];
    }

    public function getSubscriberId(): int
    {
        return $this->validated('subscriber_id');
    }

    public function getTargetId(): int
    {
        return $this->validated('target_id');
    }
}
