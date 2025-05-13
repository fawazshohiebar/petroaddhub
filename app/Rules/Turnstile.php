<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Turnstile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('turnstile.secret'),
            'response' => $value,
            'remoteip' => request()->ip(),
            'idempotency_key' => request()->ip() . '-' . now()->timestamp,
        ]);

        if (! $response->json('success')) {
            $fail('You did not prove that you are not a robot.');
        }
        info('Turnstile response', $response->json());
    }
}
