<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * NotifyMyAndroid class definition
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

use randomhost\thirdparty\NmaApi;
use randomhost\Icinga\Notification as Notification;

/**
 * Sends Icinga Android push notifications via Notify My Android.
 *
 * @category  Monitoring
 * @package   PHP_Icinga
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2014 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   Release: @package_version@
 * @link      https://pear.random-host.com/
 */
class NotifyMyAndroid extends Base implements Notification
{
    /**
     * Debug mode
     * 
     * @const bool
     */
    const DEBUG = false;

    /**
     * Notification sender
     * 
     * @const bool
     */
    const SENDER = 'Icinga';

    /**
     * Instance of NmaApi class
     *
     * @var NmaApi
     */
    protected $nmaAPI = null;

    /**
     * Maps host and service states to priority values
     * 
     * @var array
     */
    protected $stateToPriorityMap
        = array(
            'UNKNOWN' => 0,
            'OK' => 0,
            'WARNING' => 1,
            'CRITICAL' => 2,
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
                'apikey:',
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
                'apikey',
            )
        );

        $this->setHelp(
            <<<EOT
Icinga plugin for sending Android push notifications via Notify My Android.

--type    Notification type
--service Service name
--host    Host name
--address Host address
--state   Service state
--time    Notification time
--output  Check plugin output
--apikey  NotifyMyAndroid API key
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

        // load NmaApi client
        $this->nmaAPI = new nmaApi(
            array(
                'apikey' => $options['apikey']
            )
        );
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

            $message = sprintf(
                'Service: %2$s' . PHP_EOL .
                'Host: %3$s' . PHP_EOL .
                'State: %5$s' . PHP_EOL .
                'Message: %7$s',
                $options['type'],
                $options['service'],
                $options['host'],
                $options['address'],
                $options['state'],
                $options['time'],
                $options['output']
            );

            $priority = $this->determinePriority($options['state']);

            // verify API key
            if ($this->nmaAPI->verify()) {
                
                // send notification
                $result = $this->nmaAPI->notify(
                    self::SENDER,
                    $options['type'],
                    $message,
                    $priority,
                    false,
                    array('url' => 'anag://open?updateonreceive=true')
                );
                
                if ($result) {
                    $this->setMessage('Message was sent');
                    $this->setCode(self::STATE_OK);
                } else {
                    $this->setMessage('Message could not be sent');
                    $this->setCode(self::STATE_WARNING);
                }
            }
        } catch (\Exception $e) {
            $this->setMessage('Error from NmaApi: ' . $e->getMessage());
            $this->setCode(self::STATE_CRITICAL);
        }
    }

    /**
     * Returns the priority for the given host or service state.
     * 
     * @param string $state Host or service state.
     *
     * @return int
     */
    protected function determinePriority($state)
    {
        $priority = 0;
        if (array_key_exists($state, $this->stateToPriorityMap)) {
            $priority = $this->stateToPriorityMap[$state];
        }
        return $priority;
    }
} 
