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
        $openTickets = [];
        if (Session::isSuperuser()) {
            $trelloService = new TrelloService();
            $openTickets = $trelloService->getOpenTickets();
        }

        $zohoService = new ZohoService();
        $invoices = $zohoService->getInvoices(Session::getDomain());

        // Normalize Disk Usage (Resilient check for list or assoc array)
        $normalizedDisk = null;
        if (isset($diskData['data']) && !empty($diskData['data'])) {
            if (is_array($diskData['data'])) {
                // If it's a list, get the first one. If it's an assoc array, get the first element.
                $normalizedDisk = reset($diskData['data']);
            }
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
