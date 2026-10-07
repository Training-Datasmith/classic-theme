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

class TemplateReferenceTest extends TestCase
{
    public function testLiteralExtendsResolve(): void
    {
        $extendsPattern = '/\{extends\s+(?:file\s*=\s*)?([\'"])(.+?)\1/i';

        foreach (find_tpl_files() as $path) {
            $contents = strip_smarty_for_reference_scan(file_get_contents($path));
            if (!preg_match_all($extendsPattern, $contents, $matches, PREG_SET_ORDER)) {
                continue;
            }

            foreach ($matches as $match) {
                $target = $match[2];
                if (strpos($target, '$') !== false) {
                    continue;
                }

                $resolved = theme_root() . '/templates/' . $target;
                $this->assertFileExists($resolved, 'Missing extends target ' . $target . ' from ' . $path);
            }
        }
    }

    public function testLiteralIncludesResolve(): void
    {
        $includePattern = '/\{include\s+(?:file\s*=\s*)?([\'"])(.+?)\1/i';

        foreach (find_tpl_files() as $path) {
            $contents = strip_smarty_for_reference_scan(file_get_contents($path));
            if (!preg_match_all($includePattern, $contents, $matches, PREG_SET_ORDER)) {
                continue;
            }

            foreach ($matches as $match) {
                $target = $match[2];
                if (strpos($target, '$') !== false || strpos($target, 'module:') === 0) {
                    continue;
                }

                if (strpos($target, './') === 0 || strpos($target, '../') === 0) {
                    $resolved = realpath(dirname($path) . '/' . $target);
                } else {
                    $resolved = realpath(theme_root() . '/templates/' . $target);
                }

                $this->assertNotFalse($resolved, 'Missing include target ' . $target . ' from ' . $path);
            }
        }
    }

    public function testDynamicModulePartialsExist(): void
    {
        $this->assertFileExists(theme_root() . '/modules/ps_brandlist/views/templates/_partials/brand_text.tpl');
        $this->assertFileExists(theme_root() . '/modules/ps_brandlist/views/templates/_partials/brand_form.tpl');
        $this->assertFileExists(theme_root() . '/modules/ps_supplierlist/views/templates/_partials/supplier_text.tpl');
        $this->assertFileExists(theme_root() . '/modules/ps_supplierlist/views/templates/_partials/supplier_form.tpl');
    }

    public function testRequiredPageTemplatesExist(): void
    {
        $required = [
            'templates/index.tpl',
            'templates/page.tpl',
            'templates/catalog/product.tpl',
            'templates/catalog/listing/product-list.tpl',
            'templates/checkout/cart.tpl',
            'templates/checkout/checkout.tpl',
            'templates/checkout/order-confirmation.tpl',
            'templates/customer/authentication.tpl',
            'templates/customer/registration.tpl',
            'templates/customer/my-account.tpl',
            'templates/customer/order-detail.tpl',
            'templates/cms/page.tpl',
            'templates/contact.tpl',
            'templates/errors/404.tpl',
            'templates/errors/not-found.tpl',
            'templates/errors/restricted-country.tpl',
            'templates/errors/maintenance.tpl',
            'templates/errors/forbidden.tpl',
            'templates/layouts/layout-both-columns.tpl',
            'templates/layouts/layout-error.tpl',
        ];

        foreach ($required as $relative) {
            $this->assertFileExists(theme_root() . '/' . $relative, $relative);
        }
    }

    public function testLayoutInheritance(): void
    {
        $this->assertContains("{extends file='page.tpl'}", read_theme_file('templates/index.tpl'));
        $this->assertContains('{extends file=$layout}', read_theme_file('templates/page.tpl'));

        foreach ([
            'templates/layouts/layout-full-width.tpl',
            'templates/layouts/layout-left-column.tpl',
            'templates/layouts/layout-right-column.tpl',
            'templates/layouts/layout-content-only.tpl',
        ] as $layout) {
            $this->assertContains("{extends file='layouts/layout-both-columns.tpl'}", read_theme_file($layout));
        }

        $this->assertContains("{extends file='layouts/layout-error.tpl'}", read_theme_file('templates/errors/restricted-country.tpl'));
        $this->assertContains("{extends file='layouts/layout-error.tpl'}", read_theme_file('templates/errors/maintenance.tpl'));
    }
}
