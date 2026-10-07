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

class DirectoryGuardTest extends TestCase
{
    /** @var resource|null */
    private static $serverProcess;

    /** @var string */
    private static $serverHost = '127.0.0.1';

    /** @var int */
    private static $serverPort;

    public static function setUpBeforeClass(): void
    {
        $port = 9100;
        $maxPort = 9200;
        $phpBinary = escapeshellarg(PHP_BINARY);
        $documentRoot = escapeshellarg(theme_root());

        while ($port <= $maxPort) {
            $connection = @fsockopen(self::$serverHost, $port);
            if (is_resource($connection)) {
                fclose($connection);
                ++$port;
                continue;
            }

            $command = 'exec ' . $phpBinary . ' -S ' . self::$serverHost . ':' . $port . ' -t ' . $documentRoot;
            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];
            self::$serverProcess = proc_open($command, $descriptors, $pipes, theme_root());
            if (!is_resource(self::$serverProcess)) {
                throw new RuntimeException('Failed to start PHP built-in server.');
            }

            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            self::$serverPort = $port;
            break;
        }

        if (!isset(self::$serverPort)) {
            throw new RuntimeException('Could not find a free port for the built-in server.');
        }

        $deadline = microtime(true) + 5.0;
        $ready = false;
        while (microtime(true) < $deadline) {
            $probe = @fsockopen(self::$serverHost, self::$serverPort);
            if (is_resource($probe)) {
                fclose($probe);
                $ready = true;
                break;
            }
            usleep(100000);
        }

        if (!$ready) {
            self::tearDownAfterClass();
            throw new RuntimeException('PHP built-in server did not become ready.');
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (!is_resource(self::$serverProcess)) {
            return;
        }

        proc_terminate(self::$serverProcess);
        proc_close(self::$serverProcess);
        self::$serverProcess = null;
    }

    public function guardProvider(): array
    {
        $cases = [];
        foreach (find_module_index_guards() as $absolutePath) {
            $relative = substr($absolutePath, strlen(theme_root()) + 1);
            $cases[$relative] = [$relative];
        }

        return $cases;
    }

    /**
     * @dataProvider guardProvider
     */
    public function testGuardRedirectsToParent(string $relativePath): void
    {
        $url = 'http://' . self::$serverHost . ':' . self::$serverPort . '/' . $relativePath;
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'ignore_errors' => true,
                'follow_location' => 0,
            ],
        ]);

        $before = time();
        $body = file_get_contents($url, false, $context);
        $after = time();
        $this->assertNotFalse($body);
        $this->assertSame('', $body);

        $this->assertInternalType('array', $http_response_header);
        $statusLine = $http_response_header[0];
        $this->assertRegExp('/\s302\s/', $statusLine);

        $headers = [];
        foreach ($http_response_header as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }
            list($name, $value) = explode(':', $line, 2);
            $headers[strtolower(trim($name))] = trim($value);
        }

        $this->assertSame('../', $headers['location']);
        $this->assertSame('Mon, 26 Jul 1997 05:00:00 GMT', $headers['expires']);
        $this->assertSame('no-cache', $headers['pragma']);
        $cacheControlValues = [];
        foreach ($http_response_header as $line) {
            if (stripos($line, 'Cache-Control:') === 0) {
                $cacheControlValues[] = trim(substr($line, strlen('Cache-Control:')));
            }
        }
        $this->assertContains('no-store, no-cache, must-revalidate', $cacheControlValues);
        $this->assertContains('post-check=0, pre-check=0', $cacheControlValues);
        $this->assertArrayHasKey('last-modified', $headers);

        $lastModified = strtotime($headers['last-modified']);
        $this->assertNotFalse($lastModified);
        $this->assertGreaterThanOrEqual($before - 5, $lastModified);
        $this->assertLessThanOrEqual($after + 5, $lastModified);
    }
}
