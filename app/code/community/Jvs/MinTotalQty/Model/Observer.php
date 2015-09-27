<?php
/**
 * MinTotalQty observer model
 *
 * @category Jvs
 * @package  Jvs_MinTotalQty
 * @author   Javier Villanueva <javiervd@gmail.com>
 */
class Jvs_MinTotalQty_Model_Observer
{
    /**
     * Check minimun order totals
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function checkTotalQtyBeforeCheckout(Varien_Event_Observer $observer)
    {
        $quote    = $observer->getDataObject();
        $customer = Mage::helper('customer')->getCustomer();

        // If the minimun total quantity is not met
        // redirect to cart page with error message
        if ($minQty = Mage::helper('jvs_mintotalqty')->minimunOrderQty($quote, $customer)) {
            Mage::getSingleton('checkout/session')->addUniqueMessages(
                Mage::getSingleton('core/message')
                    ->error(
                        Mage::helper('cataloginventory')
                            ->__(
                                'The minimum quantity allowed for purchase is %s.',
                                $minQty
                            )
                    )
            );

            // Check if we are not already on the cart page
            if (!Mage::helper('jvs_mintotalqty')->isCartPage()) {
                Mage::app()->getFrontController()->getResponse()
                    ->setRedirect(Mage::getUrl('checkout/cart'));

                Mage::app()->getRequest()->setDispatched(true);
            }
        }
    }
}