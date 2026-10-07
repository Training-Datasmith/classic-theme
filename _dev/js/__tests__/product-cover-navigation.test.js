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
import resolveThumbParentOnSwipe from '../components/product-cover-navigation';

describe('resolveThumbParentOnSwipe', () => {
  test('moves to the previous thumb on right swipe when one exists', () => {
    document.body.innerHTML = `
      <ul>
        <li class="thumb-container" id="first"></li>
        <li class="thumb-container selected" id="second"></li>
      </ul>
    `;

    const parentThumb = $('#second');
    const nextParent = resolveThumbParentOnSwipe('right', parentThumb);

    expect(nextParent.attr('id')).toBe('first');
  });

  test('does not wrap to the next thumb when already on the first image', () => {
    document.body.innerHTML = `
      <ul>
        <li class="thumb-container selected" id="first"></li>
        <li class="thumb-container" id="second"></li>
      </ul>
    `;

    const parentThumb = $('#first');
    const nextParent = resolveThumbParentOnSwipe('right', parentThumb);

    expect(nextParent).toBeNull();
  });

  test('moves to the next thumb on left swipe when one exists', () => {
    document.body.innerHTML = `
      <ul>
        <li class="thumb-container selected" id="first"></li>
        <li class="thumb-container" id="second"></li>
      </ul>
    `;

    const parentThumb = $('#first');
    const nextParent = resolveThumbParentOnSwipe('left', parentThumb);

    expect(nextParent.attr('id')).toBe('second');
  });

  test('does not wrap to the previous thumb when already on the last image', () => {
    document.body.innerHTML = `
      <ul>
        <li class="thumb-container" id="first"></li>
        <li class="thumb-container selected" id="second"></li>
      </ul>
    `;

    const parentThumb = $('#second');
    const nextParent = resolveThumbParentOnSwipe('left', parentThumb);

    expect(nextParent).toBeNull();
  });
});
