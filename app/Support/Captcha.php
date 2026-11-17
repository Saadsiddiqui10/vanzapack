<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Form CAPTCHA.
 *
 * Uses Google reCAPTCHA v2 ("I'm not a robot" checkbox) when RECAPTCHA_SITE_KEY /
 * RECAPTCHA_SECRET_KEY are set, otherwise a simple "What is 4 + 7?" question kept in
 * the session — so forms are protected straight away and upgrade automatically once
 * the keys are added.
 */
class Captcha
{
    private const SESSION_KEY = 'captcha_answer';

    public static function usesRecaptcha(): bool
    {
        return filled(config('services.recaptcha.site_key')) && filled(config('services.recaptcha.secret_key'));
    }

    public static function siteKey(): ?string
    {
        return config('services.recaptcha.site_key');
    }

    /** New math question for the fallback; the answer is stored in the session. */
    public static function mathQuestion(): string
    {
        [$a, $b] = [random_int(2, 9), random_int(2, 9)];
        session()->put(self::SESSION_KEY, $a + $b);

        return "What is {$a} + {$b}?";
    }

    public static function passes(Request $request): bool
    {
        if (self::usesRecaptcha()) {
            $token = (string) $request->input('g-recaptcha-response');
            if ($token === '') {
                return false;
            }

            try {
                $result = Http::asForm()->timeout(10)->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $token,
                ])->json();

                return (bool) ($result['success'] ?? false);
            } catch (Throwable $e) {
                report($e);

                return false;
            }
        }

        // Math fallback: one try per question.
        $expected = session()->pull(self::SESSION_KEY);

        return $expected !== null && trim((string) $request->input('captcha_answer')) === (string) $expected;
    }
}
