<?php

class BillingController extends BaseController
{
    private $zohoService;
    private $paynowService;

    public function __construct()
    {
        $this->zohoService = new ZohoService();
        $this->paynowService = new PaynowService();
    }

    public function index()
    {
        $this->requireAuth();

        $domain = Session::getDomain();
        $email = '';

        // Fetch user email if available
        $userId = Session::get('user_id');
        if ($userId) {
            $userModel = new User();
            $user = $userModel->find($userId);
            $email = $user['email'] ?? '';
        }

        $invoices = $this->zohoService->getInvoices($domain, $email);

        // Generate payment URLs for unpaid invoices
        foreach ($invoices as &$invoice) {
            if ($invoice['status'] === 'unpaid' && $invoice['balance'] > 0) {
                $invoice['payment_url'] = $this->paynowService->generatePaymentUrl(
                    $invoice['invoice_id'],
                    $invoice['balance'],
                    $invoice['invoice_number'],
                    Session::get('profile_name') . '@' . $domain
                );
            }
        }

        $this->view('billing/index', ['invoices' => $invoices]);
    }
}
