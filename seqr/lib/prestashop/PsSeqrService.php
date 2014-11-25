<?php

include_once(dirname(__FILE__) . "/../../lib/service/SeqrService.php");
include_once(dirname(__FILE__) . "/../../lib/prestashop/PsFactory.php");

/**
 * Class PsSeqrService
 *
 * SEQR service implementation for Prestashop.
 */
class PsSeqrService extends SeqrService {

    public function PsSeqrService(SeqrConfig $config, $order = null) {

        parent::__construct($config, new PsFactory());

        if (isset($order)) {
            $this->createInvoice($order);
            $this->seqrRepo = new SeqrDataRepository($this->getOrderId());
        }
    }

    protected function saveSeqrData(SeqrData $data) {

        if (isset($data)) {
            $this->seqrRepo->save($data);
        }
    }

    protected function getSeqrData() {

        if($this->loaded) {
            return $this->seqrRepo->load();
        }
        return null;
    }

    public function getCheckStatusUrl() {
        return urlencode(
            Tools::getHttpHost(true, true) . __PS_BASE_URI__
            . 'modules/seqr/checkstatus.php?orderId=' . $this->order->getId()
        );
    }

    public function getBackUrl() {
        return Tools::getHttpHost(true, true) . __PS_BASE_URI__
            . 'modules/seqr/confirmation.php?orderId=' . $this->order->getId();
    }

    public function changeOrderStatus($status) {

        $this->throwExceptionIfNotLoaded();

        $orderHistory = new OrderHistory();
        $orderHistory->id_order = intval($this->order->getId());

        if ($status === SeqrConfig::SEQR_PAYMENT_PAID) {
            $this->updateOrderStatus($orderHistory, _PS_OS_PAYMENT_);
        }

        if ($status === SeqrConfig::SEQR_PAYMENT_CANCELED) {
            $this->updateOrderStatus($orderHistory, _PS_OS_CANCELED_);
        }

        if ($status === SeqrConfig::SEQR_PAYMENT_ERROR) {
            $this->updateOrderStatus($orderHistory, _PS_OS_ERROR_);
        }
    }

    private function updateOrderStatus($orderHistory, $status) {

        $orderHistory->changeIdOrderState($status, $orderHistory->id_order);
        $orderHistory->add(true);
    }
}

/**
 * Class SeqrDataRepository
 */
class SeqrDataRepository {

    private $orderId = null;

    public function SeqrDataRepository($orderId) {
        $this->orderId = $orderId;
    }

    /**
     * Loads seqr data from database.
     * @return bool|SeqrData
     */
    public function load() {

        $result = Db::getInstance()->getRow("SELECT seqr_data FROM "
            . _DB_PREFIX_ . "orders WHERE id_order = " . intval($this->orderId));

        return isset($result['seqr_data']) ? new SeqrData(json_decode($result['seqr_data'])) : false;
    }

    /**
     * Saves seqr data into database.
     * @param SeqrData $data
     */
    public function save(SeqrData $data) {

        Db::getInstance()->Execute("UPDATE " . _DB_PREFIX_ . "orders SET seqr_data='"
            . json_encode($data->toJson()) . "' WHERE id_order=" . intval($this->orderId));
    }
}