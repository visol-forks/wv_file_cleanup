<?php

declare(strict_types=1);

namespace WebVision\WvFileCleanup\Widgets;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use WebVision\WvFileCleanup\Service\ProtectedFileService;

/**
 * Lists all files that editors protected from cleanup
 */
class ProtectedFilesWidget implements WidgetInterface, RequestAwareWidgetInterface
{
    private ServerRequestInterface $request;

    public function __construct(
        private readonly WidgetConfigurationInterface $configuration,
        private readonly BackendViewFactory $backendViewFactory,
        private readonly ProtectedFileService $protectedFileService,
        private readonly UriBuilder $uriBuilder,
    ) {
    }

    public function renderWidgetContent(): string
    {
        $view = $this->backendViewFactory->create(
            $this->request,
            ['typo3/cms-dashboard', 'web-vision/wv_file_cleanup']
        );
        $view->assignMultiple([
            'files' => $this->getFiles(),
            'configuration' => $this->configuration,
            'exportUrl' => (string)$this->uriBuilder->buildUriFromRoute('ajax_wv_file_cleanup_protected_files_csv'),
        ]);

        return $view->render('Widget/ProtectedFiles');
    }

    /** @return array<string, mixed> */
    public function getOptions(): array
    {
        return [];
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getFiles(): array
    {
        $files = [];
        foreach ($this->protectedFileService->findProtectedFiles() as $file) {
            $files[] = [
                'resource' => $file,
                'uid' => $file->getUid(),
                'metadataUid' => (int)($file->getMetaData()['uid'] ?? 0),
                'name' => $file->getName(),
                'path' => $file->getParentFolder()->getReadablePath(),
                'extension' => strtoupper($file->getExtension()),
                'size' => $file->getSize(),
                'publicUrl' => (string)$file->getPublicUrl(),
            ];
        }

        return $files;
    }
}
