<?php

use Botble\Ads\Models\Ads;
use Botble\Base\Models\MetaBox as MetaBoxModel;
use Botble\Blog\Models\Post;
use Botble\Ecommerce\Models\FlashSale;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Marketplace\Models\Store;
use Botble\Page\Models\Page;
use Botble\SimpleSlider\Models\SimpleSlider;
use Botble\SimpleSlider\Models\SimpleSliderItem;

register_page_template([
    'full-width'    => __('Full width'),
    'homepage'      => __('Homepage'),
    'right-sidebar' => __('Page Right Sidebar'),
    'blog-grid'     => __('Blog Grid'),
    'blog-list'     => __('Blog List'),
    'blog-big'      => __('Blog Big'),
    'blog-wide'     => __('Blog Wide'),
]);

register_sidebar([
    'id'          => 'footer_sidebar',
    'name'        => __('Footer sidebar'),
    'description' => __('Widgets in footer of page'),
]);

register_sidebar([
    'id'          => 'product_sidebar',
    'name'        => __('Product sidebar'),
    'description' => __('Widgets in the product page'),
]);

register_sidebar([
    'id'          => 'pre_footer_sidebar',
    'name'        => __('Top footer sidebar'),
    'description' => __('Widgets in the blog page'),
]);

Form::component('themeIcon', Theme::getThemeNamespace() . '::partials.forms.fields.icons-field', [
    'name',
    'value'      => null,
    'attributes' => [],
]);

Menu::addMenuLocation('header-navigation', 'Header navigation');

RvMedia::setUploadPathAndURLToPublic();

RvMedia::addSize('medium', 800, 800)
    ->addSize('product-thumb', 400, 400);

if (is_plugin_active('ecommerce')) {
    app()->booted(function () {
        ProductCategory::resolveRelationUsing('icon', function ($model) {
            return $model->morphOne(MetaBoxModel::class, 'reference')->where('meta_key', 'icon');
        });

        if (is_plugin_active('language-advanced')) {
            LanguageAdvancedManager::registerModule(FlashSale::class, ['name', 'subtitle', 'image']);
        }
    });
}

if (!function_exists('get_currencies_json')) {
    /**
     * @return array
     */
    function get_currencies_json()
    {
        $currency = get_application_currency();
        $numberAfterDot = $currency->decimals ?: 0;

        return [
            'display_big_money'    => config('plugins.ecommerce.general.display_big_money_in_million_billion'),
            'billion'              => __('billion'),
            'million'              => __('million'),
            'is_prefix_symbol'     => $currency->is_prefix_symbol,
            'symbol'               => $currency->symbol,
            'title'                => $currency->title,
            'decimal_separator'    => get_ecommerce_setting('decimal_separator', '.'),
            'thousands_separator'  => get_ecommerce_setting('thousands_separator', ','),
            'number_after_dot'     => $numberAfterDot,
            'show_symbol_or_title' => true,
        ];
    }
}

if (!function_exists('get_blog_single_layouts')) {
    /**
     * @return string[]
     */
    function get_blog_single_layouts(): array
    {
        return [
            ''                        => __('Inherit'),
            'blog-post-right-sidebar' => __('Post Right Sidebar'),
            'blog-post-left-sidebar'  => __('Post Left Sidebar'),
            'blog-post-full-width'    => __('Post Full Width'),
        ];
    }
}

if (!function_exists('get_product_single_layouts')) {
    /**
     * @return string[]
     */
    function get_product_single_layouts(): array
    {
        return [
            ''                      => __('Inherit'),
            'product-right-sidebar' => __('Product Right Sidebar'),
            'product-left-sidebar'  => __('Product Left Sidebar'),
            'product-full-width'    => __('Product Full Width'),
        ];
    }
}

if (!function_exists('get_layout_header_styles')) {
    /**
     * @return string[]
     */
    function get_layout_header_styles(): array
    {
        return [
            ''               => __('Default'),
            'header-style-5' => __('Header style 5'),
        ];
    }
}

if (!function_exists('get_simple_slider_styles')) {
    /**
     * @return string[]
     */
    function get_simple_slider_styles(): array
    {
        return [
            'style-1' => __('Default - Full width'),
            'style-2' => __('Full width - text center'),
            'style-3' => __('With Ads'),
            'style-4' => __('Limit width'),
            'style-5' => __('With categories & 2 Ads'),
        ];
    }
}

if (!function_exists('get_time_to_read')) {
    /**
     * @param Post $post
     * @return string
     */
    function get_time_to_read(Post $post)
    {
        $timeToRead = MetaBox::getMetaData($post, 'time_to_read', true);

        if ($timeToRead) {
            return number_format($timeToRead);
        }

        return number_format(strlen(strip_tags($post->content)) / 300);
    }
}

