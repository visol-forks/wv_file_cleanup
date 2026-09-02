<?php

declare(strict_types=1);

namespace WebVision\WvFileCleanup\Service;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Access to the files editors marked as "protect from cleanup" in the file metadata
 */
class ProtectedFileService
{
    /**
     * Field in sys_file_metadata holding the protection flag
     */
    private const PROTECTED_FIELD = 'cleanup_protected';

    /**
     * Uids of all protected files, resolved once per instance
     *
     * @var int[]|null
     */
    private ?array $protectedFileUids = null;

    public function isProtected(File $file): bool
    {
        return in_array((int)$file->getUid(), $this->getProtectedFileUids(), true);
    }

    /**
     * Uids of all files that must not be cleaned up
     *
     * @return int[]
     */
    public function getProtectedFileUids(): array
    {
        if ($this->protectedFileUids === null) {
            $this->protectedFileUids = $this->fetchProtectedFileUids();
        }

        return $this->protectedFileUids;
    }

    /**
     * All protected files, sorted by their location. Metadata pointing to a file
     * that no longer exists is skipped.
     *
     * @return File[]
     */
    public function findProtectedFiles(): array
    {
        $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

        $files = [];
        foreach ($this->getProtectedFileUids() as $fileUid) {
            try {
                $files[] = $resourceFactory->getFileObject($fileUid);
            } catch (FileDoesNotExistException | \InvalidArgumentException $e) {
                // Nothing to list for metadata without a file
            }
        }

        return $files;
    }

    /**
     * @return int[]
     */
    private function fetchProtectedFileUids(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file_metadata');
        $fileUids = $queryBuilder
            ->select('file.uid')
            ->from('sys_file_metadata', 'metadata')
            ->join(
                'metadata',
                'sys_file',
                'file',
                $queryBuilder->expr()->eq('file.uid', $queryBuilder->quoteIdentifier('metadata.file'))
            )
            ->where(
                $queryBuilder->expr()->eq(
                    'metadata.' . self::PROTECTED_FIELD,
                    $queryBuilder->createNamedParameter(1, Connection::PARAM_INT)
                ),
                // The flag is only maintained on the default language metadata record
                $queryBuilder->expr()->eq(
                    'metadata.sys_language_uid',
                    $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                )
            )
            ->orderBy('file.storage')
            ->addOrderBy('file.identifier')
            ->executeQuery()
            ->fetchFirstColumn();

        return array_map('intval', $fileUids);
    }
}
