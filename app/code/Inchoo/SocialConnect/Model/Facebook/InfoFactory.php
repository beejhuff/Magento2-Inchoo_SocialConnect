<?php
namespace Inchoo\SocialConnect\Model\Facebook;


/**
 * Factory class for \Inchoo\SocialConnect\Model\Facebook\Info
 */
class InfoFactory
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
     * Used for caching API results
     *
     * @var array
     */
    protected $_instance = array();

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        $instanceName = '\Inchoo\SocialConnect\Model\Facebook\Info')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param \StdClass $accessToken
     * @return \Inchoo\SocialConnect\Model\Facebook\Info
     */
    public function create(\StdClass $accessToken)
    {
        if(!isset($this->_instance[$accessToken->access_token])) {
            $instance = $this->_objectManager->create('\Inchoo\SocialConnect\Model\Facebook\Info');
            /* @var $instance \Inchoo\SocialConnect\Model\Facebook\Info */

            $instance->loadByAccessToken($accessToken);

            $this->_instance[$accessToken->access_token] = $instance;
        }

        return $this->_instance[$accessToken->access_token];
    }

}