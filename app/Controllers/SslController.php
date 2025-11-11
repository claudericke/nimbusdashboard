<?php

class SslController extends BaseController {
    private $cpanelService;

    public function __construct() {
        $this->cpanelService = new CpanelService();
    }

    public function index() {
        $this->requireAuth();

        $sslData = $this->cpanelService->getSslCerts();
        $certs = $sslData['data'] ?? [];

        $this->view('ssl/index', ['certificates' => $certs]);
    }
}
