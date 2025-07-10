<?php

namespace App\Http\Controllers;

use App\Repositories\ZoomSetting\ZoomSettingInterface;
use App\Services\ResponseService;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ZoomSettingsController extends Controller
{
    private ZoomSettingInterface $zoomSetting;
    private ZoomService $zoomService;

    public function __construct(ZoomSettingInterface $zoomSetting, ZoomService $zoomService)
    {
        $this->zoomSetting = $zoomSetting;
        $this->zoomService = $zoomService;
    }

    /**
     * Display the Zoom settings form.
     */
    public function index()
    {
        ResponseService::noPermissionThenRedirect('zoom-settings');
        
        $settings = $this->zoomSetting->builder()->where('school_id', Auth::user()->school_id)->first();
        return view('zoom.settings', compact('settings'));
    }

    /**
     * Store or update Zoom settings.
     */
    public function store(Request $request)
    {
        ResponseService::noPermissionThenRedirect('zoom-settings');
        
        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
            'client_secret' => 'required',
            'account_id' => 'required',
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }

        try {
            $settings = $this->zoomSetting->builder()->where('school_id', Auth::user()->school_id)->first();
            
            $data = [
                'school_id' => Auth::user()->school_id,
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
                'account_id' => $request->account_id,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];
            
            if ($settings) {
                $this->zoomSetting->update($settings->id, $data);
            } else {
                $this->zoomSetting->create($data);
            }
            
            // Test the connection
            $token = $this->zoomService->getAccessToken();
            if (!$token) {
                ResponseService::errorResponse('Could not connect to Zoom API. Please check your credentials.');
            }
            
            ResponseService::successResponse('Zoom settings updated successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "ZoomSettingsController -> Store Method");
            ResponseService::errorResponse('Error updating Zoom settings: ' . $e->getMessage());
        }
    }
}
