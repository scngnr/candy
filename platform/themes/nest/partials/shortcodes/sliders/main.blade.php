@if (is_plugin_active('simple-slider') && count($sliders) > 0 &&
    $sliders->loadMissing('metadata') && $slider->loadMissing('metadata'))
    @php
        $style = $slider->getMetaData('simple_slider_style', true);
    @endphp
    @if ($style == 'style-3')
        <section class="home-slider position-relative mt-30">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="position-relative">
                            <div class="hero-slider-1 style-3 dot-style-1 dot-style-1-position-1">
                                @foreach($sliders as $slider)
                                    <div class="single-hero-slider single-animation-wrap" @if (!$loop->first) style="display: none;" @endif>
                                        <div class="container">
                                            <div class="slider-1-height-3 slider-animated-1">
                                                {!! Theme::partial('shortcodes.sliders.content', compact('slider', 'shortcode')) !!}
                                                <div class="slider-img">
                                                    <img src="{{ RvMedia::getImageUrl($slider->image, null, false, RvMedia::getDefaultImage()) }}" alt="image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="slider-arrow hero-slider-1-arrow style-3"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 d-md-none d-lg-block">
                        @if (is_plugin_active('ads'))
                            @foreach (get_ads_keys_from_shortcode($shortcode) as $key)
                                {!! display_ad($key, 'banner-' . ($loop->index + 1)) !!}
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @elseif ($style == 'style-4')
        <section class="home-slider position-relative mb-30 mt-30">
            <div class="container">
                <div class="home-slide-cover mt-30">
                    {!! Theme::partial('shortcodes.sliders.grid', compact('sliders', 'shortcode') + ['class' => 'style-4']) !!}
                </div>
            </div>
        </section>
    @elseif ($style == 'style-2')
        <section class="home-slider style-2 position-relative mb-50" @if ($shortcode->cover_image) style="background-image: url({{ RvMedia::getImageUrl($shortcode->cover_image) }});" @endif>
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 col-lg-12">
                        <div class="home-slide-cover">
                            <div class="hero-slider-1 style-4 dot-style-1 dot-style-1-position-1">
                                @foreach($sliders as $slider)
                                    <div class="single-hero-slider single-animation-wrap" style="background-image: url({{ RvMedia::getImageUrl($slider->image, null, false, RvMedia::getDefaultImage()) }}); @if (!$loop->first) display: none; @endif">
                                        {!! Theme::partial('shortcodes.sliders.content', compact('slider', 'shortcode')) !!}
                                    </div>
                                @endforeach
                            </div>
                            <div class="slider-arrow hero-slider-1-arrow"></div>
                        </div>
                    </div>
                    <div class="col-lg-4 d-none d-xl-block">
                        @if (is_plugin_active('ads'))
                            @foreach (get_ads_keys_from_shortcode($shortcode) as $key)
                                {!! display_ad($key) !!}
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @elseif ($style == 'style-5')
        <section class="home-slider position-relative mb-30">
            <div class="container">
                <div class="row">
                    <div class="col-lg-2 d-none d-lg-flex">
                        @php
                            $categories = !is_plugin_active('ecommerce') ? collect() : ProductCategoryHelper::getAllProductCategories()
                                ->where('status', \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)
                                ->where('parent_id', 0);
                        @endphp
                        @if ($categories->count())
                            <div class="categories-dropdown-wrap style-2 font-heading mt-30">
                                <div class="d-flex categori-dropdown-inner">
                                    <ul>
                                        @foreach ($categories->take(10) as $category)
                                            <li>
                                                <a href="{{ $category->url }}">
                                                    @if ($category->getMetaData('icon_image', true))
                                                        <img src="{{ RvMedia::getImageUrl($category->getMetaData('icon_image', true)) }}" alt="{{ $category->name }}" width="30" height="30">
                                                    @elseif ($category->getMetaData('icon', true))
                                                        <i class="{{ $category->getMetaData('icon', true) }}"></i>
                                                    @endif {{ $category->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @if ($categories->count() > 10)
                                <div class="more_slide_open" style="display: none">
                                    <div class="d-flex categori-dropdown-inner">
                                        <ul>
                                            @foreach ($categories->skip(10) as $category)
                                                <li>
                                                    <a href="{{ $category->url }}">
                                                        @if ($category->getMetaData('icon_image', true))
                                                            <img src="{{ RvMedia::getImageUrl($category->getMetaData('icon_image', true)) }}" alt="{{ $category->name }}" width="30" height="30">
                                                        @elseif ($category->getMetaData('icon', true))
                                                            <i class="{{ $category->getMetaData('icon', true) }}"></i>
                                                        @endif {{ $category->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <div class="more_categories"><span class="icon"></span> <span class="heading-sm-1">{{ __('Show more...') }}</span></div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-7">
                        <div class="home-slide-cover mt-30">
                            <div class="hero-slider-1 style-5 dot-style-1 dot-style-1-position-2">
                                @foreach($sliders as $slider)
                                    <div class="single-hero-slider single-animation-wrap" style="background-image: url({{ RvMedia::getImageUrl($slider->image, null, false, RvMedia::getDefaultImage()) }}); @if (!$loop->first) display: none; @endif">
                                        {!! Theme::partial('shortcodes.sliders.content', compact('slider', 'shortcode')) !!}
                                    </div>
                                @endforeach
                            </div>
                            <div class="slider-arrow hero-slider-1-arrow"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="row">
                            @if (is_plugin_active('ads'))
                                @foreach (get_ads_keys_from_shortcode($shortcode) as $key)
                                    <div class="col-md-6 col-lg-12">
                                        {!! display_ad($key, 'banner-' . ($loop->index + 1)) !!}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--End hero slider-->
    @else
        <section class="home-slider position-relative mb-30">
            <div class="home-slide-cover">
                {!! Theme::partial('shortcodes.sliders.grid', compact('sliders', 'shortcode') + ['class' => 'style-4', 'itemClass' => 'rectangle']) !!}
            </div>
        </section>
    @endif
@endif
