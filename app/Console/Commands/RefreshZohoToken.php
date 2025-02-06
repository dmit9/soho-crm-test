<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZohoService;

class RefreshZohoToken extends Command
{
    protected $signature = 'zoho:refresh-token';
    protected $description = 'Refresh Zoho CRM access token';

    protected $zohoService;

    public function __construct(ZohoService $zohoService)
    {
        parent::__construct();
        $this->zohoService = $zohoService;
    }

    public function handle()
    {
        $this->info('Refreshing Zoho token...');

        // Метод обновления токена в сервисе
        $this->zohoService->getAccessToken();

        $this->info('Zoho token refreshed successfully!');
    }
}
