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
const flushPromises = () => new Promise((resolve) => {
  setTimeout(resolve, 0);
});

function waitForDocumentReady($) {
  return new Promise((resolve) => {
    $(resolve);
  });
}

describe('cart.js integration', () => {
  let $;
  let prestashop;
  let jqueryMocks;

  function mountCartDom() {
    document.body.innerHTML = `
      <div id="notifications"><div class="notifications-container"></div></div>
      <div class="bootstrap-touchspin">
        <input
          name="product-quantity-spin"
          class="js-cart-line-product-quantity"
          value="2"
          data-update-url="/cart-update"
          data-id-product="42"
          data-product-id="42"
        />
      </div>
    `;
  }

  async function loadCartModule() {
    jest.resetModules();
    mountCartDom();
    $ = require('jquery').default || require('jquery');
    jqueryMocks = require('jquery').jqueryPluginMockState;
    prestashop = require('prestashop').default || require('prestashop');
    require('../cart');
    await waitForDocumentReady($);
    prestashop.emit('updatedCart');
    await flushPromises();
  }

  beforeEach(async () => {
    await loadCartModule();
    prestashop.removeAllListeners('updateCart');
    jest.spyOn(prestashop, 'emit');
  });

  afterEach(() => {
    jest.restoreAllMocks();
  });

  test('cart quantity input has no min attribute so zero can remove the line', async () => {
    await flushPromises();

    const input = document.querySelector('input[name="product-quantity-spin"]');
    expect(input.hasAttribute('min')).toBe(false);
  });

  test('createSpin configures decrease and increase touchspin button classes', async () => {
    await flushPromises();

    expect(jqueryMocks.touchSpinMock).toHaveBeenCalled();
    expect(jqueryMocks.touchSpinOptions.buttondown_class).toContain('js-decrease-product-quantity');
    expect(jqueryMocks.touchSpinOptions.buttonup_class).toContain('js-increase-product-quantity');
  });

  test('quantity update emits updateCart with reason.idProduct from the input', async () => {
    await flushPromises();

    const $input = $('input[name="product-quantity-spin"]');
    $input.val('4');
    $input.trigger('focusout');

    jqueryMocks.ajaxDeferred.resolve({quantity: 4});
    await flushPromises();

    const updateCall = prestashop.emit.mock.calls.find((call) => call[0] === 'updateCart');
    expect(updateCall).toBeDefined();
    expect(updateCall[1].reason.idProduct).toBe('42');
    expect(updateCall[1].reason.quantity).toBeUndefined();
    expect(updateCall[1].resp.quantity).toBe(4);
  });
});
