<?php

namespace Inchoo\SocialConnect\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;


    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\State $appState)
    {
        $this->_appState = $appState;

        parent::__construct($context);
    }

    public function log($message, $level = \Zend_Log::DEBUG, $loggerKey = \Magento\Framework\Logger::LOGGER_SYSTEM)
    {
        if($this->_appState->getMode() == \Magento\Framework\App\State::MODE_DEVELOPER) {
            $this->_logger->log($message, $level, $loggerKey);
        }
    }

}
