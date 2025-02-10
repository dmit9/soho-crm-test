<?php

namespace App\Services;

use App\Models\ZohoToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use function Symfony\Component\Translation\t;

class ZohoService
{
    protected $apiUrl = 'https://www.zohoapis.eu/crm/v2';
    protected $apiUrlToken = 'https://accounts.zoho.eu/oauth/v2/token';

    public function getAccessToken()
    {
        $client_id = '';
        $client_secret = '';
        $refresh_token = '';
        $grant_type = 'refresh_token';

        $response = Http::asForm()->post($this->apiUrlToken, [
            'refresh_token' => $refresh_token,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'grant_type' => $grant_type,
        ]);
        $data = $response->json();
        //     Log::info('Ответ Zoho при получении токена', $data);

        if ($response->status() === 200 && isset($data['access_token'])) {
            ZohoToken::updateOrCreate(
                ['id' => 1],
                [
                    'access_token' => $data['access_token'],
                    'expires_at' => now()->addSeconds($data['expires_in']),
                    'updated_at' => now(),
                ]
            );

            Log::info('token change ', ['token' => $data['access_token']]);

            return $data;
        }

        Log::error('error change token', ['status' => $response->status(), 'response' => $response->body()]);

        return null;
    }

    public function getValidToken()
    {
        $data = ZohoToken::first();

        if ($data && now()->lessThan($data->expires_at)) {
            $tokenEndDate = [
                'access_token' => $data->access_token,
                'expires_at' => $data->expires_at
            ];
            Log::info('The current token is still valid.', $tokenEndDate);
            return $tokenEndDate;
        }

        Log::info('Токен устарел, получаю новый...');
        $newToken = $this->getAccessToken();

        if (!$newToken || !isset($newToken['access_token'])) {
            Log::error('Error: New token not received!', ['response' => $newToken]);
            return null;
        }

        $tokenEndDate = [
            'access_token' => $newToken['access_token'],
            'expires_at' => now()->addSeconds($newToken['expires_in'])
        ];
        Log::info('New token received.', $tokenEndDate);

        return $tokenEndDate;
    }

    public function createAccount($data)
    {
        $accessToken = $this->getValidToken()['access_token'];

         Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post("{$this->apiUrl}/Accounts", [
            'data' => [[
                'Account_Name' => $data['account_name'],
                'Website' => $data['account_website'],
                'Phone' => $data['account_phone'],
            ]]
        ]);
    }

    public function createDeal($data)
    {
        $accessToken = $this->getValidToken()['access_token'];
        
        Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
        ])->post("{$this->apiUrl}/Deals", [
            'data' => [[
                'Owner' => ['id' => $data['owner']],
                'Account_Name' => ['id' => $data['account_name']],
                'Contact_Name' => ['id' => $data['contact_name']],
                'Campaign_Source' => ['id' => $data['campaign_source']],
                "Deal_Name" => $data['deal_name'],
                'Stage' => $data['stage'],
            ]]
        ]);
    }

    public function getAccounts()
    {
        $accessToken = $this->getValidToken()['access_token'];

        $owners = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
        ])->get("{$this->apiUrl}/Accounts")->json('data');
        
        return $owners;
    }

    public function getDeals()
    {
        $accessToken = $this->getValidToken()['access_token'];

        $deals = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
        ])->get("{$this->apiUrl}/Deals")->json('data');

        return $deals;
    }

    public function getContacts()
    {
        $accessToken = $this->getValidToken()['access_token'];

        $contacts = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
        ])->get("{$this->apiUrl}/Contacts")->json('data');

        return $contacts;
    }

    public function getCampaigns()
    {
        $accessToken = $this->getValidToken()['access_token'];

        $campaigns = Http::withHeaders([
            'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
        ])->get("{$this->apiUrl}/Campaigns")->json('data');

        return $campaigns;
    }
}
