<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Smstrade class definition
 *
 * PHP version 5
 *
 * @category  Monitoring
 * @package   PHP_Icinga
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2014 random-host.com
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      https://pear.random-host.com/
 */
namespace randomhost\Icinga\Notifications;

use randomhost\Icinga\Notification as Notification;

/**
 * Sends Icinga SMS notifications via Smstrade.de.
 *
 * @category  Monitoring
 * @package   PHP_Icinga
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2014 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   Release: @package_version@
 * @link      https://pear.random-host.com/
 */
class Smstrade extends Base implements Notification
{
    /**
     * Debug mode
     * 
     * @const bool
     */
    const DEBUG = false;

    /**
     * API WSDL
     * 
     * @const string
     */
    const API_WSDL = 'https://gateway.smstrade.de/soap/index.php?wsdl';
    
    /**
     * Notification sender
     * 
     * @const string 
     */
    const SENDER = 'Icinga';

    /**
     * Maximum message length
     * 
     * @const int
     */
    const MAX_MESSAGE_LENGTH = 260;
    
    /**
     * Instance of NmaApi class
     *
     * @var \SoapClient
     */
    protected $soapClient = null;

    /**
     * API response code mappings
     * 
     * @var array
     */
    protected $responseCodes
        = array(
            0 => 'no gateway connection',
            10 => 'recipient unknown',
            20 => 'sender ID too long',
            30 => 'message too long',
            31 => 'incorrect message type',
            40 => 'incorrect SMS type',
            50 => 'login error',
            60 => 'insufficient credit',
            70 => 'carrier not supported by route',
            71 => 'feature not supported by route',
            80 => 'failed to send SMS',
            90 => 'cannot send',
            100 => 'SMS sent successfully'
        );
    
    /**
     * Constructor for this class.
     */
    public function __construct()
    {
        $this->setLongOptions(
            array(
                'type:',
                'service:',
                'host:',
                'address:',
                'state:',
                'time:',
                'output:',
                'phone:',
                'apikey:',
                'route:',
            )
        );

        $this->setRequiredOptions(
            array(
                'type',
                'service',
                'host',
                'address',
                'state',
                'time',
                'output',
                'phone',
                'apikey',
                'route',
            )
        );

        $this->setHelp(
            <<<EOT
Icinga plugin for sending SMS notifications via Smstrade.de.

--type    Notification type
--service Service name
--host    Host name
--address Host address
--state   Service state
--time    Notification time
--output  Check plugin output
--phone   Phone number
--apikey  Smstrade.de API key
--route   Route type
EOT
        );
    }

    /**
     * Reads command line options and performs pre-run tasks.
     *
     * @return void
     */
    protected function preRun()
    {
        parent::preRun();

        $options = $this->getOptions();

        // send SMS only for PROBLEM reports
        if ('PROBLEM' !== $options['type']) {
            $this->setMessage(
                sprintf(
                    'Notification type mismatch "%s". Exiting.',
                    $options['type']
                )
            );
            $this->setCode(self::STATE_OK);
            
            parent::postRun();
        }

        // set SOAP options
        $soapOptions = array(
            'response' => null,
            'dlr' => null,
            'ref' => null,
            'concat' => null,
            'messagetype' => null,
            'udh' => null,
            'charset' => null,
            'senddate' => null,
            'debug' => (int)self::DEBUG
        );
        ini_set('soap.wsdl_cache', 0);

        // load SoapClient instance
        $this->soapClient = new \SoapClient(self::API_WSDL);
        foreach ($soapOptions as $key => $value) {
            if ($value != null) {
                $this->soapClient->setOptionalParam($key, $value);
            }
        }
    }

    /**
     * Sends the notification to the given Android device.
     *
     * @return void
     */
    protected function send()
    {
        try {
            $options = $this->getOptions();

            // build message
            $message = sprintf(
                '-%1$s- Service: %2$s, Host: %3$s, State: %5$s, Message: %7$s',
                $options['type'],
                $options['service'],
                $options['host'],
                $options['address'],
                $options['state'],
                $options['time'],
                $options['output']
            );

            // shorten message to maximum supported length for a single SMS
            $message = substr($message, 0, self::MAX_MESSAGE_LENGTH);

            // send SMS
            $response = $this->soapClient->sendSMS(
                $options['apikey'],
                $options['phone'],
                $message,
                $options['route'],
                self::SENDER
            );

            // evaluate return code
            $returnCode = $response[0];
            //$messageId = $response[1];
            //$cost = $response[2];

            if (100 == $returnCode) {
                $this->setMessage('Message was sent');
                $this->setCode(self::STATE_OK);
            } else {
                if (array_key_exists($returnCode, $this->responseCodes)) {
                    $this->setMessage($this->responseCodes[$returnCode]);
                    $this->setCode(self::STATE_WARNING);
                } else {
                    $this->setMessage('Message could not be sent');
                    $this->setCode(self::STATE_CRITICAL);
                }
            }
        } catch (\Exception $e) {
            $this->setMessage('Error from NmaApi: ' . $e->getMessage());
            $this->setCode(self::STATE_CRITICAL);
        }
    }
} 
