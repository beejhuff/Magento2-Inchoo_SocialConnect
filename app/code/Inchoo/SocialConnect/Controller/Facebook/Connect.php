<?php

namespace Inchoo\SocialConnect\Controller\Facebook;

class Connect extends \Inchoo\SocialConnect\Controller\Facebook
{

    /**
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_connectCallback();
        } catch (\Inchoo\SocialConnect\Model\Facebook\Oauth2\Exception $e) {
            $this->messageManager->addNotice($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $this->_sendResponse();
    }

    /**
     * @throws \Magento\Framework\Exception
     */
    protected function _connectCallback() {
        $state = $this->getRequest()->getParam('state');
        $accessToken = $this->getRequest()->getParam('access_token');
        $expiresIn = $this->getRequest()->getParam('expires_in');

        if( !$expiresIn ||
            !$accessToken ||
            !$state ||
            $state != $this->_customerSession->getFacebookCsrf()) {
            // Direct route access - deny
            return $this;
        }

        $token = new \stdClass();
        $token->access_token = $accessToken;
        $token->expires = $expiresIn;

        $info = $this->_infoFactory->create($token);

        // Reload access token in case it got extended
        $token = $info->getAccessToken();

        $customersByFacebookId = $this->_helperFacebook->getCustomersByFacebookId($info->getId());
        /* @var $customersByFacebookId \Magento\Customer\Model\Resource\Customer\Collection */

        if($this->_customerSession->isLoggedIn()) {
            // Logged in user
            if($customersByFacebookId->getSize()) {
                // Facebook account already connected to other account - deny
                $this->messageManager->addNotice(
                    __('Your Facebook account is already connected to one of our store accounts.')
                );

                return $this;
            }

            // Connect from account dashboard - attach
            $customerId = $this->_customerSession->getCustomerId();

            $this->_helperFacebook->connectByFacebookId(
                $info->getId(),
                $token,
                $customerId
            );

            $this->messageManager->addSuccess(
                __('Your Facebook account is now connected to your store account. You can now login using our Facebook '.
                    'Login button or using store account credentials you will receive to your email address.')
            );

            return $this;
        }

        if($customersByFacebookId->getSize()) {
            // Existing connected user - login
            $customer = $customersByFacebookId->getFirstItem();
            /* @var $customer \Magento\Customer\Model\Customer */

            $this->_helperFacebook->loginByCustomer($customer);

            $this->messageManager->addSuccess(
                __('You have successfully logged in using your Facebook account.')
            );

            return $this;
        }

        $customersByEmail = $this->_helperFacebook->getCustomersByEmail($info->getEmail());
        /* @var $customersByEmail \Magento\Customer\Model\Resource\Customer\Collection */

        if($customersByEmail->getSize()) {
            // Email account already exists - attach, login
            $customerId = $customersByEmail->getFirstItem()->getId();

            $this->_helperFacebook->connectByFacebookId(
                $info->getId(),
                $token,
                $customerId
            );

            $this->messageManager->addSuccess(
                __('We have discovered you already have an account at our store. Your Facebook account is now connected '.
                    'to your store account.')
            );

            return $this;
        }

        // New connection - create, attach, login
        $firstName = $info->getFirstName();
        if(!$firstName) {
            throw new \Magento\Framework\Exception(
                __('Sorry, could not retrieve your Facebook first name. Please try again.')
            );
        }

        $lastName = $info->getLastName();
        if(!$lastName) {
            throw new \Magento\Framework\Exception(
                __('Sorry, could not retrieve your Facebook last name. Please try again.')
            );
        }

        $this->_helperFacebook->connectByCreatingAccount(
            $info->getId(),
            $token,
            $info->getEmail(),
            $info->getFirstName(),
            $info->getLastName()
        );

        $this->messageManager->addSuccess(
            __('Your Facebook account is now connected to your new user account at our store. Now you can login using '.
                'our Facebook Login button or using store account credentials you will receive to your email address.')
        );
    }

}