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
import Form from '../components/form';

describe('Form', () => {
  test('toggles password visibility and button label', () => {
    document.body.innerHTML = `
      <div class="input-group js-parent-focus">
        <input type="password" class="js-visible-password" value="secret">
        <button type="button" data-action="show-password" data-text-show="Show" data-text-hide="Hide">Show</button>
      </div>
    `;

    const form = new Form();
    form.init();

    $('button[data-action="show-password"]').trigger('click');
    expect($('.js-visible-password').attr('type')).toBe('text');
    expect($('button[data-action="show-password"]').text()).toBe('Hide');

    $('button[data-action="show-password"]').trigger('click');
    expect($('.js-visible-password').attr('type')).toBe('password');
    expect($('button[data-action="show-password"]').text()).toBe('Show');
  });

  test('adds focus class to parent wrapper', () => {
    document.body.innerHTML = `
      <div class="js-parent-focus">
        <input class="js-child-focus">
      </div>
    `;

    const form = new Form();
    form.init();

    $('.js-child-focus').trigger('focus');
    expect($('.js-parent-focus').hasClass('focus')).toBe(true);

    $('.js-child-focus').trigger('focusout');
    expect($('.js-parent-focus').hasClass('focus')).toBe(false);
  });
});
