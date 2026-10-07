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

describe('product.js integration', () => {
  let $;
  let jqueryMocks;

  function renderGallery(selectedIndex) {
    const thumbs = [
      {
        medium: 'medium-1.jpg',
        large: 'large-1.jpg',
        label: 'Product 1',
      },
      {
        medium: 'medium-2.jpg',
        large: 'large-2.jpg',
        label: 'Product 2',
      },
    ];
    const selectedThumb = thumbs[selectedIndex];

    document.body.innerHTML = `
      <div id="main">
        <img class="js-modal-product-cover" src="${selectedThumb.large}" alt="${selectedThumb.label}">
        <img class="js-qv-product-cover" src="${selectedThumb.medium}" alt="${selectedThumb.label}">
        <div class="js-qv-mask">
          <ul class="js-qv-product-images">
            ${thumbs.map((thumb, index) => `
              <li class="thumb-container js-thumb-container">
                <img
                  class="thumb js-thumb${index === selectedIndex ? ' selected js-thumb-selected' : ''}"
                  data-image-medium-src="${thumb.medium}"
                  data-image-large-src="${thumb.large}"
                  alt="${thumb.label}"
                  title="${thumb.label}"
                >
              </li>
            `).join('')}
          </ul>
        </div>
      </div>
      <input id="quantity_wanted" min="1" value="1">
    `;
  }

  async function loadProductModule(selectedIndex) {
    jest.resetModules();
    renderGallery(selectedIndex);
    $ = require('jquery').default || require('jquery');
    jqueryMocks = require('jquery').jqueryPluginMockState;
    require('../product');
    await waitForDocumentReady($);
    await flushPromises();
  }

  test('swipe left moves to the next thumbnail and updates the cover src', async () => {
    await loadProductModule(0);

    expect(jqueryMocks.swipeHandler).toEqual(expect.any(Function));
    jqueryMocks.swipeHandler({}, 'left');

    expect($('.js-qv-product-cover').attr('src')).toBe('medium-2.jpg');
    expect($('.js-thumb.selected').data('image-medium-src')).toBe('medium-2.jpg');
  });

  test('swipe right on the first thumbnail does not wrap to the second image', async () => {
    await loadProductModule(0);

    jqueryMocks.swipeHandler({}, 'right');

    expect($('.js-qv-product-cover').attr('src')).toBe('medium-1.jpg');
    expect($('.js-thumb.selected').data('image-medium-src')).toBe('medium-1.jpg');
  });

  test('swipe left on the last thumbnail does not wrap to the first image', async () => {
    await loadProductModule(1);

    jqueryMocks.swipeHandler({}, 'left');

    expect($('.js-qv-product-cover').attr('src')).toBe('medium-2.jpg');
    expect($('.js-thumb.selected').data('image-medium-src')).toBe('medium-2.jpg');
  });
});
