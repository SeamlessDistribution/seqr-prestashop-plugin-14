<?php
/**
 * Created by IntelliJ IDEA.
 * User: kmanka
 * Date: 03/11/14
 * Time: 14:28
 */

include_once(dirname(__FILE__) . "/../model/SeqrInvoiceFactory.php");
include_once(dirname(__FILE__) . "/../model/SeqrInvoice.php");

class PsFactory extends SeqrInvoiceFactory {

    protected function createInvoice($order, SeqrInvoice &$seqrInvoice) {

        if (isset($order) && isset($seqrInvoice)) {

            $seqrInvoice->setId($order->id);
            $seqrInvoice->setInvoiceNumber($order->invoice_number);
            $seqrInvoice->setTotalPriceInclTax($order->total_paid);
            $seqrInvoice->setDiscountAmount($order->total_discounts);
            $seqrInvoice->setShippingInclTax($order->total_shipping);

            $tax = $this->getCarrierTaxAmount($order);
            $seqrInvoice->setShippingTaxAmount($tax->rate);

            $currency = new Currency($order->id_currency);
            $seqrInvoice->setOrderCurrencyCode($currency->iso_code);
        } else {
            throw new Exception("No order found");
        }
    }

    protected function createItem($orderItem, SeqrItem &$seqrItem) {

        if(isset($orderItem) && isset($seqrItem)) {

            $seqrItem->setName($orderItem['product_name']);
            $seqrItem->setPriceInclTax($orderItem['product_price']);
            $seqrItem->setQuantity($orderItem['product_quantity']);
            $seqrItem->setSku($orderItem['product_ean13']);
            $seqrItem->setTaxRate($orderItem['tax_rate']);
            $seqrItem->setTotalPriceInclTax(
                intval($orderItem['product_quantity']) * floatval($orderItem['product_price'])
            );
            $seqrItem->setUnit(""); // todo: change unit type
        }
    }

    protected function getItems($order) {

        if (isset($order)) {
            return $order->getProductsDetail();
        }
    }

    /**
     * @param $order
     * @return Tax
     */
    protected function getCarrierTaxAmount($order) {
        $carrier = new Carrier($order->id_carrier);
        $tax = new Tax($carrier->id_tax);
        return $tax;
    }
}