add_filter(BASE_FILTER_BEFORE_RENDER_FORM, function ($form, $data) {
    switch (get_class($data)) {
        case SimpleSliderItem::class:
            $buttonText = MetaBox::getMetaData($data, 'button_text', true);
            $subtitle = MetaBox::getMetaData($data, 'subtitle', true);
            $highlightText = MetaBox::getMetaData($data, 'highlight_text', true);

            $form
                ->addAfter('link', 'button_text', 'text', [
                    'label'      => __('Button text'),
                    'label_attr' => ['class' => 'control-label'],
                    'value'      => $buttonText,
                    'attr'       => [
                        'placeholder' => __('Ex: Shop now'),
                    ],
                ])
                ->addBefore('title', 'subtitle', 'text', [
                    'label'      => __('Subtitle'),
                    'label_attr' => ['class' => 'control-label'],
                    'value'      => $subtitle,
                    'attr'       => [
                        'placeholder' => __('Text to highlight'),
                    ],
                ])
                ->addAfter('title', 'highlight_text', 'text', [
                    'label'      => __('Highlight text'),
                    'label_attr' => ['class' => 'control-label'],
                    'value'      => $highlightText,
                    'attr'       => [
                        'placeholder' => __('Text to highlight'),
                    ],
                ]);
            break;

        case Ads::class:
            $buttonText = MetaBox::getMetaData($data, 'button_text', true);
            $subtitle = MetaBox::getMetaData($data, 'subtitle', true);

            $form
                ->addAfter('key', 'button_text', 'text', [
                    'label'      => __('Button text'),
                    'label_attr' => ['class' => 'control-label'],
                    'value'      => $buttonText,
                    'attr'       => [
                        'placeholder' => __('Ex: Shop now'),
                    ],
                ])
                ->addBefore('key', 'subtitle', 'textarea', [
                    'label'      => __('Subtitle'),
                    'label_attr' => ['class' => 'control-label'],
                    'value'      => $subtitle,
                    'attr'       => [
                        'placeholder' => __('Text to highlight'),
                        'rows'        => 3,
                    ],
                ]);
            break;

        case FlashSale::class:
            $subtitle = MetaBox::getMetaData($data, 'subtitle', true);
            $image = MetaBox::getMetaData($data, 'image', true);

            $form
                ->addAfter('name', 'subtitle', 'text', [
                    'label'      => __('Subtitle'),
                    'label_attr' => ['class' => 'control-label'],
                    'value'      => $subtitle,
                    'attr'       => [
                        'placeholder' => __('Text to highlight'),
                    ],
                ])
                ->addAfter('end_date', 'image', 'mediaImage', [
                    'label'      => __('Image'),
                    'label_attr' => ['class' => 'control-label'],
                    'value'      => $image,
                ]);
            break;
    }

    return $form;
}, 124, 3);

add_action(BASE_ACTION_META_BOXES, 'add_addition_fields_in_form_screen', 75, 2);

function add_addition_fields_in_form_screen($context, $object)
{
    switch (get_class($object)) {
        case Page::class:
            if ($context == 'top') {
                MetaBox::addMetaBox(
                    'additional_page_fields',
                    __('Appearance'),
                    function () {
                        $headerStyle = null;
                        $page = null;
                        $args = func_get_args();
                        if (!empty($args[0])) {
                            $page = $args[0];
                            $headerStyle = MetaBox::getMetaData($args[0], 'header_style', true);
                        }

                        return Theme::partial('additional-page-fields', compact('headerStyle', 'page'));
                    },
                    get_class($object),
                    $context
                );
            }
            break;
        case SimpleSlider::class:
            if ($context == 'top') {
                MetaBox::addMetaBox(
                    'additional_simple_slider_fields',
                    __('Appearance'),
                    function () {
                        $style = '';
                        $args = func_get_args();
                        if (!empty($args[0])) {
                            $style = MetaBox::getMetaData($args[0], 'simple_slider_style', true);
                        }

                        return Theme::partial('additional-simple-slider-fields', compact('style'));
                    },
                    get_class($object),
                    $context
                );
            }
            break;

        case ProductCategory::class:
            if ($context == 'advanced') {
                MetaBox::addMetaBox('additional_product_category_fields', __('Addition Information'), function () {
                    $icon = null;
                    $iconImage = null;
                    $args = func_get_args();
                    if (!empty($args[0])) {
                        $icon = MetaBox::getMetaData($args[0], 'icon', true);
                        $iconImage = MetaBox::getMetaData($args[0], 'icon_image', true);
                        $bgColor = MetaBox::getMetaData($args[0], 'background_color', true);
                    }

                    Assets::addStyles(['colorpicker'])->addScripts(['colorpicker']);

                    return Theme::partial('product-category-fields', compact('icon', 'iconImage', 'bgColor'));
                }, get_class($object), $context);
            }
            break;

        case Product::class:
            if ($context == 'top') {
                MetaBox::addMetaBox(
                    'additional_product_fields',
                    __('Addition Information'),
                    function () {
                        $layout = null;
                        $isPopular = null;

                        $args = func_get_args();
                        if (!empty($args[0])) {
                            $layout = MetaBox::getMetaData($args[0], 'layout', true);
                            $isPopular = MetaBox::getMetaData($args[0], 'is_popular', true);
                        }

                        return Theme::partial('additional-product-fields', compact('layout', 'isPopular'));
                    },
                    get_class($object),
                    $context
                );
            }
            break;

        case Post::class:
            if ($context == 'top') {
                MetaBox::addMetaBox(
                    'additional_post_fields',
                    __('Addition Information'),
                    function () {
                        $timeToRead = null;
                        $layout = null;
                        $args = func_get_args();
                        if (!empty($args[0])) {
                            $timeToRead = MetaBox::getMetaData($args[0], 'time_to_read', true);
                            $layout = MetaBox::getMetaData($args[0], 'layout', true);
                        }

                        return Theme::partial('blog-post-fields', compact('timeToRead', 'layout'));
                    },
                    get_class($object),
                    $context
                );
            }
            break;
    }
}

