<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ZohoService;
use Illuminate\Support\Facades\Log;

class ZohoController extends Controller
{
    protected $zohoService;

    public function __construct(ZohoService $zohoService)
    {
        $this->zohoService = $zohoService;
    }

    public function getValidToken()
    {
        $response = $this->zohoService->getValidToken();
        return response()->json($response);
    }

    public function getAccounts()
    {
        $response = $this->zohoService->getAccounts();
        return response()->json($response);
    }

    public function getDeals()
    {
        $response = $this->zohoService->getDeals();
        return response()->json($response);
    }

    public function getContacts()
    {
        $response = $this->zohoService->getContacts();
        return response()->json($response);
    }

    public function getCampaigns()
    {
        $response = $this->zohoService->getCampaigns();
        return response()->json($response);
    }

    public function createAccount(Request $request)
    {
        $request->validate([
            'account_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s!@#\$%\^\&*\)\(+=._-]+$/u'
            ],
            'account_website' => [
                'nullable',
                'string',
                'regex:/^(http:\/\/www\.|https:\/\/www\.|ftp:\/\/www\.|www\.|http:\/\/|https:\/\/|ftp:\/\/|){1}[^\x00-\x19\x22-\x27\x2A-\x2C\x2E-\x2F\x3A-\x3F\x5B-\x5E\x60\x7B\x7D-\x7F]+(\.[^\x00-\x19\x22\x24-\x2C\x2E-\x2F\x3C-\x3E\x40\x5B-\x5E\x60\x7B\x7D-\x7F]+)+([\/\?].*)*$/u'
            ],
            'account_phone' => [
                'nullable',
                'string',
                'max:30',
                'regex:/^([+]?)(?![.-])(?>(?>[.-]?[ ]?[\da-zA-Z]+)+|([ ]?((?![.-])(?>[ .-]?[\da-zA-Z]+)+)(?![.])([ -]?[\da-zA-Z]+)?)+)+(?>(?>([,]+)?[;]?[\da-zA-Z]+)+(([#][\da-zA-Z]+)+)?)?[#;]?$/u'
            ],
        ]);


        $response = $this->zohoService->createAccount($request->all());

        return response()->json($response);
    }

    public function createDeal(Request $request)
    {

        $request->validate([
            'owner' => ['required', 'regex:/^\d{18}$/'],
            'account_name' => ['required', 'regex:/^\d{18}$/'],
            'contact_name' => ['required', 'regex:/^\d{18}$/'],
            'campaign_source' => ['required', 'regex:/^\d{18}$/'],
            'deal_name' => ['required', 'string', 'max:255'],
            'stage' => ['required', 'string', 'max:255'],
        ]);

        $response = $this->zohoService->createDeal($request->all());

        return response()->json($response);
    }
}
