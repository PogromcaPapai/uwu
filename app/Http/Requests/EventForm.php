<?php

namespace App\Http\Requests;

use App\Models\Place;
use App\Models\User;
use DateTime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EventForm extends FormRequest
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
            'title' => ['required', 'max:255'],
            'start' => ['required'],
            'end' => ['required'],
            'description' => [],
            'place' => [],
            'invites' => []
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Program sprawdza, czy wydarzenie kończy się po swoim początku
            if (new DateTime($this->start) > new DateTime($this->end)) {
                $validator->errors()->add('end', 'Wydarzenie musi zakończyć się po rozpoczęciu.');
            }

            // Test poprawności zaproszeń
            $mails = explode(", ", $this->invites);
            $plucked = User::pluck('email');
            $user = User::where('id', '=', Auth::id())->first();
            foreach ($mails as $mail) {
                if ($mail == $user->mail) {
                    $validator->errors()->add('invites', 'Nie możesz zapraszać samego siebie');
                }
                if ($mail != "" && !$plucked->contains($mail)) {
                    $validator->errors()->add('invites', 'Możesz zapraszać tylko istniejących użytkowników');
                }
                
            }
            
        });
    }
}