add_action(BASE_ACTION_AFTER_CREATE_CONTENT, 'save_addition_in_form_fields', 75, 3);
add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, 'save_addition_in_form_fields', 76, 3);

function save_addition_in_form_fields($type, $request, $object)
{
    switch (get_class($object)) {
        case Page::class:
            if ($request->has('header_style')) {
                $style = $request->input('header_style');
                if (in_array($style, array_keys(get_layout_header_styles()))) {
                    MetaBox::saveMetaBoxData($object, 'header_style', $style);
                }
            }

            break;

        case SimpleSlider::class:
            if ($request->has('simple_slider_style')) {
                $style = $request->input('simple_slider_style');
                if (in_array($style, array_keys(get_simple_slider_styles()))) {
                    MetaBox::saveMetaBoxData($object, 'simple_slider_style', $style);
                }
            }

            break;

        case ProductCategory::class:
            if ($request->has('icon')) {
                MetaBox::saveMetaBoxData($object, 'icon', $request->input('icon'));
            }

            if ($request->has('icon_image')) {
                MetaBox::saveMetaBoxData($object, 'icon_image', $request->input('icon_image'));
            }

            if ($request->has('background_color')) {
                MetaBox::saveMetaBoxData($object, 'background_color', $request->input('background_color'));
            }

            break;

        case Product::class:
            if ($request->has('layout')) {
                MetaBox::saveMetaBoxData($object, 'layout', $request->input('layout'));
            }

            if ($request->has('is_popular')) {
                MetaBox::saveMetaBoxData($object, 'is_popular', $request->input('is_popular'));
            }

            break;

        case Post::class:
            if ($request->has('time_to_read')) {
                MetaBox::saveMetaBoxData($object, 'time_to_read', $request->input('time_to_read'));
            }

            if ($request->has('layout')) {
                MetaBox::saveMetaBoxData($object, 'layout', $request->input('layout'));
            }

            break;

        case FlashSale::class:
            if ($request->has('subtitle')) {
                MetaBox::saveMetaBoxData($object, 'subtitle', $request->input('subtitle'));
            }

            if ($request->has('image')) {
                MetaBox::saveMetaBoxData($object, 'image', $request->input('image'));
            }

            break;

        case Ads::class:
            if ($request->has('button_text')) {
                MetaBox::saveMetaBoxData($object, 'button_text', $request->input('button_text'));
            }

            if ($request->has('subtitle')) {
                MetaBox::saveMetaBoxData($object, 'subtitle', $request->input('subtitle'));
            }
            break;

        case Store::class:
            if ($request->has('social_links')) {
                if ($socialLinks = $request->input('social_links', [])) {
                    $socials = array_keys(get_store_social_links());
                    $socialLinks = collect($socialLinks)->only($socials)->filter();
                    MetaBox::saveMetaBoxData($object, 'social_links', $socialLinks);
                }
            }

            if (theme_option('vendor_page_detail_layout') == 'list') {
                if ($request->hasFile('cover_image_input')) {
                    $result = RvMedia::handleUpload($request->file('cover_image_input'), 0, 'stores');
                    if ($result['error'] == false) {
                        MetaBox::saveMetaBoxData($object, 'cover_image', $result['data']->url);
                    }
                } elseif ($request->has('cover_image') && !$request->input('cover_image')) {
                    MetaBox::deleteMetaData($object, 'cover_image');
                }
            }

            break;
    }
}

