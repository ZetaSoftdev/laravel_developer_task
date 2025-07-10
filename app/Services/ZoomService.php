<?php

namespace App\Services;

use App\Models\ZoomSetting;
use GuzzleHttp\Client;
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
            if ($this->settings && $this->settings->access_token && $this->settings->token_expires_at > now()) {
                return $this->settings->access_token;
            }

            $response = $this->client->post($this->oauthEndpoint, [
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
            
            if ($this->settings) {
                $this->settings->update([
                    'access_token' => $data['access_token'],
                    'token_expires_at' => now()->addSeconds($data['expires_in'] - 60)
                ]);
            }

            return $data['access_token'];
        } catch (GuzzleException $e) {
            Log::error('Zoom API Error: ' . $e->getMessage());
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
        } catch (GuzzleException $e) {
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
        } catch (GuzzleException $e) {
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
        } catch (GuzzleException $e) {
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
        } catch (GuzzleException $e) {
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
        } catch (GuzzleException $e) {
            Log::error('Zoom API Error: ' . $e->getMessage());
            return null;
        }
    }
} 