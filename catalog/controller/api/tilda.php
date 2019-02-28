<?php
class ControllerApiTilda extends Controller {
    public function index() {
        $json = array();
        $api_info = true;
        if ($api_info) {
            $new_session_id = isset($this->request->request['session_id']) ? $this->request->request['session_id'] : '';
            $this->session->start($new_session_id);
            if (!$json) {
                $json['cart_quantity'] = $this->cart->countProducts();
            } else {
                $json['error']['key'] = $this->language->get('error_key');
            }
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->addHeader('Access-Control-Allow-Origin: *');
        $this->response->setOutput(json_encode($json));
    }
}
