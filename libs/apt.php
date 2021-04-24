<?php
require '../autoload.php';
$Config = new Config();

/*
    Apt package status - This lib checks the status of the packages installed on the system and
    returns the number of packages that can be upgraded and how many are security updates.

    This lib is specific to Linux distributions that use the APT Package Management System such as
    Debian and Ubuntu.

    'apt-check' notes:
    * apt-check is the utility used by apt to determine if there are packages available.
    * If called with no parameters, it returns with a tuple of numbers in the format: <standard>;<security>
        - 'standard' is an int representing the upgrade packages available
        - 'security' is an int representing the security upgrade packages available
    * At least on Ubuntu 20.x, the path of the 'apt-check' utility is
        '/usr/lib/update-notifier/apt-check'. The utility and it's path will need to be validated
        on other Linux distributions and Ubuntu versions.

    Configuration / Usage
    * The property 'blahblah' must be in the 'esm.config.json' file with the property of 'true'
        for this library to execute correctly.
    * Status Values & Messages:
        - '0' - 'Success' will be set if everything was successful.
        - '1' - 'Disabled' will be set if this library is disabled.
        - '2' - 'Failure' will be set if apt-check failed to execute. Can have additional
            information appended to the message.
        - '3' - 'Not Available' will be set if apt-check could not be located.
        - '-1' - 'Unknown' or other messages will be set for any other reason.

*/
$configKey = 'package_management:apt';

$command_path = '/usr/lib/update-notifier/apt-check';

// apt-check outputs to stderr, 2>&1 concat's stderr to stdout
$options = '2>&1';

$datas = array();

if (count($Config->get($configKey)) != 1) {
    $datas['status'] = -1;
    $datas['message'] = 'Not Configured';
} elseif ($Config->get($configKey) == false ) {
    $datas['status'] = 1;
    $datas['message'] = 'Disabled';
} elseif ($Config->get($configKey) == true ) {
    // If the command is configured & enabled
    if( file_exists($command_path) ) {
        $command = $command_path . " " . $options;
        $execresult = exec($command, $output, $retval);
        if( $execresult ) {
            $items = explode(';', $output[0]);
            $datas['status'] = 0;
            $datas['message'] = 'Success';
            $datas['standard'] = $items[0];
            $datas['security'] = $items[1];
        } else {
            $datas['status'] = 2;
            $datas['message'] = 'Failure ' . $retval;
        }
    } else {
        $datas['status'] = 3;
        $datas['message'] = 'Not Available';
    }
} else {
    // Not sure what's going on here....
    $datas['status'] = -1;
    $datas['message'] = 'Unknown';
}

echo json_encode($datas);
