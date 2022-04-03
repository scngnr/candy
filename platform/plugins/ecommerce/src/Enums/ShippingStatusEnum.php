<?php

namespace Botble\Ecommerce\Enums;

use Botble\Base\Supports\Enum;
use Html;

/**
 * @method static ShippingStatusEnum NOT_APPROVED()
 * @method static ShippingStatusEnum APPROVED()
 * @method static ShippingStatusEnum PICKING()
 * @method static ShippingStatusEnum DELAY_PICKING()
 * @method static ShippingStatusEnum PICKED()
 * @method static ShippingStatusEnum NOT_PICKED()
 * @method static ShippingStatusEnum DELIVERING()
 * @method static ShippingStatusEnum DELIVERED()
 * @method static ShippingStatusEnum NOT_DELIVERED()
 * @method static ShippingStatusEnum AUDITED()
 * @method static ShippingStatusEnum CANCELED()
 */
class ShippingStatusEnum extends Enum
{
    public const NOT_APPROVED = 'not_approved';
    public const APPROVED = 'approved';
    public const PICKING = 'picking';
    public const DELAY_PICKING = 'delay_picking';
    public const PICKED = 'picked';
    public const NOT_PICKED = 'not_picked';
    public const DELIVERING = 'delivering';
    public const DELIVERED = 'delivered';
    public const NOT_DELIVERED = 'not_delivered';
    public const AUDITED = 'audited';
    public const CANCELED = 'canceled';

    /**
     * @var string
     */
    public static $langPath = 'plugins/ecommerce::shipping.statuses';

    /**
     * @return string
     */
    public function toHtml()
    {
        switch ($this->value) {
            case self::NOT_APPROVED:
            case self::APPROVED:
                return Html::tag('span', self::APPROVED()->label(), ['class' => 'label-warning status-label'])
                    ->toHtml();
            case self::PICKING:
            case self::DELAY_PICKING:
            case self::NOT_PICKED:
                return Html::tag('span', self::PICKING()->label(), ['class' => 'label-info status-label'])
                    ->toHtml();
            case self::DELIVERING:
                return Html::tag('span', self::DELIVERING()->label(), ['class' => 'label-info status-label'])
                    ->toHtml();
            case self::DELIVERED:
            case self::AUDITED:
                return Html::tag('span', self::DELIVERED()->label(), ['class' => 'label-success status-label'])
                    ->toHtml();
            case self::NOT_DELIVERED:
                return Html::tag('span', self::NOT_DELIVERED()->label(), ['class' => 'label-danger status-label'])
                    ->toHtml();
            case self::CANCELED:
                return Html::tag('span', self::CANCELED()->label(), ['class' => 'label-danger status-label'])
                    ->toHtml();
            default:
                return parent::toHtml();
        }
    }
}
