<?php

namespace App\Http\Controllers;

use App\Helpers\DatabaseBackupHelper;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        return view('admin.backup.index');
    }

    public function create(Request $request)
    {
        try {
            $backupPath = DatabaseBackupHelper::generateMysqlDump();
            
            ActivityLogger::log(
                action: 'backup.created',
                description: 'Database backup created',
                properties: ['backup_path' => basename($backupPath)],
                user: $request->user()
            );

            return response()->download($backupPath)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip|max:51200', // 50MB max
        ]);

        try {
            $file = $request->file('backup_file');
            $tempPath = $file->store('temp');
            $fullPath = storage_path('app/' . $tempPath);
            
            DatabaseBackupHelper::restoreDump($fullPath);
            
            // Clean up
            Storage::delete($tempPath);
            
            ActivityLogger::log(
                action: 'backup.restored',
                description: 'Database backup restored',
                properties: ['filename' => $file->getClientOriginalName()],
                user: $request->user()
            );

            return redirect()->back()->with('success', 'Database restored successfully.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }
}