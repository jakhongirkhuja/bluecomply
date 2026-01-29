<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class NotificationSettingRequest extends FormRequest
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
        $types = [
            'compliance',
            'system',
            'driver',
            'test',
            'employment',
            'roadside',
            'random_pool',
            'billing',
        ];

        $channels = ['email','in_app','both'];
        return [
            'notifications' => ['required', 'array'],
            'notifications.*' => ['required', 'array'],
            'notifications.*.*' => ['required', 'string', 'in:' . implode(',', $channels)],
            'notifications.*' => 'array',
            'notifications.*' => function ($attribute, $value, $fail) use ($types) {
                $type = explode('.', $attribute)[1]; // extract type key
                if (!in_array($type, $types)) {
                    $fail("The notification type '{$type}' is invalid.");
                }
            },
        ];
    }
    public function messages()
    {
        return [
            'notifications.required' => 'Notification settings are required.',
            'notifications.*.*.in' => 'The channel must be either email or in-app.',
        ];
    }
}
