<?php

require_once(dirname(__FILE__) . "/lib/seqr_package.php");

class SeqrCheckStatusController extends PsSeqrFrontController {

    public function SeqrCheckStatusController() {

        parent::__construct();

        $this->config = new PsConfig();
        $this->config->load();
    }

    public function execute() {

        try {
            $orderId = Tools::getValue("orderId");

            if(!isset($orderId)) {
                $this->respondWithError("No order found");
            }

            $order = new Order($orderId);
            $service = new PsSeqrService($this->config, $order);
            $status = $service->getPaymentStatus();

            $this->respond(json_encode($status));

        } catch (Exception $e) {
            $this->respondWithError('Payment checking error');
        }
    }

    private function respond($data) {
        die($data);
    }

    private function respondWithError($message) {
        $this->respond(json_encode(array('error' => $message)));
    }


}

require_once(dirname(__FILE__) . '/../../config/config.inc.php');

$useSSL = true;

header('Content-type: application/json');

$checkStatus = new SeqrCheckStatusController();
$checkStatus->execute();


