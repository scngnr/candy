@if (is_plugin_active('newsletter'))
    <form class="form-subcriber d-flex newsletter-form {{ $className ?? '' }}" method="post" action="{{ route('public.newsletter.subscribe') }}">
        @csrf
        <input type="email" name="email" placeholder="{{ __('Your email address') }}" />
        @if (setting('enable_captcha') && is_plugin_active('captcha'))
            <div class="col-auto">
                {!! Captcha::display() !!}
            </div>
        @endif
        <button class="btn" type="submit">{{ __('Subscribe') }}</button>
    </form>
@endif
