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

class LayoutGridTest extends TestCase
{
    public function testBothColumnsSumsToTwelve(): void
    {
        $left = element_class_from_tpl('templates/layouts/layout-both-columns.tpl', 'left-column');
        $content = element_class_from_tpl('templates/layouts/layout-both-columns.tpl', 'content-wrapper');
        $right = element_class_from_tpl('templates/layouts/layout-both-columns.tpl', 'right-column');

        $this->assertSame(12, bootstrap_column_width_at($left, 'md')
            + bootstrap_column_width_at($content, 'md')
            + bootstrap_column_width_at($right, 'md'));
        $this->assertSame(12, bootstrap_column_width_at($left, 'lg')
            + bootstrap_column_width_at($content, 'lg')
            + bootstrap_column_width_at($right, 'lg'));
    }

    public function testLeftColumnPageSumsToTwelve(): void
    {
        $left = element_class_from_tpl('templates/layouts/layout-both-columns.tpl', 'left-column');
        $content = element_class_from_tpl('templates/layouts/layout-left-column.tpl', 'content-wrapper');

        $this->assertSame(12, bootstrap_column_width_at($left, 'md') + bootstrap_column_width_at($content, 'md'));
        $this->assertSame(12, bootstrap_column_width_at($left, 'lg') + bootstrap_column_width_at($content, 'lg'));
    }

    public function testRightColumnPageSumsToTwelve(): void
    {
        $right = element_class_from_tpl('templates/layouts/layout-both-columns.tpl', 'right-column');
        $content = element_class_from_tpl('templates/layouts/layout-right-column.tpl', 'content-wrapper');

        $this->assertSame(12, bootstrap_column_width_at($right, 'md') + bootstrap_column_width_at($content, 'md'));
        $this->assertSame(12, bootstrap_column_width_at($right, 'lg') + bootstrap_column_width_at($content, 'lg'));
    }

    public function testFullWidthAndContentOnlyAreFullRow(): void
    {
        $fullWidth = element_class_from_tpl('templates/layouts/layout-full-width.tpl', 'content-wrapper');
        $contentOnly = element_class_from_tpl('templates/layouts/layout-content-only.tpl', 'content-wrapper');

        $this->assertContains('col-xs-12', $fullWidth);
        $this->assertContains('col-xs-12', $contentOnly);
        $this->assertContains("{block name='left_column'}{/block}", read_theme_file('templates/layouts/layout-full-width.tpl'));
        $this->assertContains("{block name='right_column'}{/block}", read_theme_file('templates/layouts/layout-full-width.tpl'));
    }
}
