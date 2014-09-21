[![Build Status](https://travis-ci.org/Random-Host/PHP_Icinga.svg)](https://travis-ci.org/Random-Host/PHP_Icinga)

PHP_Icinga
==========

This package provides check and notification commands for the Icinga monitoring
system.

Usage
-----

A basic approach at using this package could look like this:

```php
<?php
namespace randomhost\Icinga\Checks;

require 'psr0.autoloader.php';

$check = new ExampleService();
$check->run();
```

This will instantiate the check class for the example service and run the checks
defined for that service. What is being checked depends on the individual check
implementation.

### The Icinga/Checks/Base class

The abstract `Icinga/Checks/Base` class provides common methods for extending
child classes. It implements the Icinga/Check::run() method which by default is
the only public accessible method of a check class.

It takes care of parsing command line parameters, displaying status messages
and exiting with a proper exit code which Icinga understands.

All check classes should extend this class.

### Implementing check classes

To create a check class, simply extend the `Icinga/Checks/Base` class and
implement a protected method `check()`.

```php
<?php
namespace randomhost\Icinga\Checks;

use randomhost\Icinga\Check as Check;

class ExampleService extends Base
{
  protected function check() {
    
    // main check logic goes here
    
    $this->setMessage('Everything is fine');
    $this->setCode(Check::SERVICE_STATE_OK);
  }
}
```

If your check requires command line parameters, you can define those in the
constructor of your check class. This is also the right place to place the
help output which is shown if a required parameter is missing.

```php
<?php
namespace randomhost\Icinga\Checks;

use randomhost\Icinga\Check as Check;

class ExampleService extends Base
{
  public function __construct() {
    $this->setLongOptions( array(
        'host:',
        'port:',
        'user:',
        'password:',
        'warningThreshold:',
        'criticalThreshold:'
    ) );

    $this->setRequiredOptions( array(
        'host',
        'port',
        'user',
        'password',
        'warningThreshold',
        'criticalThreshold'
    ) );

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
  
  protected function check() {
    $options = $this->getOptions();
    
    // main check logic goes here
  }
}
```

System-Wide Installation
------------------------

PHP_Icinga should be installed using the [PEAR Installer](http://pear.php.net).
This installer is the PHP community's de-facto standard for installing PHP
components.

    sudo pear channel-discover pear.random-host.com
    sudo pear install --alldeps randomhost/PHP_Icinga

As A Dependency On Your Component
---------------------------------

If you are creating a component that relies on PHP_Icinga, please make sure that
you add PHP_Icinga to your component's package.xml file:

```xml
<dependencies>
  <required>
    <package>
      <name>PHP_Icinga</name>
      <channel>pear.random-host.com</channel>
      <min>1.0.0</min>
      <max>1.999.9999</max>
    </package>
  </required>
</dependencies>
```

Development Environment
-----------------------

If you want to patch or enhance this component, you will need to create a
suitable development environment. The easiest way to do that is to install
phix4componentdev:

    # phix4componentdev
    sudo apt-get install php5-xdebug
    sudo apt-get install php5-imagick
    sudo pear channel-discover pear.phix-project.org
    sudo pear -D auto_discover=1 install -Ba phix/phix4componentdev

You can then clone the git repository:

    # PHP_Icinga
    git clone https://github.com/Random-Host/PHP_Icinga.git

Then, install a local copy of this component's dependencies to complete the
development environment:

    # build vendor/ folder
    phing build-vendor

To make life easier for you, common tasks (such as running unit tests,
generating code review analytics, and creating the PEAR package) have been
automated using [phing](http://phing.info).  You'll find the automated steps
inside the build.xml file that ships with the component.

Run the command 'phing' in the component's top-level folder to see the full list
of available automated tasks.

License
-------

See LICENSE.txt for full license details.
