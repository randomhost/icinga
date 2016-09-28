<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * NmaApi class definition
 *
 * PHP version 5
 *
 * @category Monitoring
 * @package  PHP_Icinga
 * @author   Ken Pepple <ken.pepple@rabbityard.com>
 * @license  http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link     https://github.com/snider/php-notifyMyAndroid
 */
namespace randomhost\thirdparty;

/**
 * PHP library for NotifyMyAndroid.com which does not require cURL.
 *
 * @category Monitoring
 * @package  PHP_Icinga
 * @author   Ken Pepple <ken.pepple@rabbityard.com>
 * @license  http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version  Release: @package_version@
 * @link     https://github.com/snider/php-notifyMyAndroid
 */
class NmaApi
{
    /**
     * @const LIB_ERROR_TYPE can be exception or error
     */
    const LIB_ERROR_TYPE = 'exception';

    /**
     * @const holds the api key verify url
     */
    const LIB_NMA_VERIFY = 'https://www.notifymyandroid.com/publicapi/verify';

    /**
     * @const holds the notify url
     */
    const LIB_NMA_NOTIFY = 'https://www.notifymyandroid.com/publicapi/notify';

    /**
     * toggles on debugging
     *
     * @var bool
     */
    public $debug = false;

    public $apiCallsRemaining = false;

    public $apiLimitReset = false;

    public $lastStatus = false;

    /**
     * @var bool|string
     */
    protected $apiKey = false;

    /**
     * @var bool|string
     */
    protected $devKey = false;


    protected $error_codes = array(
        200 => 'Notification submitted.',
        400 => 'The data supplied is in the wrong format, invalid length or null.',
        401 => 'None of the API keys provided were valid.',
        402 => 'Maximum number of API calls per hour exceeded.',
        500 => 'Internal server error.'
    );

    /**
     * Constructor for this class.
     *
     * @param array $options Options for the API call
     */
    function __construct($options = array())
    {
        if (!isset($options['apikey'])) {
            return $this->_error('You must supply an API Key');
        } else {
            $this->apiKey = $options['apikey'];
        }

        if (isset($options['developerkey'])) {
            $this->devKey = $options['developerkey'];
        }

        if (isset($options['debug'])) {
            $this->debug = true;
        }

        return true;
    }


    /**
     * Verifies the API key.
     * 
     * @param bool $key [optional] if not set the one used on construct is used
     *
     * @return bool|mixed|\SimpleXMLElement|string
     */
    public function verify($key = false)
    {
        $options = array();

        if ($key !== false) {
            $options['apikey'] = $key;
        } else {
            $options['apikey'] = $this->apiKey;
        }

        if ($this->devKey) {
            $options['developerkey'] = $this->devKey;
        }

        // check multiple api-keys
        if (strpos($options['apikey'], ",")) {
            $keys = explode(',', $options['apikey']);
            foreach ($keys as $api) {
                $options['apikey'] = $api;
                if (!$this->makeApiCall(self::LIB_NMA_VERIFY, $options)) {
                    return $this->makeApiCall(self::LIB_NMA_VERIFY, $options);
                }
            }
            return true;
        } else {
            return $this->makeApiCall(self::LIB_NMA_VERIFY, $options);
        }
    }

    /**
     * Sends a notification with the given parameters.
     * 
     * @param string      $application Application name
     * @param string      $event       Event name
     * @param string      $description Event descriptions
     * @param int         $priority    Notification priority
     * @param string|bool $apiKeys     Comma separated list of API keys
     * @param array       $options     API options
     *
     * @return bool|mixed|\SimpleXMLElement|string
     */
    public function notify(
        $application = '', $event = '', $description = '', $priority = 0,
        $apiKeys = false, $options = array()
    ) {
        if (empty($application) || empty($event) || empty($description)) {
            return $this->_error(
                'you must supply a application name, event and long desc'
            );
        }

        // place here so other parameter settings can override this
        $post = array();

        // notify options present? This can be: url or content-type for now.
        if (count($options) > 0) {
            foreach ($options as $k => $v) {
                $post[$k] = $v;
            }
        }

        $post['application'] = substr($application, 0, 256);
        $post['event'] = substr($event, 0, 1000);
        $post['description'] = substr($description, 0, 10000);
        $post['priority'] = $priority;

        if ($this->devKey) {
            $post['developerkey'] = $this->devKey;
        }

        if ($apiKeys !== false) {
            $post['apikey'] = $apiKeys;
        } else {
            $post['apikey'] = $this->apiKey;
        }

        return $this->makeApiCall(self::LIB_NMA_NOTIFY, $post, 'POST');
    }


