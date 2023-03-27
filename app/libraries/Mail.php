<?php

require_once APP_ROOT . '/models/CustomersBC.php';
require_once APP_ROOT . '/models/OrdersBC.php';

class Mail {

    public static function sendOrderConfirmation($order_id) {
        $CustomersBC = new CustomersBC();
        $customer = $CustomersBC->getCustomerFromOrder($order_id);

        $OrdersBC = new OrdersBC();
        $orderlines = $OrdersBC->getOrderLines($order_id);
        $total_price = $OrdersBC->getTotalOrderPrice($order_id);
        
        $to = $customer->EMAIL;
        $subject = "Bevestiging order #" . $order_id . " - " . SITE_NAME;

        $message = "
        <html>
            <head>
                <title" . $subject . "</title>
            </head>
            <body>
                <p>Hoi " . $customer->FIRST_NAME . ",</p>
                <p>Bedankt voor je bestelling! Wij gaan zo spoedig mogelijk voor je aan de slag. Je ordernummer is #" . $order_id . ". Dit nummer kun je als referentie gebruiken als wij hiernaar vragen.</p>
                <p>Hieronder een overzicht van je bestelling:</p>
                
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Aantal</th>
                            <th>Bedrag</th>
                        </tr>
                    </thead>
                    <tbody>
                        " . Mail::orderlinesToHTML($orderlines) . "
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan=" . "2" . ">Totaal</th>
                            <th>€" . number_format($total_price, 2, ',', '.') . "</th>
                        </tr>
                    </tfoot>
                </table>

                <p>We hopen dat je van je bestelling gaat genieten!</p>
                <p>Met vriendelijke groet</p>
                <p>" . SITE_NAME . "</p>
            </body>
        </html>
        ";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        $headers .= 'From: ' . SITE_NAME . ' <' . MAIN_EMAIL . '>' . "\r\n";

        return mail($to, $subject, $message, $headers);
    }

    private static function orderlinesToHTML($orderlines) {
        $html = "";

        foreach ($orderlines as $orderline) {
            $html .= "
            <tr>
                <td>" . $orderline->DESCRIPTION . "</td>
                <td>" . $orderline->AMOUNT . "x</td>
                <td>€" . number_format($orderline->PRICE * $orderline->AMOUNT, 2, ',', '.') . "</td>
            </tr>
            ";
        }

        return $html;
    }

}