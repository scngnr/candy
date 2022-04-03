<div class="hero-slider-1 dot-style-1 dot-style-1-position-1 {{ $class ?? ''}}">
    @foreach($sliders as $slider)
        <div class="single-hero-slider single-animation-wrap {{ $itemClass ?? ''}}" style="background-image: url({{ RvMedia::getImageUrl($slider->image, null, false, RvMedia::getDefaultImage()) }}); @if (!$loop->first) display: none; @endif">
            {!! Theme::partial('shortcodes.sliders.content', compact('slider', 'shortcode')) !!}
        </div>
    @endforeach
</div>
<div class="slider-arrow hero-slider-1-arrow"></div>
