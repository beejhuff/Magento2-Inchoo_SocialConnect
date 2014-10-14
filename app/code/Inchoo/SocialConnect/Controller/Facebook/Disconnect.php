<?php

namespace Inchoo\SocialConnect\Controller\Facebook;

class Disconnect extends \Inchoo\SocialConnect\Controller\Facebook
{

    /**
     *
     * @return void
     */
    public function execute()
    {
        $customer = $this->_getSession()->getCustomer();

        try {
            $this->_disconnectCallback($customer);
        } catch (\Inchoo\SocialConnect\Model\Facebook\Oauth2\Exception $e) {
            $this->messageManager->addNotice($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $this->_sendResponse();
    }

    protected function _disconnectCallback(\Magento\Customer\Model\Customer $customer) {
        $this->_helperFacebook->disconnect($customer);

        $this->messageManager->addSuccess(
            __('You have successfully disconnected your Facebook account from our store account.')
        );
    }

}