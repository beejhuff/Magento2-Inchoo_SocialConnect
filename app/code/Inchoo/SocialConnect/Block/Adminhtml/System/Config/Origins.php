<?php

namespace Inchoo\SocialConnect\Block\Adminhtml\System\Config;

class Origins extends \Magento\Backend\Block\System\Config\Form\Field
{

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        return sprintf('<pre>%s</pre>', $this->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB));
    }


}