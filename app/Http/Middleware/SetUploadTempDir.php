<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetUploadTempDir
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Set a custom upload directory
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        ini_set('upload_tmp_dir', $tempDir);
        
        return $next($request);
    }
} 