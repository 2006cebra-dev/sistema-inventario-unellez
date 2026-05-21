<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    public function download()
    {
        if (!Auth::check() || Auth::user()->rol !== 'admin') {
            abort(403, 'No autorizado para realizar respaldos.');
        }

        $filename = "backup_oswa_" . date('Y-m-d_H-i-s') . ".sql";
        $path = storage_path('app/' . $filename);

        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbHost = env('DB_HOST');

        $mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

        if (empty($dbPass)) {
            $command = "\"{$mysqldumpPath}\" --user={$dbUser} --host={$dbHost} {$dbName} > \"{$path}\" 2>&1";
        } else {
            $command = "\"{$mysqldumpPath}\" --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > \"{$path}\" 2>&1";
        }

        exec($command, $output, $returnVar);

        if ($returnVar !== 0 || !file_exists($path)) {
            Log::error("Error en Backup: " . implode("\n", $output));
            return back()->with('error', 'Fallo técnico al generar el SQL. Revisa los logs de Laravel.');
        }

        return response()->download($path)->deleteFileAfterSend(true);
    }
}
