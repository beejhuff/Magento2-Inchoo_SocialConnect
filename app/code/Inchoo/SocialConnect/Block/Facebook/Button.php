<?php

namespace Inchoo\SocialConnect\Block\Facebook;

class Button extends \Magento\Framework\View\Element\Template
{

    const AJAX_ROUTE = 'socialconnect/facebook/connect';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * Facebook client model
     *
     * @var \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client
     */
    protected $_clientFacebook;

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client $clientFacebook
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client $clientFacebook,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,

        // Parent
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = array())
    {

        $this->_clientFacebook = $clientFacebook;
        $this->_registry = $registry;
        $this->_customerSession = $customerSession;

        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        // CSRF protection
        $this->_customerSession->setFacebookCsrf($csrf = md5(uniqid(rand(), true)));

        $this->_clientFacebook->setState($csrf);
    }

    public function getButtonText()
    {
        $userInfo = $this->_registry->registry('inchoo_socialconnect_facebook_userinfo');

        if (is_null($userInfo) || !$userInfo->hasData()) {
            if (!($text = $this->_registry->registry('inchoo_socialconnect_button_text'))) {
                $text = __('Connect');
            }
        } else {
            $text = __('Disconnect');
        }

        return $text;
    }

    /**
     * @return array
     */
    public function getScope()
    {
        return $this->_clientFacebook->getScope();
    }

    /**
     * @return mixed|string
     */
    public function getAppId()
    {
        return $this->_clientFacebook->getClientId();
    }


    /**
     * @return string
     */
    public function getState()
    {
        return $this->_clientFacebook->getState();
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl(self::AJAX_ROUTE);
    }

}