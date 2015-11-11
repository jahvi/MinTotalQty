<?php
/**
 * MinTotalQty data helper
 *
 * @category Jvs
 * @package  Jvs_MinTotalQty
 * @author   Javier Villanueva <javiervd@gmail.com>
 */
class Jvs_MinTotalQty_Helper_Data extends Mage_CatalogInventory_Helper_Minsaleqty
{
    const XML_PATH_MIN_TOTAL_QTY = 'cataloginventory/options/min_total_qty';

    /**
     * Retrieve min_total_qty value from config
     *
     * @param  int   $customerGroupId
     * @param  mixed $store
     * @return float|null
     */
    public function getConfigValue($customerGroupId, $store = null)
    {
        $value = Mage::getStoreConfig(self::XML_PATH_MIN_TOTAL_QTY, $store);
        $value = $this->_unserializeValue($value);
        if ($this->_isEncodedArrayFieldValue($value)) {
            $value = $this->_decodeArrayFieldValue($value);
        }
        $result = null;
        foreach ($value as $groupId => $qty) {
            if ($groupId == $customerGroupId) {
                $result = $qty;
                break;
            } else if ($groupId == Mage_Customer_Model_Group::CUST_GROUP_ALL) {
                $result = $qty;
            }
        }
        return $this->_fixQty($result);
    }

    /**
     * Check if quote meets the minimun quantity
     * of total items for a specific customer
     *
     * @todo   Change to more meaningful name
     *
     * @param  Mage_Sales_Model_Quote       $quote
     * @param  Mage_Customer_Model_Customer $customer
     * @return int|false
     */
    public function minimunOrderQty(Mage_Sales_Model_Quote $quote, Mage_Customer_Model_Customer $customer)
    {
        $minQtyForCustomer = $this->getConfigValue($customer->getGroupId());

        if ($quote->getItemsQty() < $minQtyForCustomer && $quote->getItemsQty() !== 0) {
            return $minQtyForCustomer;
        }

        return false;
    }

    /**
     * Check if current page is shopping cart
     *
     * @return boolean
     */
    public function isCartPage()
    {
        $frontController = Mage::app()->getFrontController();

        return ($frontController->getAction()->getFullActionName() === 'checkout_cart_index');
    }
}