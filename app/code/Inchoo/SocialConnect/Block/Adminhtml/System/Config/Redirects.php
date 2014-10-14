<?php

namespace Inchoo\SocialConnect\Block\Adminhtml\System\Config;

class Redirects extends \Magento\Backend\Block\System\Config\Form\Field
{

    /**
     * @return string
     */
    protected function getAuthProvider()
    {
        return '';
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        return sprintf(
            '<pre>%ssocialconnect/%s/connect/</pre>',
            $this->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB),
            $this->getAuthProvider()
        );
    }

}