    /**
     * Calls the API with the given parameters
     * 
     * @param string     $url           URL for the API key.
     * @param array|null $params        API parameters
     * @param string     $requestMethod HTTP Request Method
     * @param string     $format        API response format (only XML for now)
     *
     * @return bool|mixed|\SimpleXMLElement|string
     * @throws \Exception
     */
    protected function makeApiCall(
        $url, $params = null, $requestMethod = 'GET', $format = 'xml'
    ) {
        $cparams = array(
            'http' => array(
                'method' => $requestMethod,
                'ignore_errors' => true
            )
        );
        if ($params !== null && !empty($params)) {
            $params = http_build_query($params, '', '&');
            if ($requestMethod == 'POST') {
                $cparams["http"]['header']
                    = 'Content-Type: application/x-www-form-urlencoded';
                $cparams['http']['content'] = $params;
            } else {
                $url .= '?' . $params;
            }
        } else {
            return $this->_error(
                'this api requires all calls to have params'
                . $this->debug ? ', you provided: ' . var_dump($params) : ''
            );
        }

        $context = stream_context_create($cparams);
        $fp = fopen($url, 'rb', false, $context);
        if (!$fp) {
            $res = false;
        } else {

            if ($this->debug) {
                $meta = stream_get_meta_data($fp);
                $this->_error(
                    'var dump of http headers' . var_dump($meta['wrapper_data'])
                );
            }

            $res = stream_get_contents($fp);
        }

        if ($res === false) {
            return $this->_error("$requestMethod $url failed: $php_errormsg");
        }

        switch ($format) {
        case 'json':
            return $this->_error('this api does not support json');
            /*
            * uncomment the below if json is added later
            * $r = json_decode($res);
           if ($r === null) {
               return $this->error("failed to decode $res as json");
           }
           return $r;*/

        case 'xml':
            $r = simplexml_load_string($res);
            if ($r === null) {
                return $this->_error("failed to decode $res as xml");
            }
            return $this->_processXmlReturn($r);
        }
        return $res;
    }

    /**
     * Triggers a PHP error or throws an Exception.
     * 
     * @param string $message Error message
     * @param int    $type    Error type
     *
     * @return bool
     * @throws \Exception
     */
    private function _error($message, $type = E_USER_NOTICE)
    {
        if (self::LIB_ERROR_TYPE == 'error') {
            trigger_error($message, $type);
            return false;
        } else {
            throw new \Exception($message, $type);
        }
    }

    /**
     * Processes the XML API reponse.
     * 
     * @param \SimpleXMLElement $obj \SimpleXMLElement instance
     *
     * @return bool
     */
    private function _processXmlReturn(\SimpleXMLElement $obj)
    {

        if (isset($obj->success)) {
            $this->lastStatus = $obj->success["@attributes"]['code'];

            $this->apiCallsRemaining
                = $obj->success["@attributes"]['remaining'];
            $this->apiLimitReset = $obj->success["@attributes"]['resettimer'];
            return true;
        } elseif (isset($obj->error)) {
            if (isset($obj->error["@attributes"])) {
                $this->lastStatus = $obj->error["@attributes"]['code'];

                if (isset($obj->error["@attributes"]['resettimer'])) {
                    $this->apiLimitReset
                        = $obj->error["@attributes"]['resettimer'];
                }

            }
            return $this->_error($obj->error);
        } else {
            return $this->_error("unkown error");
        }
    }
} 
