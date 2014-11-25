<?php

require_once(dirname(__FILE__) . "/lib/seqr_package.php");
require_once(dirname(__FILE__) . "/../../config/config.inc.php");
require_once(dirname(__FILE__) . "/seqr.php");

class SeqrPaymentCodeController extends PsSeqrFrontController {

    private $service = null;
    private $error= false;

    function __construct(Cart $cart) {

        parent::__construct();

        $this->cart = $cart;
    }

    private function process() {

        try {
            $this->validate();
            $this->placeOrder();
            $this->service = $this->createService();
            $this->service->sendInvoice();

        } catch (Exception $e) {
            $this->service->changeOrderStatus(SeqrConfig::SEQR_PAYMENT_ERROR);
            $this->error = true;
            error_log($e);
        }
    }

    public function execute() {

        $this->process();

        if ($this->error) {
            $this->showPaymentFailed();
            return;
        }

        $cart = $this->cart;
        $currency = new Currency($cart->id_currency);

        $this->assignBreadcrumb();
        $this->smarty->assign(array(
            'nbProducts' => $cart->nbProducts(),
            'orderId' => $this->service->getOrderId(),
            'cust_currency' => $cart->id_currency,
            'currency' => $currency,
            'total' => $cart->getOrderTotal(true),
            'webPluginUrl' => $this->service->getWebPluginUrl(),
            'backUrl' => $this->service->getBackUrl()
        ));

        $this->display("payment_code.tpl");
    }

    /**
     * Validates the current step in the order process.
     * @return mixed
     */
    private function validate() {

        $cart = $this->cart;
        if ($cart->id_customer == 0
            || $cart->id == 0
            || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0) {

            Tools::redirect('order.php?step=1');
        }
    }

    /**
     * Places order with the PREPARATION status.
     * @internal param $cart
     */
    private function placeOrder() {

        $cart = $this->cart;
        $customer = new Customer($cart->id_customer);

        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('order.php?step=1');
        }

        $currency = new Currency($cart->id_currency);
        $total = (float)$cart->getOrderTotal(true);

        $seqr = new Seqr();
        $seqr->validateOrder(
            $cart->id,
            _PS_OS_PREPARATION_,
            $total, "seqr",
            null,
            null,
            (int)$currency->id,
            false,
            $customer->secure_key
        );
    }

    /**
     * @return PsSeqrService
     */
    private function createService() {

        $cart = $this->cart;
        $orderId = Order::getOrderByCartId($cart->id);
        $order = new Order($orderId);
        $service = new PsSeqrService($this->config, $order);

        return $service;
    }

    private function showPaymentFailed() {

        $this->assignBreadcrumb();
        $this->assignNavigation();
        $this->display("payment_failed.tpl");
    }
}

require(dirname(__FILE__) . '/../../config/config.inc.php');
require(dirname(__FILE__) . '/../../header.php');

$useSSL = true;

global $cart;
$payment = new SeqrPaymentCodeController($cart);
$payment->verifyUserLogged();
$payment->execute();

require(dirname(__FILE__) . '/../../footer.php');