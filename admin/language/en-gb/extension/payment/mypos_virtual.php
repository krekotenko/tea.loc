<?php
// Heading
$_['heading_title']					= 'myPOS Checkout';

// Text
$_['text_payment']					= 'Payment';
$_['text_success']					= 'Success: You have modified myPOS Checkout payment module!';
$_['text_edit']                     = 'Edit myPOS Checkout';
$_['text_mypos_virtual'] = '<a onclick="window.open(\'https://mypos.eu\');"><img src="view/image/payment/mypos.png" alt="myPOS Checkout" title="myPOS Checkout" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_mypos_join']				= 'To use this payment option you need to <a href="https://mypos.eu/en/register/" target="_blank">sign up</a> for a myPOS account.';
$_['text_full_requests'] = 'Full payment form';
$_['text_simplified_request'] = 'Simplified payment form';
$_['text_simplified_payment_page'] = 'Ultra-simplified payment form';

// Entry
$_['entry_status']			    = 'Status';
$_['entry_logging']  	        = 'Logging';
$_['entry_test']		        = 'Test Mode';
$_['entry_sid']				    = 'Store ID';
$_['entry_wallet_number']	    = 'Client Number';
$_['entry_private_key']		    = 'Private Key';
$_['entry_public_certificate']  = 'myPOS Public Certificate';
$_['entry_version']			    = 'API Version';
$_['entry_developer_url']	    = 'Developer URL';
$_['entry_production_url']	    = 'Production URL';
$_['entry_sort_order']  	    = 'Sort Order';
$_['entry_developer_keyindex']  = 'Developer Key Index';
$_['entry_production_keyindex'] = 'Production Key Index';
$_['entry_ppr']                 = 'Checkout form view';

// Tooltip
$_['tooltip_sid'] = 'Store ID is given when you add a new online store. It could be reviewed in your online banking at www.mypos.eu > menu Online > Online stores.';
$_['tooltip_wallet_number'] = 'You can view your myPOS Client number in your online banking at www.mypos.eu.';
$_['tooltip_private_key'] = 'The Private Key for your store is generated in your online banking at www.mypos.eu > menu Online > Online stores > Keys.';
$_['tooltip_public_certificate'] = 'The myPOS Public Certificate is available for download in your online banking at www.mypos.eu > menu Online > Online stores > Keys.';
$_['tooltip_keyindex'] = 'The Key Index assigned to the certificate could be reviewed in your online banking at www.mypos.eu > menu Online > Online stores > Keys.';
$_['tooltip_ppr'] = '<strong style=&quot;text-decoration: underline;&quot;>Full payment form</strong><br/>When you choose the &quot;Full payment form&quot;, you can collect detailed customer information on checkout - customer names, address, phone number and email. Have in mind, that if your website has a shipping form, customer should double type some of the details. All fields are mandatory. Names and email address are not editable on the payment page.<br/><br/><strong style=&quot;text-decoration: underline;&quot;>Simplified payment form</strong><br/>Similar to the &quot;Full payment form&quot;. However, customer names and email addresses are editable on the payment page.<br/><br/><strong style=&quot;text-decoration: underline;&quot;>Ultra-simplified payment form</strong><br/>The most basic payment form - it requires only card details. Use this only if you collect customer details on a prior page.';

// Help
$_['help_total']					= 'The checkout total the order must reach before this payment method becomes active.';

// Error
$_['error_permission']				= 'Warning: You do not have permission to modify myPOS Checkout payment!';