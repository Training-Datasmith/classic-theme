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
use Symfony\Component\Yaml\Yaml;

class ThemeManifestTest extends TestCase
{
    /** @var array */
    private $manifest;

    protected function setUp(): void
    {
        $this->manifest = Yaml::parseFile(theme_root() . '/config/theme.yml');
    }

    public function testManifestParsesToArray(): void
    {
        $this->assertInternalType('array', $this->manifest);
        foreach (['name', 'display_name', 'version', 'author', 'meta', 'global_settings', 'theme_settings'] as $key) {
            $this->assertArrayHasKey($key, $this->manifest);
        }
    }

    public function testIdentity(): void
    {
        $this->assertSame('classic', $this->manifest['name']);
        $this->assertNotEmpty($this->manifest['display_name']);
        $this->assertRegExp('/^\d+\.\d+\.\d+$/', $this->manifest['version']);
        $this->assertNotEmpty($this->manifest['author']['name']);
        $this->assertContains('@', $this->manifest['author']['email']);
    }

    public function testCompatibility(): void
    {
        $from = $this->manifest['meta']['compatibility']['from'];
        $this->assertRegExp('/^\d+\.\d+\.\d+$/', $from);
        $this->assertNotEmpty($this->manifest['meta']['compatibility']['framework']);
        $this->assertNotEmpty($this->manifest['meta']['available_layouts']);
    }

    public function testEachAvailableLayoutHasATemplate(): void
    {
        foreach ($this->manifest['meta']['available_layouts'] as $layoutKey => $layoutMeta) {
            $this->assertFileExists(theme_root() . '/templates/layouts/' . $layoutKey . '.tpl');
            $this->assertNotEmpty($layoutMeta['name']);
            $this->assertNotEmpty($layoutMeta['description']);
        }
    }

    public function testThemeSettingsLayoutsAreDeclared(): void
    {
        $layoutKeys = array_keys($this->manifest['meta']['available_layouts']);
        $this->assertContains($this->manifest['theme_settings']['default_layout'], $layoutKeys);

        foreach ($this->manifest['theme_settings']['layouts'] as $page => $layout) {
            $this->assertNotEmpty($page);
            $this->assertContains($layout, $layoutKeys);
        }
    }

    public function testImageSettings(): void
    {
        $quality = $this->manifest['global_settings']['configuration']['PS_IMAGE_QUALITY'];
        $this->assertContains($quality, ['jpg', 'png', 'webp', 'avif']);

        foreach ($this->manifest['global_settings']['image_types'] as $type) {
            $this->assertGreaterThan(0, $type['width']);
            $this->assertGreaterThan(0, $type['height']);
            $this->assertNotEmpty($type['scope']);
            foreach ($type['scope'] as $scope) {
                $this->assertContains($scope, ['products', 'categories', 'manufacturers', 'suppliers', 'stores']);
            }
        }
    }

    public function testHookAssignments(): void
    {
        $modules = $this->manifest['global_settings']['modules']['to_enable'];
        $this->assertContains('ps_linklist', $modules);

        foreach ($modules as $moduleName) {
            $this->assertNotEmpty($moduleName);
        }

        $hooks = $this->manifest['global_settings']['hooks']['modules_to_hook'];
        $this->assertInternalType('array', $hooks);

        foreach ($hooks as $hookName => $moduleList) {
            $this->assertNotEmpty($hookName);
            $this->assertNotEmpty($moduleList);

            $seen = [];
            $nullSeen = false;
            foreach ($moduleList as $moduleName) {
                if ($moduleName === null) {
                    $nullSeen = true;
                    continue;
                }

                $this->assertFalse($nullSeen, 'Null sentinel must be last in hook ' . $hookName);
                $this->assertNotEmpty($moduleName);
                $this->assertNotContains($moduleName, $seen, 'Duplicate module on hook ' . $hookName);
                $seen[] = $moduleName;
            }
        }
    }

    public function testPasswordPolicyFlagMatchesTemplate(): void
    {
        $this->assertTrue($this->manifest['global_settings']['new_password_policy_feature']);
        $template = read_theme_file('templates/_partials/password-policy-template.tpl');
        $this->assertContains('id="password-feedback"', $template);
        $this->assertContains('password-strength-feedback', $template);
        $this->assertContains('js-hint-password', $template);
        $this->assertContains('password-requirements-length', $template);
        $this->assertContains('password-requirements-score', $template);
    }

    public function testPreviewImage(): void
    {
        $path = theme_root() . '/preview.png';
        $this->assertFileExists($path);
        $this->assertGreaterThan(0, filesize($path));
        $this->assertSame("\x89PNG\r\n\x1a\n", substr(file_get_contents($path), 0, 8));
    }

    public function testPackageType(): void
    {
        $composer = json_decode(file_get_contents(theme_root() . '/composer.json'), true);
        $this->assertSame('prestashop-theme', $composer['type']);
        $this->assertSame('prestashop/classic', $composer['name']);
    }
}
