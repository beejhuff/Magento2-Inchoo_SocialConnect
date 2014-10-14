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
     * @param \Magento\Customer\Model\Session $customerSession
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

    /**
     * @return string
     */
    public function getButtonText()
    {
        // Get user info for currently logged in user if it already exists
        $userInfo = $this->_registry->registry('inchoo_socialconnect_userinfo');

        if (is_null($userInfo) || !$userInfo->hasData()) {
            // No user info, see if we have something set through layout
            if (!($text = $this->getData('button_text'))) {
                // "Connect" is fallback used when text isn't set through layout
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