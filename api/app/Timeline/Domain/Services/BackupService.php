<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/17/19
 * Time: 12:50 AM
 */

namespace App\Timeline\Domain\Services;


use App\Timeline\Domain\Collections\BackupCollection;
use App\Timeline\Domain\Models\Backup;
use App\Timeline\Exceptions\TimelineException;
use Carbon\Carbon;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\Console\Output\BufferedOutput;

class BackupService
{
    /**
     * @var Factory
     */
    private $fsFactory;
    /**
     * @var Filesystem
     */
    private $fs;
    /**
     * @var Kernel
     */
    private $console;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * BackupService constructor.
     * @param Factory $fsFactory
     * @param Kernel $console
     * @param UserService $userService
     */
    public function __construct(Factory $fsFactory, Kernel $console, UserService $userService)
    {
        $this->fsFactory = $fsFactory;
        $this->console = $console;
        $this->userService = $userService;
        $this->fs = $this->fsFactory->disk('local-backup-disk');
    }

    public function getAll(): BackupCollection
    {
        $backupFolder = $this->getBackupFolder();
        $backupFiles = $this->fs->files($backupFolder);
        $backupFiles = array_filter($backupFiles, function (string $file) {
            return preg_match('/^.+?\\.zip$/', $file);
        });
        $backupFiles = array_values($backupFiles);

        $backups = new BackupCollection();

        foreach ($backupFiles as $backupFile) {
            $date = Carbon::createFromTimestamp($this->fs->lastModified($backupFile));
            $size = $this->fs->size($backupFile);
            $name = str_after($backupFile, $backupFolder . '/');

            $backups->push(new Backup(
                $name,
                $size,
                $date
            ));
        }

        return $backups;
    }

    public function backupNow(BufferedOutput $output = null): Backup
    {
        $oldBackups = $this->getAll();
        $status = $this->console->call('backup:run', [], $output);
        if ($status !== 0) {
            throw TimelineException::ofFailedToBackup();
        }
        $newBackups = $this->getAll();
        $backups = $newBackups->diffUsing($oldBackups, function (Backup $a, Backup $b) {
            if ($a->getName() > $b->getName()) {
                return 1;
            } elseif ($a->getName() < $b->getName()) {
                return -1;
            }
            return 0;
        })->values();
        if (count($backups) === 0) {
            throw TimelineException::ofFailedToBackup();
        }
        return $backups[0];
    }

    public function getSummary(): string
    {
        $output = new BufferedOutput();
        $this->console->call('backup:list', [], $output);
        return $output->fetch();
    }

    public function getStatus(): string
    {
        $output = new BufferedOutput();
        $this->console->call('backup:monitor', [], $output);
        return $output->fetch();
    }

    public function delete(string $name): void
    {
        if (!$this->isBackupFileExist($name)) {
            throw TimelineException::ofBackupFileWithNameDoesNotExist($name);
        }
        $status = $this->fs->delete($this->getBackupFilePath($name));
        if (!$status) {
            throw TimelineException::ofUnableToDeleteBackupWithName($name);
        }
    }

    public function getDownloadPath(string $name, string $password): string
    {
        if (!$this->isBackupFileExist($name)) {
            throw TimelineException::ofBackupFileWithNameDoesNotExist($name);
        }

        $isPasswordCorrect = $this->userService->validateCurrentUserPassword($password);

        if (!$isPasswordCorrect) {
            throw TimelineException::ofUnauthorizedToDownloadBackupFile($name);
        }

        $fsRoot = config('filesystems.disks.local-backup-disk.root');

        return $fsRoot . '/' . $this->getBackupFilePath($name);
    }

    private function getBackupFilePath(string $name): string
    {
        $backupFolder = $this->getBackupFolder();
        return $backupFolder . '/' . $name;
    }

    private function isBackupFileExist(string $name): bool
    {
        return $this->fs->exists($this->getBackupFilePath($name));
    }

    private function getBackupFolder(): string
    {
        return config('backup.backup.name');
    }
}