<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use WebVision\WvFileCleanup\Widgets\ProtectedFilesWidget;

/**
 * The dashboard widget is optional: EXT:dashboard is only suggested, so the
 * widget is registered - and its class loaded - when dashboard is installed.
 */
return static function (ContainerConfigurator $configurator): void {
    if (!ExtensionManagementUtility::isLoaded('dashboard')) {
        return;
    }

    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();

    $services->set('dashboard.widget.wvFileCleanupProtectedFiles', ProtectedFilesWidget::class)
        ->tag('dashboard.widget', [
            'identifier' => 'wvFileCleanupProtectedFiles',
            'groupNames' => 'systemInfo',
            'title' => 'LLL:EXT:wv_file_cleanup/Resources/Private/Language/locallang_mod_cleanup.xlf:widget.protectedFiles.title',
            'description' => 'LLL:EXT:wv_file_cleanup/Resources/Private/Language/locallang_mod_cleanup.xlf:widget.protectedFiles.description',
            'iconIdentifier' => 'content-widget-list',
            'height' => 'large',
            'width' => 'large',
        ]);
};
