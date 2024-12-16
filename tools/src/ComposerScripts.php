<?php

declare(strict_types=1);

/**
 * This file is part of the Nexus framework.
 *
 * (c) John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Nexus\Tools;

use Nexus\Suppression\Silencer;

/**
 * @internal
 */
final class ComposerScripts
{
    public const VSCODE_SETTINGS_JSON = __DIR__.'/../../.vscode/settings.json';

    public static function postUpdate(): void
    {
        if (is_file(self::VSCODE_SETTINGS_JSON)) {
            self::recursiveDelete(__DIR__.'/../../vendor/phpstan/phpstan-phar');
            self::extractPhpstanPhar();
            self::updateVscodeIntelephenseEnvironmentIncludePaths();
        }
    }

    private static function recursiveDelete(string $directory): void
    {
        if (! is_dir($directory)) {
            echo \sprintf("\033[33mWARN\033[0m Cannot recursively delete \"%s\" as it does not exist.\n", $directory);

            return;
        }

        /** @var \SplFileInfo $file */
        foreach (new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(rtrim($directory, '\\/'), \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        ) as $file) {
            $path = $file->getPathname();

            if ($file->isDir()) {
                rmdir($path);
            } else {
                unlink($path);
            }
        }
    }

    private static function extractPhpstanPhar(): void
    {
        try {
            (new \Phar(__DIR__.'/../../vendor/phpstan/phpstan/phpstan.phar'))->extractTo(__DIR__.'/../../vendor/phpstan/phpstan-phar', null, true);
            echo "\033[32mOK\033[0m PHPStan successfully extracted for IDE completion.\n";
        } catch (\PharException|\UnexpectedValueException $e) {
            echo \sprintf("\033[31mFAIL\033[0m %s\n", $e->getMessage());

            exit(1);
        }
    }

    private static function updateVscodeIntelephenseEnvironmentIncludePaths(): void
    {
        $contents = Silencer::suppress(
            static fn(): false|string => file_get_contents(self::VSCODE_SETTINGS_JSON),
        );

        if (false === $contents) {
            echo \sprintf("\033[31mFAIL\033[0m Cannot get the contents of %s as it is probably missing or unreadable.\n", self::VSCODE_SETTINGS_JSON);

            exit(1);
        }

        $settingsJson = (array) json_decode($contents, true);

        if (! isset($settingsJson['intelephense.environment.includePaths']) || ! \is_array($settingsJson['intelephense.environment.includePaths'])) {
            $settingsJson['intelephense.environment.includePaths'] = [];
        }

        if (! \in_array('vendor/phpstan/phpstan-phar/', $settingsJson['intelephense.environment.includePaths'], true)) {
            $settingsJson['intelephense.environment.includePaths'][] = 'vendor/phpstan/phpstan-phar/';
        }

        ksort($settingsJson);

        try {
            $newContents = json_encode($settingsJson, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

            if ($newContents === $contents) {
                echo "\033[32mSKIP\033[0m .vscode/settings.json not needed to update.\n";

                return;
            }

            if (file_put_contents(self::VSCODE_SETTINGS_JSON, $newContents) === false) {
                echo "\033[31mFAIL\033[0m Cannot save the new contents to .vscode/settings.json.\n";

                exit(1);
            }
        } catch (\JsonException $e) {
            echo \sprintf("\033[31mFAIL\033[0m %s\n", $e->getMessage());

            exit(1);
        }
    }
}
