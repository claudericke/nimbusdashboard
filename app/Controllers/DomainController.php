<?php

class DomainController extends BaseController {
    private $cpanelService;

    public function __construct() {
        $this->cpanelService = new CpanelService();
    }

    public function index() {
        $this->requireAuth();

        $domainsData = $this->cpanelService->getDomains();
        $domains = $domainsData['data']['main_domain'] ?? null;
        $subdomains = $domainsData['data']['sub_domains'] ?? [];
        $addonDomains = $domainsData['data']['addon_domains'] ?? [];

        $this->view('domains/index', [
            'mainDomain' => $domains,
            'subdomains' => $subdomains,
            'addonDomains' => $addonDomains
        ]);
    }
}
