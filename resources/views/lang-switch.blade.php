<div class="lang-switch">
    @foreach (App\Http\Middleware\SetLocale::LOCALES as $locale)
        @if ($locale === app()->getLocale())
            <span>{{ strtoupper($locale) }}</span>
        @else
            <a href="{{ route('lang', $locale) }}">{{ strtoupper($locale) }}</a>
        @endif
    @endforeach
</div>
