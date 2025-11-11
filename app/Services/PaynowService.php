<?php

class PaynowService {
    public function generatePaymentUrl($invoiceId, $amount, $reference, $email) {
        $data = [
            'resulturl' => 'https://dashboard.driftnimbus.com/payment-success.php',
            'returnurl' => 'https://dashboard.driftnimbus.com/billing',
            'reference' => $reference,
            'amount' => $amount,
            'id' => '18491',
            'additionalinfo' => $invoiceId,
            'authemail' => $email,
            'status' => 'Message'
        ];

        $encodedData = base64_encode(json_encode($data));
        return "https://www.paynow.co.zw/Payment/Link/?q={$encodedData}";
    }
}
