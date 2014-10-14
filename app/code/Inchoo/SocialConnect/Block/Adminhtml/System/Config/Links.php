<?php

namespace Inchoo\SocialConnect\Block\Adminhtml\System\Config;

class Links extends \Magento\Backend\Block\System\Config\Form\Field
{

    /**
     * @return string
     */
    protected function getAuthProviderLink()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getAuthProviderLinkHref()
    {
        return '';
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        return sprintf(
            '<a href="%s" target="_blank">%s</a>',
            $this->getAuthProviderLinkHref(),
            $this->getAuthProviderLink()
        );
    }

}