{{-- Security check: Google reCAPTCHA ("I'm not a robot") when configured, otherwise a simple math question --}}
@if(\App\Support\Captcha::usesRecaptcha())
    <div>
        <div class="g-recaptcha" data-sitekey="{{ \App\Support\Captcha::siteKey() }}"></div>
        <x-input-error :messages="$errors->get('captcha')" class="mt-1" />
    </div>
    @once
        @push('scripts')
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endpush
    @endonce
@else
    <div>
        <label class="label" for="captcha_answer">Security check: {{ \App\Support\Captcha::mathQuestion() }}</label>
        <input id="captcha_answer" name="captcha_answer" inputmode="numeric" autocomplete="off" required
               class="field w-32" placeholder="Answer">
        <x-input-error :messages="$errors->get('captcha')" class="mt-1" />
    </div>
@endif
