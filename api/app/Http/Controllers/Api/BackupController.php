<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\App\Validators\ValidatorFactory;
use App\Timeline\Domain\Services\BackupService;
use Illuminate\Http\Request;
use Symfony\Component\Console\Output\BufferedOutput;

class BackupController extends Controller
{
    /**
     * @var BackupService
     */
    private $backupService;

    /**
     * BackupController constructor.
     * @param BackupService $bakcupService
     */
    public function __construct(BackupService $bakcupService)
    {
        $this->backupService = $bakcupService;
    }

    public function getAll()
    {
        return response()->json($this->backupService->getAll());
    }

    public function backupNow()
    {
        $output = new BufferedOutput();
        $backup = $this->backupService->backupNow($output)->toValueArray();
        $backup['message'] = $output->fetch();
        return response()->json($backup);
    }

    public function getSummary()
    {
        return response()->json($this->backupService->getSummary());
    }

    public function getStatus()
    {
        return response()->json($this->backupService->getStatus());
    }

    public function delete(string $name)
    {
        $this->backupService->delete($name);
        return response()->json(true);
    }

    public function download(string $name, Request $request, ValidatorFactory $validatorFactory)
    {
        $validatorFactory->validate($request->all(), [
            'password' => 'required|string'
        ]);

        return response()->download($this->backupService->getDownloadPath($name, $request->get('password')));
    }
}
