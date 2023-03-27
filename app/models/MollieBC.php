<?php

class MollieBC extends BusinessComponent {

    public function createPayment($hashed_payment_id, $order_id, $total_order_price) {
        if (TEST_MODE === false) {
            $headers = ["Authorization: Bearer " . MOLLIE_API_LIVE];
        } else {
            $headers = ["Authorization: Bearer " . MOLLIE_API_TEST];
        }

        $post = [
            'amount[currency]' => 'EUR',
            'amount[value]' => number_format($total_order_price, 2),
            'description' => 'Order #' . $order_id,
            'redirectUrl' => URL_ROOT . '/Checkout/' . $hashed_payment_id,
            'metadata' => 'order_id=' . $order_id
        ];

        if (strpos(URL_ROOT, 'localhost') === false) {
            $post['webhookUrl'] = URL_ROOT . '/Checkout/webhook/';
        }

        $ch = curl_init('https://api.mollie.com/v2/payments');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response, true);
        
        if (isset($json['_links']['checkout']['href'])) {
            return $json;
        } else {
            return false;
        }
    }

    public function getPayment($payment_id) {
        if (TEST_MODE === false) {
            $headers = ["Authorization: Bearer " . MOLLIE_API_LIVE];
        } else {
            $headers = ["Authorization: Bearer " . MOLLIE_API_TEST];
        }

        $ch = curl_init('https://api.mollie.com/v2/payments/' . $payment_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

}