add_filter('marketplace_vendor_settings_register_content_tabs', function ($tabs, $model) {
    switch (get_class($model)) {
        case Store::class:
            $tabs .= Theme::partial('marketplace.store.settings.extra-tab', compact('model'));
            break;
    }

    return $tabs;
}, 124, 2);

add_filter('marketplace_vendor_settings_register_content_tab_inside', function ($tabs, $model) {
    switch (get_class($model)) {
        case Store::class:
            $model->loadMissing(['metadata']);

            $tabs .= Theme::partial('marketplace.store.settings.extra-content', compact('model'));
            break;
    }

    return $tabs;
}, 124, 2);

function get_store_social_links()
{
    return [
        'facebook'  => [
            'title'  => 'Facebook',
            'logo'   => 'imgs/theme/icons/social-facebook.svg',
            'domain' => 'https://facebook.com/',
        ],
        'twitter'   => [
            'title'  => 'Twitter',
            'logo'   => 'imgs/theme/icons/social-twitter.svg',
            'domain' => 'https://twitter.com/',
        ],
        'instagram' => [
            'title'  => 'Instagram',
            'logo'   => 'imgs/theme/icons/social-instagram.svg',
            'domain' => 'https://instagram.com/',
        ],
        'pinterest' => [
            'title'  => 'Pinterest',
            'logo'   => 'imgs/theme/icons/social-pinterest.svg',
            'domain' => 'https://pinterest.com/',
        ],
        'youtube'   => [
            'title'  => 'Youtube',
            'logo'   => 'imgs/theme/icons/social-youtube.svg',
            'domain' => 'https://youtube.com/',
        ],
        'linkedin'  => [
            'title'  => 'Linkedin',
            'logo'   => 'imgs/theme/icons/social-linkedin.svg',
            'domain' => 'https://linkedin.com/',
        ],
        'messenger' => [
            'title'  => 'Messenger',
            'logo'   => 'imgs/theme/icons/social-messenger.svg',
            'domain' => 'https://messenger.com/',
        ],
        'flickr'    => [
            'title'  => 'Flickr',
            'logo'   => 'imgs/theme/icons/social-flickr.svg',
            'domain' => 'https://flickr.com/',
        ],
        'tiktok'    => [
            'title'  => 'Tiktok',
            'logo'   => 'imgs/theme/icons/social-tiktok.svg',
            'domain' => 'https://tiktok.com/',
        ],
        'skype'     => [
            'title'       => 'Skype',
            'logo'        => 'imgs/theme/icons/social-skype.svg',
            'placeholder' => 'Ex: https://skype.com/{username}',
        ],
        'snapchat'  => [
            'title'       => 'Snapchat',
            'logo'        => 'imgs/theme/icons/social-snapchat.svg',
            'placeholder' => 'Ex: https://snapchat.com/{username}',
        ],
        'tumblr'    => [
            'title'       => 'Tumblr',
            'logo'        => 'imgs/theme/icons/social-tumblr.svg',
            'placeholder' => 'Ex: https://tumblr.com/{username}',
        ],
        'whatsapp'  => [
            'title'       => 'Whatsapp',
            'logo'        => 'imgs/theme/icons/social-whatsapp.svg',
            'placeholder' => 'Ex: https://whatsapp.com/{username}',
        ],
        'wechat'    => [
            'title'       => 'Wechat',
            'logo'        => 'imgs/theme/icons/social-wechat.svg',
            'placeholder' => 'Ex: https://wechat.com/{username}',
        ],
        'vimeo'     => [
            'title'       => 'Vimeo',
            'logo'        => 'imgs/theme/icons/social-vimeo.svg',
            'placeholder' => 'Ex: https://vimeo.com/{username}',
        ],
    ];
}

app()->booted(function () {
    if (is_plugin_active('social-login')) {
        $data = SocialService::getModule('customer');
        if ($data) {
            $data['view'] = Theme::getThemeNamespace('partials.social-login-options');
            $data['use_css'] = false;
            SocialService::registerModule($data);
        }
    }

    if (is_plugin_active('ads') && is_plugin_active('language-advanced')) {
        LanguageAdvancedManager::registerModule(Ads::class, ['name', 'subtitle', 'button_text']);
    }
});

if (is_plugin_active('ads')) {
    AdsManager::registerLocation('item-style-2', __('Ads style 2'))
        ->registerLocation('item-style-4', __('Ads style 4'))
        ->registerLocation('item-style-5', __('Ads style 5'));
}

/**
 * @param string $color
 * @return string
 */
function hex_to_rgb(string $color)
{
    [$r, $g, $b] = sscanf($color, "#%02x%02x%02x");
    return $r . ',' . $g . ',' . ($b === null ? 0 : $b);
}
