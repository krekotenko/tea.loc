<?php

class ControllerExtensionPaymentMyposVirtual extends Controller
{
    /**
     * @var Log $log
     */
    private $log;
    private $isTest;
    private $keyindex;
    private $sid;
    private $wallet_number;
    private $private_key;
    private $public_key;
    private $version;
    private $action;
    private $logging;
    private $paymentParametersRequired;

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->log = new Log('mypos_virtual.log');

        $this->isTest = $this->config->get('payment_mypos_virtual_test') === '1' ? true : false;
        $this->logging = $this->config->get('payment_mypos_virtual_logging') === '1' ? true : false;
        $this->version = '1.3';

        if (!$this->isTest)
        {
            $this->sid           = $this->config->get('payment_mypos_virtual_production_sid');
            $this->wallet_number = $this->config->get('payment_mypos_virtual_production_wallet_number');
            $this->private_key   = $this->config->get('payment_mypos_virtual_production_private_key');
            $this->public_key    = $this->config->get('payment_mypos_virtual_production_public_certificate');
            $this->action        = $this->config->get('payment_mypos_virtual_production_url');
            $this->keyindex      = $this->config->get('payment_mypos_virtual_production_keyindex');
            $this->paymentParametersRequired = $this->config->get('payment_mypos_virtual_production_ppr');
        }
        else
        {
            $this->sid           = $this->config->get('payment_mypos_virtual_developer_sid');
            $this->wallet_number = $this->config->get('payment_mypos_virtual_developer_wallet_number');
            $this->private_key   = $this->config->get('payment_mypos_virtual_developer_private_key');
            $this->public_key    = $this->config->get('payment_mypos_virtual_developer_public_certificate');
            $this->action        = $this->config->get('payment_mypos_virtual_developer_url');
            $this->keyindex      = $this->config->get('payment_mypos_virtual_developer_keyindex');
            $this->paymentParametersRequired = $this->config->get('payment_mypos_virtual_developer_ppr');
        }
    }

	public function index() {
        $this->language->load('extension/payment/mypos_virtual');
        $data['action'] = $this->action;
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['continue'] = $this->url->link('checkout/success');

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data['post'] = $this->create_post($order_info);

        return $this->load->view('extension/payment/mypos_virtual', $data);
	}

    public function callback() {
        if ($this->is_valid_signature($this->request->post))
        {
            if (isset($this->request->post['OrderID'])) {
                $order_id = $this->request->post['OrderID'];
            } else {
                die('Illegal Access');
            }

            $this->load->model('checkout/order');

            if ($this->request->post['IPCmethod'] == 'IPCPurchaseNotify')
            {
                $this->log('Received ' . $this->request->post['IPCmethod'] . ' request for order: ' . $order_id . '.');
                $this->model_checkout_order->addOrderHistory($order_id, 2, 'myPOS Checkout has authorized transaction. Transaction ID: ' . $this->request->post['IPC_Trnref'], true);

                echo 'OK';
                exit;
            }
            else if ($this->request->post['IPCmethod'] == 'IPCPurchaseRollback')
            {
                $this->log('Received ' . $this->request->post['IPCmethod'] . ' request for order: ' . $order_id . '.');
                $this->model_checkout_order->addOrderHistory($order_id, 7, 'myPOS Checkout Checkout has denied transaction.');

                echo 'OK';
                exit;
            }
            else if ($this->request->post['IPCmethod'] == 'IPCPurchaseCancel')
            {
                $this->log('Received ' . $this->request->post['IPCmethod'] . ' request for order: ' . $order_id . '.');
                $this->model_checkout_order->addOrderHistory($order_id, 7, 'User has cancelled the order.');

                $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
                exit;
            }
            else if ($this->request->post['IPCmethod'] == 'IPCPurchaseOK')
            {
                $this->log('Received ' . $this->request->post['IPCmethod'] . ' request for order: ' . $order_id . '.');
                $this->response->redirect($this->url->link('checkout/success'));
                exit;
            }
            else
            {
                $this->log('Invalid method received on notify url. Method name:' . $this->request->post['IPCmethod'] . '.');
                echo 'INVALID METHOD';
                exit;
            }
        }
        else
        {
            echo 'INVALID SIGNATURE';
            exit;
        }
    }

    public function create_post($order_info)
    {
        $post['IPCmethod'] = 'IPCPurchase';
        $post['IPCVersion'] = $this->version;
        $post['IPCLanguage'] = $this->language->get('code');
        $post['WalletNumber'] = $this->wallet_number;
        $post['SID'] = $this->sid;
        $post['keyindex'] = $this->keyindex;
	    $post['Source'] = 'sc_opencart 1.7 ' . PHP_VERSION_ID . ' ' . VERSION;
        $post['CardTokenRequest'] = 0;
        $post['PaymentParametersRequired'] = $this->paymentParametersRequired;

        $post['Amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
        $post['Currency'] = $order_info['currency_code'];
        $post['OrderID'] = $this->session->data['order_id'];
        $post['URL_OK'] = $this->url->link('extension/payment/mypos_virtual/callback', '', true);
        $post['URL_CANCEL'] = $this->url->link('extension/payment/mypos_virtual/callback', '', true);
        $post['URL_Notify'] = $this->url->link('extension/payment/mypos_virtual/callback', '', true);
        $post['CustomerIP'] = $_SERVER['REMOTE_ADDR'];
        $post['CustomerEmail'] = $order_info['email'];
        $post['CustomerFirstNames'] =  $order_info['payment_firstname'];
        $post['CustomerFamilyName'] = $order_info['payment_lastname'];
        $post['CustomerCountry'] = $order_info['payment_iso_code_3'];
        $post['CustomerCity'] = $order_info['payment_city'];
        $post['CustomerZIPCode'] = $order_info['payment_postcode'];
        $post['CustomerAddress'] = $order_info['payment_address_1'];
        $post['CustomerPhone'] = $order_info['telephone'];
        $post['Note'] = 'myPOS Checkout OpenCart Extension';
        $post['CartItems'] = 0;

        $index = 1;

        $data['products'] = array();

        foreach ($this->cart->getProducts() as $product) {
            $post['Article_' . $index] = html_entity_decode(strip_tags($product['name']));
            $post['Quantity_' . $index] = $product['quantity'];
            $post['CartItems'] -= $product['quantity'] - 1;
            $post['Price_' . $index] = $this->currency->format($product['price'], $order_info['currency_code'], false, false);
            $post['Amount_' . $index] = $this->currency->format($product['total'], $order_info['currency_code'], false, false);
            $post['Currency_' . $index] = $order_info['currency_code'];
            $index++;
        }

        // Totals
        $this->load->model('setting/extension');

        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        // Because __call can not keep var references so we put them into an array.
        $total_data = array(
            'totals' => &$totals,
            'taxes'  => &$taxes,
            'total'  => &$total
        );

        $sort_order = array();

        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get('total_' . $result['code'] . '_status')) {
                $this->load->model('extension/total/' . $result['code']);

                // We have to put the totals in an array so that they pass by reference.
                $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
            }
        }

        $sort_order = array();

        foreach ($totals as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $totals);

        $json['totals'] = array();

        foreach ($totals as $total) {
            if ($total['code'] === 'sub_total' || $total['code'] === 'total') {
                continue;
            }

            $post['CartItems'] += 1;
            $post['Article_' . $index] = html_entity_decode(strip_tags($total['title']));
            $post['Quantity_' . $index] = 1;
            $post['Price_' . $index] = $this->currency->format($total['value'], $order_info['currency_code'], false, false);
            $post['Amount_' . $index] = $this->currency->format($total['value'], $order_info['currency_code'], false, false);
            $post['Currency_' . $index] = $order_info['currency_code'];
            $index++;
        }

	    $post['CartItems'] = $index - 1;

        $post['Signature'] = $this->create_signature($post);

        $this->log('Created POST data for order: ' . $post['OrderID'] . '.');

        return $post;
    }

    public function create_signature($post)
    {
        $concData = base64_encode(implode('-', $post));
        $privKeyObj = openssl_get_privatekey($this->private_key);
        openssl_sign($concData, $signature, $privKeyObj, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    public function is_valid_signature($post)
    {
        // Save signature
        $signature = $post['Signature'];

        // Remove signature from POST data array
        unset($post['Signature']);

        // Concatenate all values
        $concData = base64_encode(implode('-', $post));

        // Extract public key from certificate
        $pubKeyId = openssl_get_publickey($this->public_key);

        // Verify signature
        $result = openssl_verify($concData, base64_decode($signature), $pubKeyId, OPENSSL_ALGO_SHA256);

        //Free key resource
        openssl_free_key($pubKeyId);

        if ($result == 1)
        {
            $this->log('Valid signature.');
            return true;
        }
        else
        {
            $this->log('Invalid signature.');
            return false;
        }
    }

    private function log($message) {
        if ($this->logging)
        {
            $this->log->write($message);
        }
    }
}