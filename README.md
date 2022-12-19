[![Build Status][1]][2]

# randomhost/icinga

<!-- TOC -->
* [1. Purpose](#1-purpose)
* [2. Usage](#2-usage)
  * [2.1. Base Class](#21-base-class)
  * [2.2. Check Plugins](#22-check-plugins)
    * [2.2.1. Check\Base Class](#221-checkbase-class)
    * [2.2.2. Implementing Check Classes](#222-implementing-check-classes)
  * [2.3. Notification Plugins](#23-notification-plugins)
    * [2.3.1. Notification\Base Class](#231-notificationbase-class)
    * [2.3.2. Implementing Notification Classes](#232-implementing-notification-classes)
* [3. License](#3-license)
<!-- TOC -->

## 1. Purpose

This package provides check and notification commands for the Icinga monitoring
system.

## 2. Usage

### 2.1. Base Class

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

### 2.2. Check Plugins

A basic approach at using a check plugin built with this package could look like
this:

```php
<?php
namespace randomhost\Icinga\Check;

require_once '/path/to/vendor/autoload.php';

$check = new ExampleService();
$check->setOptions(
    getopt(
        $check->getShortOptions(),
        $check->getLongOptions()
    )
);
$check->run();

echo $check->getMessage();
exit($check->getCode());
```

This will instantiate the check class for the example service and run the checks
defined for that service. What is being checked depends on the individual check
implementation.

#### 2.2.1. Check\Base Class

The abstract `Check\Base` class provides common methods for extending child
classes. It implements one public method in addition to the ones provided by
the common `Base` class:

- `Check\Base::run()`  
    Takes care of validating command line parameters, displaying help output and
    executing the main check plugin logic.

All check classes should extend this class.

#### 2.2.2. Implementing Check Classes

To create a check class, simply extend the `Check\Base` class and implement a
protected method `check()`.

```php
<?php
namespace randomhost\Icinga\Check;

use randomhost\Icinga\Plugin;

class ExampleService extends Base implements Check
{
    protected function check(): Plugin
    {
        // main check logic goes here
        
        $this->setMessage('Everything is fine');
        $this->setCode(self::STATE_OK);
        
        return $this;
    }
}
```

If your check requires command line parameters, you can define those in the
constructor of your check class. This is also the right place to place the
help output which is shown if a required parameter is missing.

```php
<?php
namespace randomhost\Icinga\Check;

use randomhost\Icinga\Plugin;

class ExampleService extends Base implements Check
{
    public function __construct()
    {
        $this->setLongOptions(
            [
                'host:',
                'port:',
                'user:',
                'password:',
                'warningThreshold:',
                'criticalThreshold:'
            ]
        );
    
        $this->setRequiredOptions(
            [
                'host',
                'port',
                'user',
                'password',
                'warningThreshold',
                'criticalThreshold'
            ]
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
    
    protected function check(): Plugin
    {
        $options = $this->getOptions();
        
        // main check logic goes here
        
        return $this;
    }
}
```

### 2.3. Notification Plugins

A basic approach at using a notification plugin built with this package could
look like this:

```php
<?php
namespace randomhost\Icinga\Notification;

require_once '/path/to/vendor/autoload.php';

$notification = new ExampleNotification();
$notification->setOptions(
    getopt(
        $notification->getShortOptions(),
        $notification->getLongOptions()
    )
);
$notification->run();

echo $notification->getMessage();
exit($notification->getCode());
```

This will instantiate the notification class for the example notification plugin
and run the logic defined for that plugin. What type of notification is being
sent depends on the individual notification class implementation.

#### 2.3.1. Notification\Base Class

The abstract `Notification\Base` class provides common methods for extending
child classes. It implements one public method in addition to the ones provided
by the common `Base` class:

- `Notification\Base::run()`  
    Takes care of validating command line parameters, displaying help output and
    executing the main notification plugin logic.

All notification classes should extend this class.

#### 2.3.2. Implementing Notification Classes

To create a notification class, simply extend the `Notification\Base` class and
implement a protected method `send()`.

```php
<?php
namespace randomhost\Icinga\Notification;

use randomhost\Icinga\Plugin;

class ExampleNotification extends Base implements Notification
{
    protected function send(): Plugin
    {
        // main notification logic goes here
        
        $this->setMessage('Notification sent');
        $this->setCode(self::STATE_OK);
        
        return $this;
    }
}
```

If your notification class requires command line parameters, you can define
those in the constructor of your notification class. This is also the right
place to place the help output which is shown if a required parameter is missing.

```php
<?php
namespace randomhost\Icinga\Notification;

use randomhost\Icinga\Plugin;

class ExampleNotification extends Base implements Notification
{
    public function __construct()
    {
        $this->setLongOptions(
            [
                'type:',
                'service:',
                'host:',
                'address:',
                'state:',
                'time:',
                'output:',
                'phonenumber:',
            ]
        );
        
        $this->setRequiredOptions(
            [
                'type',
                'service',
                'host',
                'address',
                'state',
                'time',
                'output',
                'phonenumber',
            ]
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
    
    protected function send(): Plugin
    {
        $options = $this->getOptions();
        
        // main notification logic goes here
        
        return $this;
    }
}
```

## 3. License

See LICENSE.txt for full license details.


[1]: https://github.com/randomhost/icinga/actions/workflows/php.yml/badge.svg
[2]: https://github.com/randomhost/icinga/actions/workflows/php.yml
