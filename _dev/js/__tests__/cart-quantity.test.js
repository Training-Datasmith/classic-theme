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
import {getCartTouchSpinOptions, getUpdateCartReasonFromTarget} from '../components/cart-quantity';

describe('cart quantity helpers', () => {
  test('maps touchspin button classes to decrease and increase actions', () => {
    document.body.innerHTML = '<input name="product-quantity-spin" min="3" value="3">';
    const options = getCartTouchSpinOptions(document.querySelector('input'));

    expect(options.buttondown_class).toContain('js-decrease-product-quantity');
    expect(options.buttonup_class).toContain('js-increase-product-quantity');
    expect(options.min).toBe(3);
  });

  test('reads min from the input attribute when present', () => {
    document.body.innerHTML = '<input name="product-quantity-spin" min="5" value="5">';
    const options = getCartTouchSpinOptions(document.querySelector('input'));

    expect(options.min).toBe(5);
  });

  test('builds updateCart reason from the DOM dataset contract', () => {
    document.body.innerHTML = `
      <input
        class="js-cart-line-product-quantity"
        data-id-product="42"
        data-product-id="42"
        data-update-url="/cart"
        value="2"
      >
    `;

    const $input = $('.js-cart-line-product-quantity');
    const reason = getUpdateCartReasonFromTarget($input, {quantity: 2});

    expect(reason.idProduct).toBe('42');
    expect(reason.productId).toBe('42');
    expect(reason.updateUrl).toBe('/cart');
  });
});
