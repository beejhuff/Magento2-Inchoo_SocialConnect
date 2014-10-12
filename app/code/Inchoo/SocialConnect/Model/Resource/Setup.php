<?php
/**
 * Resource Setup Model
 */
namespace Inchoo\SocialConnect\Model\Resource;

class Setup extends \Magento\Eav\Model\Entity\Setup
{
    protected $_customerAttributes = array();

    /**
     *
     * @param array $customerAttributes
     * @return \Inchoo\SocialConnect\Model\Resource\Setup
     */
    public function setCustomerAttributes(array $customerAttributes)
    {
        $this->_customerAttributes = $customerAttributes;

        return $this;
    }

   /**
     * Add our custom attributes
     *
     * @return \Inchoo\SocialConnect\Model\Resource\Setup
     */
    public function installCustomerAttributes()
    {
        foreach ($this->_customerAttributes as $code => $attr) {
            $this->addAttribute(\Magento\Customer\Model\Customer::ENTITY, $code, $attr);
        }

        return $this;
    }

    /**
     * Remove custom attributes
     *
     * @return \Inchoo\SocialConnect\Model\Resource\Setup
     */
    public function removeCustomerAttributes()
    {
        foreach ($this->_customerAttributes as $code => $attr) {
            $this->removeAttribute('customer', $code);
        }

        return $this;
    }

}
