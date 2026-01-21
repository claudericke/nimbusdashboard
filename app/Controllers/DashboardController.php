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
        $normalizedDisk = [
            'used' => 0,
            'limit' => 0,
            'percentage' => 0
        ];

        if (isset($diskData['data']) && !empty($diskData['data'])) {
            $raw = is_array($diskData['data']) ? reset($diskData['data']) : $diskData['data'];

            // Map keys from cPanel UAPI Quota::get_quota_info or StatsBar::get_stats
            $used = $raw['megabytes_used'] ?? $raw['usage'] ?? $raw['used'] ?? 0;
            $limit = $raw['megabytes_limit'] ?? $raw['limit'] ?? 0;

            // Handle "unlimited" or 0 as unlimited
            if ($limit === 'unlimited' || (int) $limit === 0) {
                $limit = 0;
            } else {
                $limit = (int) $limit;
            }

            $used = (int) $used;
            $percentage = ($limit > 0) ? round(($used / $limit) * 100) : 0;

            $normalizedDisk = [
                'used' => $used,
                'limit' => $limit,
                'percentage' => $percentage
            ];
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
