<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

use PHPUnit\Framework\TestCase;

class StorefrontHookTest extends TestCase
{
    public function testDropdownMarkersExist(): void
    {
        $this->assertContains('js-dropdown', read_theme_file('modules/ps_languageselector/ps_languageselector.tpl'));
        $this->assertContains('js-dropdown', read_theme_file('modules/ps_currencyselector/ps_currencyselector.tpl'));
    }

    public function testPasswordPolicyMarkersExist(): void
    {
        $this->assertContains('field-password-policy', read_theme_file('templates/customer/password-new.tpl'));
        $this->assertContains('field-password-policy', read_theme_file('templates/customer/_partials/customer-form.tpl'));
        $this->assertContains('js-input-column', read_theme_file('templates/_partials/form-fields.tpl'));
    }

    public function testPasswordFieldMarkersExist(): void
    {
        $formFields = read_theme_file('templates/_partials/form-fields.tpl');
        $this->assertContains('js-visible-password', $formFields);
        $this->assertContains('data-action="show-password"', $formFields);
        $this->assertContains('js-child-focus', $formFields);
        $this->assertContains('js-parent-focus', $formFields);
        $this->assertContains('data-minlength', $formFields);
        $this->assertContains('data-maxlength', $formFields);
        $this->assertContains('data-minscore', $formFields);
    }

    public function testCartQuantityInputUsesProductMinimum(): void
    {
        $cartLine = read_theme_file('templates/checkout/_partials/cart-detailed-product-line.tpl');
        $this->assertContains('js-cart-line-product-quantity', $cartLine);
        $this->assertContains('name="product-quantity-spin"', $cartLine);
        $this->assertContains('data-up-url', $cartLine);
        $this->assertContains('data-down-url', $cartLine);
        $this->assertContains('data-update-url', $cartLine);
        $this->assertContains('data-id-product', $cartLine);
        $this->assertContains('$product.minimal_quantity', $cartLine);
        $this->assertRegExp('/min="\{if isset\(\$product\.minimal_quantity\)/', $cartLine);
    }

    public function testProductGalleryMarkersExist(): void
    {
        $cover = read_theme_file('templates/catalog/_partials/product-cover-thumbnails.tpl');
        $this->assertContains('js-qv-product-cover', $cover);
        $this->assertContains('js-thumb', $cover);
        $this->assertContains('data-image-medium-src', $cover);
        $this->assertContains('data-image-large-src', $cover);

        $modal = read_theme_file('templates/catalog/_partials/product-images-modal.tpl');
        $this->assertContains('js-modal-thumb', $modal);
        $this->assertContains('js-modal-product-cover', $modal);
        $this->assertContains('data-image-large-src', $modal);
    }

    public function testProductMiniatureMarkerExists(): void
    {
        $this->assertContains('js-product-miniature', read_theme_file('templates/catalog/_partials/miniatures/product.tpl'));
    }

    public function testOrderReturnFormMarkersExist(): void
    {
        foreach ([
            'templates/customer/_partials/order-detail-return.tpl',
            'templates/customer/_partials/order-detail-return-multishipment.tpl',
        ] as $template) {
            $contents = read_theme_file($template);
            $this->assertContains('id="order-return-form"', $contents);
            $this->assertContains('js-order-return-form', $contents);
            $this->assertContains('<input type="checkbox"/>', $contents);
        }
    }

    public function testLayoutShellMarkersExist(): void
    {
        $layout = read_theme_file('templates/layouts/layout-both-columns.tpl');
        $this->assertContains('id="{$page.page_name}"', $layout);
        $this->assertContains('id="content-wrapper"', $layout);
        $this->assertContains('id="footer"', $layout);
    }

    public function testWebpackEntriesExist(): void
    {
        $this->assertFileExists(theme_root() . '/_dev/js/theme.js');
        $this->assertFileExists(theme_root() . '/_dev/css/theme.scss');
        $this->assertFileExists(theme_root() . '/_dev/css/error.scss');
    }

    public function testProductQuantityWantedMarkerExists(): void
    {
        $addToCart = read_theme_file('templates/catalog/_partials/product-add-to-cart.tpl');
        $this->assertContains('id="quantity_wanted"', $addToCart);
        $this->assertRegExp('/min="\{if \$product\.quantity_required\}/', $addToCart);
    }
}
