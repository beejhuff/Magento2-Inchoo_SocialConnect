<?php

namespace Inchoo\SocialConnect\Controller;

abstract class Facebook extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $coreHelperData;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerHelperData;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     *
     * @var \Inchoo\SocialConnect\Helper\Data
     */
    protected $_helperData;

    /**
     *
     * @var \Inchoo\SocialConnect\Helper\Facebook
     */
    protected $_helperFacebook;

    /**
     * @var \Inchoo\SocialConnect\Model\Facebook\InfoFactory
     */
    protected $_infoFactory;

    /**
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * TODO: Move arguments to respective action class where applicable
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Core\Helper\Data $coreHelperData
     * @param \Magento\Customer\Helper\Data $customerHelperData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Inchoo\SocialConnect\Helper\Data $helperData
     * @param \Inchoo\SocialConnect\Helper\Facebook $helperFacebook
     * @param \Inchoo\SocialConnect\Model\Facebook\InfoFactory $infoFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Core\Helper\Data $coreHelperData,
        \Magento\Customer\Helper\Data $customerHelperData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Inchoo\SocialConnect\Helper\Data $helperData,
        \Inchoo\SocialConnect\Helper\Facebook $helperFacebook,
        \Inchoo\SocialConnect\Model\Facebook\InfoFactory $infoFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,

        // Parent
        \Magento\Framework\App\Action\Context $context)
    {
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->coreHelperData = $coreHelperData;
        $this->_customerHelperData = $customerHelperData;
        $this->_scopeConfig = $scopeConfig;
        $this->_helperData = $helperData;
        $this->_helperFacebook = $helperFacebook;
        $this->_infoFactory = $infoFactory;
        $this->_customerFactory = $customerFactory;

        parent::__construct($context);
    }

    protected function _sendResponse()
    {
        $return = array(
            'redirect' => $this->_loginPostRedirect()
        );

        echo json_encode($return);
    }

   /**
     * Define and return target URL after logging in
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _loginPostRedirect()
    {
        $lastCustomerId = $this->_getSession()->getLastCustomerId();
        if (isset(
            $lastCustomerId
            ) && $this->_getSession()->isLoggedIn() && $lastCustomerId != $this->_getSession()->getId()
        ) {
            $this->_getSession()->unsBeforeAuthUrl()->setLastCustomerId($this->_getSession()->getId());
        }
        if (!$this->_getSession()->getBeforeAuthUrl() ||
            $this->_getSession()->getBeforeAuthUrl() == $this->_storeManager->getStore()->getBaseUrl()
        ) {
            // Set default URL to redirect customer to
            $this->_getSession()->setBeforeAuthUrl($this->_customerHelperData->getAccountUrl());
            // Redirect customer to the last page visited after logging in
            if ($this->_getSession()->isLoggedIn()) {
                if (!$this->_scopeConfig->isSetFlag(
                    \Magento\Customer\Helper\Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
                ) {
                    $referer = $this->getRequest()->getParam(\Magento\Customer\Helper\Data::REFERER_QUERY_PARAM_NAME);
                    if ($referer) {
                        $referer = $this->coreHelperData->urlDecode($referer);
                        if ($this->_url->isOwnOriginUrl()) {
                            $this->_getSession()->setBeforeAuthUrl($referer);
                        }
                    }
                } elseif ($this->_getSession()->getAfterAuthUrl()) {
                    $this->_getSession()->setBeforeAuthUrl($this->_getSession()->getAfterAuthUrl(true));
                }
            } else {
                $this->_getSession()->setBeforeAuthUrl($this->_customerHelperData->getLoginUrl());
            }
        } elseif ($this->_getSession()->getBeforeAuthUrl() == $this->_customerHelperData->getLogoutUrl()) {
            $this->_getSession()->setBeforeAuthUrl($this->_customerHelperData->getDashboardUrl());
        } else {
            if (!$this->_getSession()->getAfterAuthUrl()) {
                $this->_getSession()->setAfterAuthUrl($this->_getSession()->getBeforeAuthUrl());
            }
            if ($this->_getSession()->isLoggedIn()) {
                $this->_getSession()->setBeforeAuthUrl($this->_getSession()->getAfterAuthUrl(true));
            }
        }
        return $this->_getSession()->getBeforeAuthUrl(true);
    }

    /**
     * Retrieve customer session model object
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->_customerSession;
    }

}
