<?php

namespace Inchoo\SocialConnect\Block\Adminhtml\Facebook\System\Config;

class Links extends \Inchoo\SocialConnect\Block\Adminhtml\System\Config\Links
{

    /**
     * @return string
     */
    protected function getAuthProviderLink()
    {
        return 'Facebook Developers';
    }

    /**
     * @return string
     */
    protected function getAuthProviderLinkHref()
    {
        return 'https://developers.facebook.com/';
    }

}