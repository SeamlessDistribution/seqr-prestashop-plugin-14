<?php

require_once(dirname(__FILE__) . "/lib/seqr_package.php");

/**
 * Class SeqrPaymentController
 *
 * Controller prints payment summary page.
 */
class SeqrPaymentController extends PsSeqrFrontController {

    public function __construct(Cart $cart) {

        parent::__construct();

        $this->cart = $cart;
    }

    public function execute() {

        $currency = new Currency($this->cart->id_currency);

        $this->assignBreadcrumb();
        $this->smarty->assign(array(
            'nbProducts' => $this->cart->nbProducts(),
            'cust_currency' => $this->cart->id_currency,
            'currency' => $currency,
            'total' => $this->cart->getOrderTotal(true)
        ));

        $this->display("payment.tpl");
    }
}


require(dirname(__FILE__) . '/../../config/config.inc.php');
require(dirname(__FILE__) . '/../../header.php');

$useSSL = true;

global $cart;
$payment = new SeqrPaymentController($cart);
$payment->verifyUserLogged();
$payment->execute();

require(dirname(__FILE__) . '/../../footer.php');