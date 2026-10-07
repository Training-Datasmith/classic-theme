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
const jquery = jest.requireActual('../../node_modules/jquery/dist/jquery.js');

export const jqueryPluginMockState = {
  touchSpinOptions: null,
  swipeHandler: null,
  ajaxDeferred: null,
  touchSpinMock: null,
  ajaxMock: null,
};

export function resetJqueryPluginMockState() {
  jqueryPluginMockState.touchSpinOptions = null;
  jqueryPluginMockState.swipeHandler = null;
  jqueryPluginMockState.ajaxDeferred = jquery.Deferred();
  jqueryPluginMockState.touchSpinMock = jest.fn(function touchSpin(options) {
    jqueryPluginMockState.touchSpinOptions = options;
    return this;
  });
  jqueryPluginMockState.ajaxMock = jest.fn(
    () => jqueryPluginMockState.ajaxDeferred.promise(),
  );

  jquery.fn.TouchSpin = jqueryPluginMockState.touchSpinMock;
  jquery.fn.modal = jest.fn().mockReturnThis();
  jquery.fn.collapse = jest.fn().mockReturnThis();
  jquery.fn.scrollbox = jest.fn().mockReturnThis();
  jquery.fn.swipe = jest.fn(function swipe(options) {
    if (this.hasClass('js-qv-product-cover')) {
      jqueryPluginMockState.swipeHandler = options.swipe;
    }

    return this;
  });
  jquery.ajax = jqueryPluginMockState.ajaxMock;
}

resetJqueryPluginMockState();

export default jquery;

module.exports = jquery;
module.exports.default = jquery;
module.exports.jqueryPluginMockState = jqueryPluginMockState;
module.exports.resetJqueryPluginMockState = resetJqueryPluginMockState;
