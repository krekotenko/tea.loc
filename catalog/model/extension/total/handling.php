<?php
class ModelExtensionTotalHandling extends Model {
	public function getTotal($total) {
		if (($this->cart->getSubTotal() > $this->config->get('total_handling_total')) && ($this->cart->getSubTotal() > 0)) {
			$this->load->language('extension/total/handling');
            $ttt = $this->cart;
			$value = $this->config->get('total_handling_fee');


           /* if (isset($this->session->data['payment_method']['code']) && $this->session->data['payment_method']['code'] != 'cod' ) {
                return;
            }

            if (isset($this->session->data['shipping_method']['code']) && $this->session->data['shipping_method']['code'] != 'flat.flat' ) {
                return;
            }

            foreach ($total['totals'] as $item) {
                if ($item['code'] === 'shipping') {
                    if($item['title'] != 'Česká pošta' && $item['title'] != 'Czech Post') {
                        return;
                    }
                }
			}*/

           $value =  $this->config->get("payment_{$this->session->data['payment_method']['code']}_total");
			$total['totals'][] = array(
				'code'       => 'handling',
				'title'      => $this->session->data['payment_method']['title'],
				'value'      => $value,
				'sort_order' => $this->config->get('total_handling_sort_order')
			);

			if ($this->config->get('total_handling_tax_class_id')) {
				$tax_rates = $this->tax->getRates($this->config->get('total_handling_fee'), $this->config->get('total_handling_tax_class_id'));

				foreach ($tax_rates as $tax_rate) {
					if (!isset($total['taxes'][$tax_rate['tax_rate_id']])) {
						$total['taxes'][$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					} else {
						$total['taxes'][$tax_rate['tax_rate_id']] += $tax_rate['amount'];
					}
				}
			}

			$total['total'] += $value;
		}
	}
}