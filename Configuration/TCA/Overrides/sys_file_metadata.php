<?php

declare(strict_types=1);

defined('TYPO3') || die();

$GLOBALS['TCA']['sys_file_metadata']['columns']['cleanup_protected'] = [
    'exclude' => true,
    'label' => 'LLL:EXT:wv_file_cleanup/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.cleanup_protected',
    'description' => 'LLL:EXT:wv_file_cleanup/Resources/Private/Language/locallang_db.xlf:sys_file_metadata.cleanup_protected.description',
    // The flag belongs to the file itself, translations of the metadata inherit it
    'l10n_mode' => 'exclude',
    'l10n_display' => 'defaultAsReadonly',
    'config' => [
        'type' => 'check',
        'renderType' => 'checkboxToggle',
        'default' => 0,
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'sys_file_metadata',
    'cleanup_protected',
    '',
    'after:description'
);
