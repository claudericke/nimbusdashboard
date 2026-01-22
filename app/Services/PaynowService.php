<?php

class PaynowService
{
    public function generatePaymentUrl($invoiceId, $amount, $reference, $email)
    {
        $merchantEmail = 'billing@driftnimbus.com';

        // 1. Construct arguments and URL encode values (http_build_query does this)
        $args = [
            'search' => $merchantEmail,
            'amount' => number_format((float) $amount, 2, '.', ''), // Ensure correct decimal format
            'reference' => $reference,
            'l' => '1'
        ];

        $queryString = http_build_query($args);
        // Example: search=billing%40driftnimbus.com&amount=12.50&reference=INV-001&l=1

        // 2. Base64 encode the arguments string
        $base64Encoded = base64_encode($queryString);

        // 3. URL encode the Base64 string
        $finalEncoded = urlencode($base64Encoded);

        // 4. Construct final URL
        return "https://www.paynow.co.zw/payment/link/{$email}?q={$finalEncoded}";
    }
}
