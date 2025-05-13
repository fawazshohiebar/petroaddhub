<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Statamic\Events\FormSubmitted;

class CheckTurnstileCaptcha
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FormSubmitted $event): void
    {
        $token = $event->submission->get('turnstile_token');

        $validator = Validator::make([
            'turnstile_token' => $token
        ],[
            'turnstile_token' => ['required', new \App\Rules\Turnstile]
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'turnstile_token' => ['You did not prove that you are not a robot.']
            ]);
        }
    }
}
