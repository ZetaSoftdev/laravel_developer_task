<?php

namespace App\Services;

use App\Models\ZoomSetting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ZoomService
{
    protected $client;
    protected $clientId;
    protected $clientSecret;
    protected $accountId;
    protected $apiEndpoint = 'https://api.zoom.us/v2/';
    protected $oauthEndpoint = 'https://zoom.us/oauth/token';
    protected $settings;

    public function __construct()
    {
        $this->client = new Client();
        
        // Get settings from database if available
        if (Auth::check() && Auth::user()->school_id) {
            $this->settings = ZoomSetting::where('school_id', Auth::user()->school_id)->first();
            
            if ($this->settings) {
                $this->clientId = $this->settings->client_id;
                $this->clientSecret = $this->settings->client_secret;
                $this->accountId = $this->settings->account_id;
                return;
            }
        }
        
        // Fallback to config values
        $this->clientId = config('services.zoom.client_id');
        $this->clientSecret = config('services.zoom.client_secret');
        $this->accountId = config('services.zoom.account_id');
    }

    public function getAccessToken()
    {
        try {
            // Reload settings to get the latest credentials
            if (Auth::check() && Auth::user()->school_id) {
                $this->settings = ZoomSetting::where('school_id', Auth::user()->school_id)->first();
                if ($this->settings) {
                    $this->clientId = $this->settings->client_id;
                    $this->clientSecret = $this->settings->client_secret;
                    $this->accountId = $this->settings->account_id;
                }
            }

            // Return existing token if still valid
            if ($this->settings && $this->settings->access_token && $this->settings->token_expires_at > now()) {
                Log::info('Returning existing valid Zoom access token');
                return $this->settings->access_token;
            }
    
            // Request new access token from Zoom API
            $response = $this->client->post($this->oauthEndpoint, [
                'verify' => base_path('cacert.pem'),
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => [
                    'grant_type' => 'account_credentials',
                    'account_id' => $this->accountId
                ]
            ]);
    
            $data = json_decode($response->getBody(), true);
    
            Log::info('Zoom token retrieved', [
                'access_token' => $data['access_token'],
                'expires_in' => $data['expires_in']
            ]);
    
            // Save the token to DB
            if ($this->settings) {
                $this->settings->update([
                    'access_token' => $data['access_token'],
                    'token_expires_at' => now()->addSeconds($data['expires_in'] - 60)
                ]);
    
                Log::info('Zoom settings updated successfully');
            }
    
            return $data['access_token'];
        } catch (ClientException $e) {
            Log::error('Zoom ClientException: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::error('Zoom Exception: ' . $e->getMessage());
            return null;
        }
    }
    
    

    public function createMeeting(array $data)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return null;
            }

            $response = $this->client->post($this->apiEndpoint . 'users/me/meetings', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            Log::error('Zoom API Error: ' . $e->getMessage());
            return null;
        }
    }

    public function getMeeting($meetingId)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return null;
            }

            $response = $this->client->get($this->apiEndpoint . 'meetings/' . $meetingId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            Log::error('Zoom API Error: ' . $e->getMessage());
            return null;
        }
    }

    public function updateMeeting($meetingId, array $data)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return null;
            }

            $response = $this->client->patch($this->apiEndpoint . 'meetings/' . $meetingId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            Log::error('Zoom API Error: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteMeeting($meetingId)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return false;
            }

            $this->client->delete($this->apiEndpoint . 'meetings/' . $meetingId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            return true;
        } catch (ClientException $e) {
            Log::error('Zoom API Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getMeetingParticipants($meetingId)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return null;
            }

            $response = $this->client->get($this->apiEndpoint . 'report/meetings/' . $meetingId . '/participants', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {
            Log::error('Zoom API Error: ' . $e->getMessage());
            return null;
        }
    }
} 