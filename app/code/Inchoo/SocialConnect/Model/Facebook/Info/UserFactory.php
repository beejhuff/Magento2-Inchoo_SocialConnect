<?php

namespace Inchoo\SocialConnect\Model\Facebook\Info;


/**
 * Factory class for \Inchoo\SocialConnect\Model\Facebook\Info\User
 */
class UserFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName;

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var array
     */
    protected $_instance = array();

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        $instanceName = '\Inchoo\SocialConnect\Model\Facebook\Info\User')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->_customerSession = $customerSession;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param int $customerId
     * @throws \Magento\Framework\Exception
     * @return \Inchoo\SocialConnect\Model\Facebook\Info\User
     */
    public function create($customerId = 0)
    {
        if(!isset($this->_instance[$customerId])) {
            $instance = $this->_objectManager->create('\Inchoo\SocialConnect\Model\Facebook\Info\User');
            /* @var $instance \Inchoo\SocialConnect\Model\Facebook\Info\User */

            if($customerId) {
                $instance->loadByCustomerId($customerId);
            } else if($this->_customerSession->isLoggedIn()) {
                $instance->loadSelf();
            } else {
                throw new \Magento\Framework\Exception(
                    'Could not create user info object. Please try again.'
                );
            }
        }

        // Currently logged in user info under 0 key
        return $this->_instance[$customerId];
    }

}