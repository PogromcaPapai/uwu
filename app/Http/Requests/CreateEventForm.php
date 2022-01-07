<?php

namespace App\Http\Requests;

use App\Models\Place;
use App\Models\User;
use DateTime;
use Illuminate\Foundation\Http\FormRequest;

class CreateEventForm extends EventForm
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required', 'max:255'],
            'start' => ['required'],
            'end' => ['required'],
            'description' => [],
            'place' => ['required'],
            'invites' => []
        ];
    }
}