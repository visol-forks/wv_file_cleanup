<?php

declare(strict_types=1);

use WebVision\WvFileCleanup\Controller\Backend\ProtectedFilesCsvExportController;

return [
    'wv_file_cleanup_protected_files_csv' => [
        'path' => '/wv-file-cleanup/protected-files/csv',
        'target' => ProtectedFilesCsvExportController::class . '::handleRequest',
    ],
];
