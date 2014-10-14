<?php

namespace Inchoo\SocialConnect\Model\Facebook;

class Info extends \Magento\Framework\Object
{
    /**
     *
     * @var array
     */
    protected $_params;

    /**
     * @var string
     */
    protected $_target;

    /**
     * Facebook client model
     *
     * @var \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client
     */
    protected $_client;

    /**
     *
     * @param \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client $client
     * @param array $params
     * @param string $target
     * @param array $data
     */
    public function __construct(
        \Inchoo\SocialConnect\Model\Facebook\Oauth2\Client $client,
        array $params = array(),
        $target = 'me',

        // Parent
        array $data = array())
    {
        $this->_client = $client;
        $this->_params = $params;
        $this->_target = $target;

        parent::__construct($data);
    }

    /**
     *
     * @param \StdClass $token Access token
     */
    public function setAccessToken(\StdClass $token)
    {
        $this->_client->setAccessToken($token);
    }

    /**
     * Get Facebook client's access token
     *
     * @return \stdClass
     */
    public function getAccessToken()
    {
        return $this->_client->getAccessToken();
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->_target = $target;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->_target;
    }

    /**
     * @param \StdClass $accessToken
     * @return $this
     */
    public function loadByAccessToken(\StdClass $accessToken)
    {
        $this->setAccessToken($accessToken);

        $this->_load();

        return $this;
    }

    /**
     * @throws \Inchoo\SocialConnect\Model\Facebook\Oauth2\Exception
     * @throws \Exception
     */
    protected function _load()
    {

        try{
            $response = $this->_client->api(
                '/'.$this->_target,
                'GET',
                $this->_params
            );

            foreach ($response as $key => $value) {
                $this->setData($key, $value);
            }

        } catch(\Inchoo\SocialConnect\Model\Facebook\Oauth2\Exception $e) {
            $this->_onException($e);
        } catch(Exception $e) {
            $this->_onException($e);
        }
    }

    /**
     *
     * @param \Exception $e
     * @throws \Exception
     */
    protected function _onException(\Exception $e)
    {

        throw $e;
    }

}