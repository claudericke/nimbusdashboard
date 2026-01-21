<?php

class DashboardController extends BaseController
{
    private $cpanelService;
    private $weatherService;
    private $quoteModel;

    public function __construct()
    {
        $this->cpanelService = new CpanelService();
        $this->weatherService = new WeatherService();
        $this->quoteModel = new Quote();
    }

    public function index()
    {
        // Get authentic user data for package/name
        $userId = Session::get('user_id');
        $userModel = new User();
        $user = $userModel->find($userId);

        // Get dashboard data
        $diskData = $this->cpanelService->getDiskUsage();
        $sslData = $this->cpanelService->getSslCerts();
        $weather = $this->weatherService->getCurrentWeather();
        $quote = $this->quoteModel->random();
        $serverStatus = $this->cpanelService->checkServerStatus();

        // New Integrations
        $trelloService = new TrelloService();
        $zohoService = new ZohoService();
        $openTickets = $trelloService->getOpenTickets();
        $invoices = $zohoService->getInvoices(Session::getDomain());

        // Normalize Disk Usage
        $normalizedDisk = [];
        if (isset($diskData['data'])) {
            // Check if it's the data object itself (associative) or a list (indexed)
            if (isset($diskData['data']['megabytes_used'])) {
                $normalizedDisk = $diskData['data'];
            } elseif (isset($diskData['data'][0])) {
                $normalizedDisk = $diskData['data'][0];
            }
        }

        // Standardize keys for the view
        if (isset($normalizedDisk['megabyte_limit']) && !isset($normalizedDisk['megabytes_limit'])) {
            $normalizedDisk['megabytes_limit'] = $normalizedDisk['megabyte_limit'];
        }

        $data = [
            'diskUsage' => $normalizedDisk,
            'sslCerts' => $sslData['data'] ?? [],
            'weather' => $weather,
            'weatherIcon' => $weather ? $this->weatherService->getWeatherIcon($weather['weathercode'] ?? 0) : null,
            'quote' => $quote,
            'serverStatus' => $serverStatus,
            'openTickets' => $openTickets,
            'invoices' => $invoices,
            'profileName' => $user['full_name'] ?? Session::get('profile_name'),
            'profilePicture' => $user['profile_picture_url'] ?? Session::get('profile_picture'),
            'packageName' => $user['package'] ?? 'Solopreneur',
            'domain' => Session::getDomain(),
            'isSuperuser' => Session::get('is_superuser')
        ];

        $this->view('dashboard/index', $data);
    }
}
