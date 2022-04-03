<link rel="stylesheet" href="{{ Theme::asset()->url('css/plugins/magnific-popup.css') }}">
<script src="{{ Theme::asset()->url('js/plugins/magnific-popup.js') }}"></script>

<div class="product-detail accordion-detail">
<div class="row">
    <div class="col-md-6 col-sm-12 col-xs-12 mb-md-0 mb-sm-5">
        <div class="detail-gallery">
            <span class="zoom-icon"><i class="fi-rs-search"></i></span>
            <div class="product-image-slider">
                @foreach ($productImages as $img)
                    <figure class="border-radius-10">
                        <a href="{{ RvMedia::getImageUrl($img) }}"><img src="{{ RvMedia::getImageUrl($img, 'medium') }}" alt="{{ $product->name }}"></a>
                    </figure>
                @endforeach
            </div>
            <!-- THUMBNAILS -->
            <div class="slider-nav-thumbnails">
                @foreach ($productImages as $img)
                    <div><img src="{{ RvMedia::getImageUrl($img, 'thumb') }}" alt="{{ $product->name }}"></div>
                @endforeach
            </div>
        </div>
        <!-- End Gallery -->
    </div>

    <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="detail-info pr-30 pl-30">
            @foreach ($product->productLabels as $label)
                <div class="product-badges">
                    <span @if ($label->color) style="background-color: {{ $label->color }}" @endif>{{ $label->name }}</span>
                </div>
            @endforeach

            <h2 class="title-detail">{{ $product->name }}</h2>
            <div class="product-detail-rating">
                @if (EcommerceHelper::isReviewEnabled())
                    <div class="product-rate-cover text-end">
                        <div class="product-rate d-inline-block">
                            <div class="product-rating" style="width: {{ $product->reviews_avg * 20 }}%"></div>
                        </div>
                        <span class="font-small ml-5 text-muted">({{ __(':count reviews', ['count' => $product->reviews_count]) }})</span>
                    </div>
                @endif
            </div>
            <div class="clearfix product-price-cover">
                <div class="product-price primary-color float-left">
                    <span class="current-price text-brand">{{ format_price($product->front_sale_price_with_taxes) }}</span>
                    <span>
                        <span class="save-price font-md color3 ml-15" @if ($product->front_sale_price == $product->price) style="display: none" @endif>
                            <span class="percentage-off">{{ get_sale_percentage($product->price, $product->front_sale_price) }} {{ __('Off') }}</span>
                        </span>
                        <span class="old-price font-md ml-15" @if ($product->front_sale_price == $product->price) style="display: none" @endif>{{ format_price($product->price_with_taxes) }}</span>
                    </span>
                </div>
            </div>

            <div class="short-desc mb-30">
                @if (is_plugin_active('marketplace') && $product->store_id)
                    <p>{{ __('Sold By') }}: <a href="{{ $product->store->url }}"><strong>{{ $product->store->name }}</strong></a></p>
                @endif

                {!! apply_filters('ecommerce_before_product_description', null, $product) !!}
                {!! clean($product->description) !!}
                {!! apply_filters('ecommerce_after_product_description', null, $product) !!}
            </div>

            @if ($product->variations()->count() > 0)
                <div class="pr_switch_wrap">
                    {!! render_product_swatches($product, [
                        'selected' => $selectedAttrs,
                        'view'     => Theme::getThemeNamespace() . '::views.ecommerce.attributes.swatches-renderer'
                    ]) !!}
                </div>
                <div class="number-items-available" style="@if (!$product->isOutOfStock()) display: none; @endif margin-bottom: 10px;">
                    @if ($product->isOutOfStock())
                        <span class="text-danger">({{ __('Out of stock') }})</span>
                    @endif
                </div>
            @endif

            <form class="add-to-cart-form" method="POST" action="{{ route('public.cart.add-to-cart') }}">
                @csrf
                {!! apply_filters(ECOMMERCE_PRODUCT_DETAIL_EXTRA_HTML, null) !!}
                <input type="hidden" name="id" class="hidden-product-id" value="{{ ($product->is_variation || !$product->defaultVariation->product_id) ? $product->id : $product->defaultVariation->product_id }}"/>
                <div class="detail-extralink mb-50">
                    @if (EcommerceHelper::isCartEnabled())
                        <div class="detail-qty border radius">
                            <input type="hidden" value="1" name="qty">
                            <a href="#" class="qty-down"><i class="fi-rs-angle-small-down"></i></a>
                            <span class="qty-val">1</span>
                            <a href="#" class="qty-up"><i class="fi-rs-angle-small-up"></i></a>
                        </div>
                    @endif

                    <div class="product-extra-link2 @if (EcommerceHelper::isQuickBuyButtonEnabled()) has-buy-now-button @endif">
                        @if (EcommerceHelper::isCartEnabled())
                            <button type="submit"
                                    class="button button-add-to-cart @if ($product->isOutOfStock()) btn-disabled @endif"
                                    @if ($product->isOutOfStock()) disabled @endif><i class="fi-rs-shopping-cart"></i>{{ __('Add to cart') }}</button>
                        @endif

                        @if (EcommerceHelper::isWishlistEnabled())
                            <a aria-label="{{ __('Add To Wishlist') }}" class="action-btn hover-up js-add-to-wishlist-button" data-url="{{ route('public.wishlist.add', $product->id) }}" href="#"><i class="fi-rs-heart"></i></a>
                        @endif
                        @if (EcommerceHelper::isCompareEnabled())
                            <a aria-label="{{ __('Add To Compare') }}" href="#" class="action-btn hover-up js-add-to-compare-button" data-url="{{ route('public.compare.add', $product->id) }}"><i class="fi-rs-shuffle"></i></a>
                        @endif
                    </div>
                </div>
            </form>
            <div class="font-xs">

                <ul class="mr-50 float-start">

                    <li class="mb-5" @if ($product->sku) style="display: none" @endif id="product-sku">
                        <span class="d-inline-block">{{ __('SKU') }}</span>: <span class="sku-text">{{ $product->sku }}</span>
                    </li>

                    @if ($product->categories->count())
                        <li class="mb-5">
                            <span class="d-inline-block">{{ __('Categories') }}:</span>
                            @foreach($product->categories as $category)
                                <a href="{{ $category->url }}" title="{{ $category->name }}">{{ $category->name }}</a>@if (!$loop->last),@endif
                            @endforeach
                        </li>
                    @endif
                    @if ($product->tags->count())
                        <li class="mb-5">
                            <span class="d-inline-block">{{ __('Tags') }}:</span>
                            @foreach($product->tags as $tag)
                                <a href="{{ $tag->url }}" rel="tag" title="{{ $tag->name }}">{{ $tag->name }}</a>@if (!$loop->last),@endif
                            @endforeach
                        </li>
                    @endif

                    @if ($product->brand->id)
                        <li class="mb-5">
                            <span class="d-inline-block">{{ __('Brands') }}:</span>
                            <a href="{{ $product->brand->url }}" title="{{ $product->brand->name }}">{{ $product->brand->name }}</a>
                        </li>
                    @endif
                    <li class="stock-status-wrap">
                        <span class="d-inline-block">{{ __('Availability') }}:</span>
                        <span class="in-stock text-success ml-5">{!! clean($product->stock_status_html) !!}</span>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Detail Info -->
    </div>
</div>
</div>
