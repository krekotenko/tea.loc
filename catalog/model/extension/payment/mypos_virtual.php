<?php
class ModelExtensionPaymentMyposVirtual extends Model {
	public function getMethod($address, $total) {
		$this->load->language('extension/payment/mypos_virtual');

        $status = false;

        $currencies = array(
            'BGN', 'USD', 'EUR', 'GBP', 'CHF', 'JPY', 'RON', 'HRK', 'NOK', 'SEK', 'CZK', 'HUF', 'PLN', 'DKK', 'ISK',
        );

        foreach ($currencies as $currency) {
            if ($this->currency->has($currency)) {
                $status = true;
                break;
            }
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code'       => 'mypos_virtual',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('payment_mypos_virtual_sort_order')
            );
        }

        return $method_data;
	}
}