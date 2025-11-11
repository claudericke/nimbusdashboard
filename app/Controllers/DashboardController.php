<?php

class DashboardController extends BaseController {
    private $cpanelService;
    private $weatherService;
    private $quoteModel;

    public function __construct() {
        $this->cpanelService = new CpanelService();
        $this->weatherService = new WeatherService();
        $this->quoteModel = new Quote();
    }

    public function index() {
        $this->requireAuth();

        // Get dashboard data
        $diskData = $this->cpanelService->getDiskUsage();
        $sslData = $this->cpanelService->getSslCerts();
        $weather = $this->weatherService->getCurrentWeather();
        $quote = $this->quoteModel->random();
        $serverStatus = $this->cpanelService->checkServerStatus();

        $data = [
            'diskUsage' => $diskData['data'] ?? null,
            'sslCerts' => $sslData['data'] ?? [],
            'weather' => $weather,
            'weatherIcon' => $weather ? $this->weatherService->getWeatherIcon($weather['weathercode']) : null,
            'quote' => $quote,
            'serverStatus' => $serverStatus,
            'profileName' => Session::get('profile_name'),
            'profilePicture' => Session::get('profile_picture'),
            'packageName' => Session::get('package_name'),
            'domain' => Session::getDomain(),
        ];

        $this->view('dashboard/index', $data);
    }
}
