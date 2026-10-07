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

require dirname(__DIR__) . '/vendor/autoload.php';

function theme_root(): string
{
    return dirname(__DIR__);
}

/**
 * @return string[]
 */
function find_tpl_files(): array
{
    $root = theme_root();
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $fileInfo) {
        if (!$fileInfo->isFile() || $fileInfo->getExtension() !== 'tpl') {
            continue;
        }

        $path = $fileInfo->getPathname();
        if (strpos($path, '/.git/') !== false || strpos($path, '/vendor/') !== false) {
            continue;
        }

        $files[] = $path;
    }

    sort($files);

    return $files;
}

function strip_smarty_for_reference_scan(string $contents): string
{
    $contents = preg_replace('/\{\*.*?\*\}/s', '', $contents);
    $contents = preg_replace('/\{literal\}.*?\{\/literal\}/s', '', $contents);

    return $contents;
}

function read_theme_file(string $relativePath): string
{
    $path = theme_root() . '/' . ltrim($relativePath, '/');
    if (!is_file($path)) {
        throw new InvalidArgumentException('Missing theme file: ' . $relativePath);
    }

    return file_get_contents($path);
}

function bootstrap_column_width_at(string $classAttribute, string $breakpoint): int
{
    if (!preg_match('/\bcol-' . preg_quote($breakpoint, '/') . '-(\d+)\b/', $classAttribute, $matches)) {
        return 0;
    }

    return (int) $matches[1];
}

function element_class_from_tpl(string $relativePath, string $elementId): string
{
    $contents = read_theme_file($relativePath);
    $pattern = '/id="' . preg_quote($elementId, '/') . '"[^>]*class="([^"]+)"/';

    if (!preg_match($pattern, $contents, $matches)) {
        $pattern = '/class="([^"]+)"[^>]*id="' . preg_quote($elementId, '/') . '"/';
        if (!preg_match($pattern, $contents, $matches)) {
            throw new RuntimeException('Could not find element #' . $elementId . ' in ' . $relativePath);
        }
    }

    return $matches[1];
}

/**
 * @return string[]
 */
function find_module_index_guards(): array
{
    $root = theme_root() . '/modules';
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $fileInfo) {
        if ($fileInfo->isFile() && $fileInfo->getFilename() === 'index.php') {
            $files[] = $fileInfo->getPathname();
        }
    }

    sort($files);

    return $files;
}
