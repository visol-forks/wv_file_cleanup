<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Dashboard\WidgetRegistry;
use WebVision\WvFileCleanup\Widgets\ProtectedFilesWidget;

/**
 * The dashboard widget is optional: EXT:dashboard is only suggested, so the
 * widget is registered - and its class loaded - when dashboard is installed.
 * ExtensionManagementUtility cannot tell, it is not initialized while the
 * container is built.
 */
return static function (ContainerConfigurator $configurator, ContainerBuilder $containerBuilder): void {
    if (!$containerBuilder->hasDefinition(WidgetRegistry::class)) {
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
