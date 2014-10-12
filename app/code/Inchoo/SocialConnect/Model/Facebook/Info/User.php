<?php

namespace Inchoo\SocialConnect\Model\Facebook\Info;

class User extends \Inchoo\SocialConnect\Model\Facebook\Info
{

    /**
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     *
     * @var \Inchoo\SocialConnect\Helper\Data
     */
    protected $_helper;

    /**
     *
     * @var \Inchoo\SocialConnect\Helper\Facebook
     */
    protected $_helperFacebook;

    /**
     *
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Inchoo\SocialConnect\Helper\Data $helper
     * @param \Inchoo\SocialConnect\Helper\Facebook $helperFacebook
     * @param \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client $client
     * @param array $params
     * @param string $target
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Inchoo\SocialConnect\Helper\Data $helper,
        \Inchoo\SocialConnect\Helper\Facebook $helperFacebook,

        // Parent
        \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client $client,
        array $params = array(
            'fields' => 'id,name,first_name,last_name,link,birthday,gender,email,picture.type(large)'
        ),
        $target = 'me',
        array $data = array())
    {
        $this->_customerFactory = $customerFactory;
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->_helperFacebook = $helperFacebook;

        parent::__construct($client, $params, $target, $data);
    }


    public function loadByCustomerId($customerId)
    {
        $this->_customer = $this->_customerFactory->create()->load($customerId);

        if(!$this->_customer->getId()) {
            throw new \Magento\Framework\Exception(
                __('Could not load by customer id')
            );
        }

        if(!($socialconnectFid = $this->_customer->getInchooSocialconnectFid()) ||
            !($socialconnectFtoken = $this->_customer->getInchooSocialconnectFtoken())) {
            throw new \Magento\Framework\Exception(
                __('Could not retrieve token by customer id')
            );
        }

        $this->setTarget($socialconnectFid);

        $this->setAccessToken($socialconnectFtoken);

        $this->_load();

        return $this;
    }

    /**
     * Load customer user info
     *
     * @throws \Magento\Framework\Exception
     * @return \Inchoo\SocialConnect\Model\Facebook\Info\User
     */
    public function loadSelf()
    {
        if(!$this->_customerSession->isLoggedIn()) {
            if(!$this->_customer->getId()) {
                throw new \Magento\Framework\Exception(
                    __('Could not load self since customer isn\'t logged in')
                );
            }
        }

        $this->_customer = $this->_customerSession->getCustomer();

        if(!$this->_customer->getId()) {
            throw new \Magento\Framework\Exception(
                __('Could not load by customer id')
            );
        }

        if(!($socialconnectFid = $this->_customer->getInchooSocialconnectFid()) ||
            !($socialconnectFtoken = $this->_customer->getInchooSocialconnectFtoken())) {
            throw new \Magento\Framework\Exception(
                __('Could not retrieve token by customer id')
            );
        }

        $this->setAccessToken($socialconnectFtoken);

        $this->_load();

        return $this;
    }

    /**
     *
     * @param \Exception $e
     * @throws \Exception
     */
    protected function _onException(\Exception $e)
    {

        $this->_helperFacebook->disconnect($this->_customer);

        parent::_onException($e);
    }
    
}