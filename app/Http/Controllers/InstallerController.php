<?php

namespace App\Http\Controllers;

use dacoto\EnvSet\Facades\EnvSet;
use dacoto\LaravelWizardInstaller\Controllers\InstallFolderController;
use dacoto\LaravelWizardInstaller\Controllers\InstallServerController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InstallerController extends Controller {
    public function purchaseCodeIndex() {
        if (!(new InstallServerController())->check() || !(new InstallFolderController())->check()) {
            return redirect()->route('LaravelWizardInstaller::install.folders');
        }
        return view('vendor.installer.steps.purchase-code');
    }


    public function checkPurchaseCode(Request $request) {
		EnvSet::setKey('APPSECRET', $request->input('purchase_code'));
		EnvSet::save();
		return redirect()->route('LaravelWizardInstaller::install.database');
    }

    public function phpFunctionIndex() {
        if (!(new InstallServerController())->check() || !(new InstallFolderController())->check()) {
            return redirect()->route('LaravelWizardInstaller::install.purchase_code');
        }
        return view('vendor.installer.steps.symlink_basedir_check', [
            'result' => $this->checkSymlink(),
            // 'baseDir' =>$this->checkBaseDir()
        ]);
    }

    public function checkSymlink(): bool
    {
        return function_exists('symlink');
    }
    public function checkBaseDir(): bool
    {
        $openBaseDir = ini_get('open_basedir');
        if ($openBaseDir) {
            return false;
        }
        return true;
    }

}
