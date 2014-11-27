<?php

require_once(dirname(__FILE__) . "/lib/prestashop/package.php");

class SeqrConfirmationController extends PsSeqrFrontController {

    public function SeqrConfirmationController() {

        parent::__construct();

        $this->assignBreadcrumb();
        $this->assignNavigation();
    }

    public function execute() {

        $orderId = Tools::getValue("orderId");

        if (!isset($orderId)) {
            $this->failed();
            return;
        }

        $order = new Order($orderId);
        $service = new PsSeqrService($this->config, $order);

        $seqrData = $service->getInvoiceData();

        if ($seqrData->status === SeqrConfig::SEQR_PAYMENT_PAID) {
            $this->succeed();
        } else if ($seqrData->status === SeqrConfig::SEQR_PAYMENT_CANCELED) {
            $this->cancelled();
        } else {
            $this->failed();
        }
    }

    private function failed() {
        $this->display('payment_failed.tpl');
    }

    private function succeed() {
        $this->display('payment_succeed.tpl');
    }

    private function cancelled() {
        $this->display('payment_cancelled.tpl');
    }

}


require(dirname(__FILE__) . '/../../config/config.inc.php');
require(dirname(__FILE__) . '/../../header.php');

$useSSL = true;
$confirmation = new SeqrConfirmationController();
$confirmation->execute();

require(dirname(__FILE__) . '/../../footer.php');