<?php

namespace Inchoo\SocialConnect\Block\Adminhtml\Facebook\System\Config;

class Redirects extends \Inchoo\SocialConnect\Block\Adminhtml\System\Config\Redirects
{

    /**
     * @return string
     */
    protected function getAuthProvider()
    {
        return 'facebook';
    }

}