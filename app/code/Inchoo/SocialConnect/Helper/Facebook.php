<?php

namespace Inchoo\SocialConnect\Helper;

class Facebook extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder
     */
    protected $_customerDetailsBuilder;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerBuilder
     */
    protected $_customerBuilder;

    /**
     * @var \Magento\Customer\Model\Converter
     */
    protected $_converter;

    /**
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Framework\Image\Factory
     */
    protected $_imageFactory;

    /**
     *
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $_httpClientFactory;

    /**
     * Facebook client model
     *
     * @var \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client
     */
    protected $_client;

    /**
     *
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     * @param \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder
     * @param \Magento\Customer\Model\Converter $converter
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client $client
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService,
        \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder,
        \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder,
        \Magento\Customer\Model\Converter $converter,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client $client,

        // Parent
        \Magento\Framework\App\Helper\Context $context)
    {
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_customerAccountService = $customerAccountService;
        $this->_customerDetailsBuilder = $customerDetailsBuilder;
        $this->_customerBuilder = $customerBuilder;
        $this->_converter = $converter;
        $this->_customerFactory = $customerFactory;
        $this->_imageFactory = $imageFactory;
        $this->_httpClientFactory = $httpClientFactory;
        $this->_client = $client;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     */
    public function disconnect(\Magento\Customer\Model\Customer $customer)
    {
        // TODO: Move to \Inchoo\SocialConnect\Model\Facebook\Info\User
        try {
            $this->_client->setAccessToken(unserialize($customer->getInchooSocialconnectFtoken()));
            $this->_client->api('/me/permissions', 'DELETE');
        } catch (Exception $e) {}

        $pictureFilename = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            )
            .'/inchoo/socialconnect/facebook/'
            .$customer->getInchooSocialconnectFid();

        if(file_exists($pictureFilename)) {
            @unlink($pictureFilename);
        }

        $customer->setInchooSocialconnectFid(null)
            ->setInchooSocialconnectFtoken(null)
            ->save();
    }

    public function connectByFacebookId(
        $facebookId,
        \stdClass $token,
        $customerId)
    {
        $customerDetailsObject = $this->_customerAccountService->getCustomerDetails($customerId);
        /* @var $customerDetailsObject \Magento\Customer\Service\V1\Data\CustomerDetails */

        $customerDataObject = $customerDetailsObject->getCustomer();
        /* @var $customerDetailsObject \Magento\Customer\Service\V1\Data\Customer */

        // Merge old and new data
        $customerDetailsArray = array_merge(
            $customerDataObject->__toArray(),
            array('custom_attributes' =>
                array(
                    array(
                        \Magento\Framework\Service\Data\AttributeValue::ATTRIBUTE_CODE => 'inchoo_socialconnect_fid',
                        \Magento\Framework\Service\Data\AttributeValue::VALUE => $facebookId
                    ),
                    array(
                        \Magento\Framework\Service\Data\AttributeValue::ATTRIBUTE_CODE => 'inchoo_socialconnect_ftoken',
                        \Magento\Framework\Service\Data\AttributeValue::VALUE => serialize($token)
                    )
                )
            )
        );

        // Pass result to customerBuilder
        $this->_customerBuilder->populateWithArray($customerDetailsArray);

        // Pass result to customerDetailsBuilder
        $this->_customerDetailsBuilder->setCustomer($this->_customerBuilder->create());

        // Update customer
        $this->_customerAccountService->updateCustomer($customerId, $this->_customerDetailsBuilder->create());

        // Set customer as logged in
        $this->_customerSession->setCustomerDataAsLoggedIn($customerDataObject);
    }

    public function connectByCreatingAccount(
        $facebookId,
        $token,
        $email,
        $firstName,
        $lastName)
    {
        $customerDetails = array(
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => $email,
            'sendemail' => 0,
            'confirmation' => 0,
            'custom_attributes' => array(
                array(
                    \Magento\Framework\Service\Data\AttributeValue::ATTRIBUTE_CODE => 'inchoo_socialconnect_fid',
                    \Magento\Framework\Service\Data\AttributeValue::VALUE => $facebookId
                ),
                array(
                    \Magento\Framework\Service\Data\AttributeValue::ATTRIBUTE_CODE => 'inchoo_socialconnect_ftoken',
                    \Magento\Framework\Service\Data\AttributeValue::VALUE => serialize($token)
                )
            )
        );

        $customer = $this->_customerBuilder->populateWithArray($customerDetails)
            ->create();

        // Save customer
        $customerDetails = $this->_customerDetailsBuilder->setCustomer($customer)
            ->setAddresses(null)
            ->create();

        $customerDataObject = $this->_customerAccountService->createCustomer($customerDetails);
        /* @var $customer \Magento\Customer\Service\V1\Data\Customer */

        // Convert data object to customer model
        $customer = $this->_converter->createCustomerModel($customerDataObject);
        /* @var $customer \Magento\Customer\Model\Customer */

        $customer->sendNewAccountEmail('confirmed', '');

        $this->_customerSession->setCustomerAsLoggedIn($customer);
    }

    public function loginByCustomer(\Magento\Customer\Model\Customer $customer)
    {
        if($customer->getConfirmation()) {
            $customer->setConfirmation(null);
            $customer->save();
        }

        $this->_customerSession->setCustomerAsLoggedIn($customer);
    }

    public function getCustomersByFacebookId($facebookId)
    {
        $customer = $this->_customerFactory->create();

        $collection = $customer->getResourceCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('inchoo_socialconnect_fid', $facebookId)
            ->setPage(1, 1);

        return $collection;
    }

    public function getCustomersByEmail($email)
    {
        $customer = $this->_customerFactory->create();

        $collection = $customer->getResourceCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('email', $email)
            ->setPage(1, 1);

        return $collection;
    }

    public function getProperDimensionsPictureUrl($facebookId, $pictureUrl)
    {
        $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ).'/inchoo/socialconnect/facebook/'.$facebookId;

        $filename = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ).$facebookId;

        $directory = dirname($filename);

        if (!file_exists($directory) || !is_dir($directory)) {
            if (!@mkdir($directory, 0777, true))
                return null;
        }

        if(!file_exists($filename) ||
            (file_exists($filename) && (time() - filemtime($filename) >= 3600))) {
            $client = $this->_httpClientFactory->create($pictureUrl);
            $client->setStream();
            $response = $client->request('GET');
            stream_copy_to_stream($response->getStream(), fopen($filename, 'w'));

            $imageObj = $this->_imageFactory->create($filename);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            $imageObj->resize(150, 150);
            $imageObj->save($filename);
        }

        return $url;
    }
}
