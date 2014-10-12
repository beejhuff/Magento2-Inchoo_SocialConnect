<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 11.10.14.
 * Time: 14:20
 */

namespace Inchoo\SocialConnect\Block;


class Login  extends \Magento\Framework\View\Element\Template
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
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
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
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_registry->register('inchoo_socialconnect_button_text', __('Login'));
    }

    /**
     * @return bool
     */
    public function facebookEnabled()
    {
        return $this->_clientFacebook->isEnabled();
    }

} 