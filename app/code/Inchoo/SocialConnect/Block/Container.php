<?php

namespace Inchoo\SocialConnect\Block;


abstract class Container  extends \Magento\Framework\View\Element\Template
{
    /**
     * Facebook client model
     *
     * @var \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client
     */
    protected $_clientFacebook;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @param \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client $clientFacebook
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client $clientFacebook,
        \Magento\Framework\Registry $registry,

        // Parent
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = array())
    {
        $this->_clientFacebook = $clientFacebook;
        $this->_registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function facebookEnabled()
    {
        return $this->_clientFacebook->isEnabled();
    }

} 