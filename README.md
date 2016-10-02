[![Build Status][0]][1]

randomhost/icinga
=================

This package provides check and notification commands for the Icinga monitoring
system.

Usage
-----

### The Base class

The abstract `Base` class implements the following public methods which are
available for both, check and notification classes.

- `Base::getShortOptions()`  
    Returns available short options. The return value is supposed to be passed
    to PHP's built-in `getopt()` function as the first parameter and is used for
    setting up the command line arguments accepted by the check class.

- `Base::getLongOptions()`  
    Returns available long options. The return value is supposed to be passed to
    PHP's built-in `getopt()` function as the second parameter and is used for
    setting up the command line arguments accepted by the check class.
    
    The `Base` class comes with the pre-defined long option `--help` which
    triggers the built-in help method `Base::displayHelp()`.

- `Base::getMessage()`  
    Returns the plugin output. The return value is supposed to be echoed to
    stdout and defines the status message which will be passed to Icinga.

- `Base::getCode()`  
    Returns the return code. The return value is supposed to be passed to PHP's
    built-in `exit()` function and defines the status code which will be passed
    to Icinga.

- `Base::setOptions($options)`  
    This method accepts parsed command line arguments as returned by PHP's
    built-in `getopt()` function.

Check and notification classes should **NOT** extend this class directly. They
should extend their corresponding base class `Check\Base` or `Notification\Base`
accordingly.

### Check plugins

A basic approach at using a check plugin built with this package could look like
this:

```php
<?php
namespace randomhost\Icinga\Check;

require_once '/path/to/vendor/autoload.php';

$check = new ExampleService();
$check->run();

echo $check->getMessage();
exit($check->getCode());
```

This will instantiate the check class for the example service and run the checks
defined for that service. What is being checked depends on the individual check
implementation.

#### The Check\Base class

The abstract `Check\Base` class provides common methods for extending child
classes. It implements one public method in addition to the ones provided by
the common `Base` class:

- `Check\Base::run()`  
    Takes care of validating command line parameters, displaying help output and
    executing the main check plugin logic.

All check classes should extend this class.

#### Implementing check classes

To create a check class, simply extend the `Check\Base` class and implement a
protected method `check()`.

```php
<?php
namespace randomhost\Icinga\Check;

class ExampleService extends Base implements Check
{
    protected function check()
    {
        // main check logic goes here
        
        $this->setMessage('Everything is fine');
        $this->setCode(self::STATE_OK);
    }
}
```

If your check requires command line parameters, you can define those in the
constructor of your check class. This is also the right place to place the
help output which is shown if a required parameter is missing.

```php
<?php
namespace randomhost\Icinga\Check;

class ExampleService extends Base implements Check
{
    public function __construct()
    {
        $this->setLongOptions(
            array(
                'host:',
                'port:',
                'user:',
                'password:',
                'warningThreshold:',
                'criticalThreshold:'
            )
        );
    
        $this->setRequiredOptions(
            array(
                'host',
                'port',
                'user',
                'password',
                'warningThreshold',
                'criticalThreshold'
            )
        );
        
        $this->setHelp('
Icinga plugin for checking the example service.

--host              Example service IP address or hostname
--port              Example service port
--user              Example service user
--password          Example service password
--warningThreshold  Threshold to trigger the WARNING state
--criticalThreshold Threshold to trigger the CRITICAL state
        ');
    }
    
    protected function check()
    {
        $options = $this->getOptions();
        
        // main check logic goes here
    }
}
```

### Notification plugins

A basic approach at using a notification plugin built with this package could
look like this:

```php
<?php
namespace randomhost\Icinga\Notification;

require_once '/path/to/vendor/autoload.php';

$notification = new ExampleNotification();
$notification->run();

echo $notification->getMessage();
exit($notification->getCode());
```

This will instantiate the notification class for the example notification plugin
and run the logic defined for that plugin. What type of notification is being
sent depends on the individual notification class implementation.

#### The Notification\Base class

The abstract `Notification\Base` class provides common methods for extending
child classes. It implements one public method in addition to the ones provided
by the common `Base` class:

- `Notification\Base::run()`  
    Takes care of validating command line parameters, displaying help output and
    executing the main notification plugin logic.

All notification classes should extend this class.

#### Implementing notification classes

To create a notification class, simply extend the `Notification\Base` class and
implement a protected method `send()`.

```php
<?php
namespace randomhost\Icinga\Notification;

class ExampleNotification extends Base implements Notification
{
    protected function send()
    {
        // main notification logic goes here
        
        $this->setMessage('Notification sent');
        $this->setCode(self::STATE_OK);
    }
}
```

If your notification class requires command line parameters, you can define
those in the constructor of your notification class. This is also the right
place to place the help output which is shown if a required parameter is missing.

```php
<?php
namespace randomhost\Icinga\Notification;

class ExampleNotification extends Base implements Notification
{
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
                'phonenumber:',
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
                'phonenumber',
            )
        );
        
        $this->setHelp('
Icinga plugin for sending notifications via the example notification provider.

--type         Notification type
--service      Service name
--host         Host name
--address      Host address
--state        Service state
--time         Notification time
--output       Check plugin output
--phonenumber  User phone number
        ');
    }
    
    protected function send()
    {
        $options = $this->getOptions();
        
        // main notification logic goes here
    }
}
```


License
-------

See LICENSE.txt for full license details.


[0]: https://travis-ci.org/randomhost/icinga.svg?branch=master
[1]: https://travis-ci.org/randomhost/icinga
