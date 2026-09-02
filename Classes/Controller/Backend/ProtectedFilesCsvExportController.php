<?php

declare(strict_types=1);

namespace WebVision\WvFileCleanup\Controller\Backend;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\CsvUtility;
use WebVision\WvFileCleanup\Service\ProtectedFileService;

/**
 * Downloads the list of files protected from cleanup as CSV
 */
class ProtectedFilesCsvExportController
{
    public function __construct(
        private readonly ProtectedFileService $protectedFileService,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->getBackendUser()->check('available_widgets', 'wvFileCleanupProtectedFiles')) {
            return $this->responseFactory->createResponse(403);
        }

        $lines = [CsvUtility::csvValues(['UID', 'Name', 'Path', 'Type', 'Size', 'URL'])];
        foreach ($this->protectedFileService->findProtectedFiles() as $file) {
            $lines[] = CsvUtility::csvValues([
                (string)$file->getUid(),
                $file->getName(),
                $file->getParentFolder()->getReadablePath(),
                strtoupper($file->getExtension()),
                (string)$file->getSize(),
                (string)$file->getPublicUrl(),
            ]);
        }

        $csvContent = implode("\n", $lines) . "\n";

        return $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="protected-files.csv"')
            ->withBody($this->streamFactory->createStream($csvContent));
    }

    private function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
