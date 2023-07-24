<?php
require '../autoload.php';
$Config = new Config();

/*
    Apt package status - This lib checks the status of the packages installed on the system and
    returns the number of packages that can be upgraded and how many are security updates.

    This lib is specific to Linux distributions that use the APT Package Management System such as
    Debian and Ubuntu.

    'apt-check' notes:
    * apt-check is the utility used by apt to determine if there are packages available in Ubuntu
        distributions. 
    * If called with no parameters, it returns with a tuple of numbers in the format: <standard>;<security>
        - 'standard' is an int representing the upgrade packages available
        - 'security' is an int representing the security upgrade packages available
    * At least on Ubuntu 20.x, the path of the 'apt-check' utility is
        '/usr/lib/update-notifier/apt-check'. The utility and it's path will need to be validated
        on other Ubuntu versions.
    * The results of apt-check are cached on the OS side of things and thus, if we can, we should 
        prefer the use of this command.
    
    'apt-get' notes:
    * The command `apt-get update` **must** be run before this will report the correct number of 
        packages. As this will need to be run with super user privileges, it is recommended that a
        simple cron job or timer script be configured for this job.
    * It is possible to enable it directly by putting 'apt_update_before_check' parameter to true.
        Note that you'll then need to grant sudo apt-get rights to www-data:
        `sudo visudo`
        `www-data ALL=(ALL) NOPASSWD: /bin/apt-get`
        <!> ACTIVATING IT MAY HAVE IMPACT ON SECURITY OF YOUR COMPUTER SO USE IT WITH CAUTION
        <!> THIS APPROACH WILL ALSO SLOW DOWN PAGE LOAD
    * The `apt-get` approach is used if the `apt-check` command cannot be found. Most likely, it 
        means that this script is not running in an Ubuntu environment.
    * Basically, this calls and filters 'apt-get --simulate dist-upgrade'. If this call is 
        successful, the results are filtered with php commands to get the number of standard and 
        security updates.
    * This call is not cached and can take a bit of time to complete.
    * In the grand scheme of things, this is basically running these two CLI commands:
        - 'apt-get --simulate dist-upgrade |grep "^Inst" |grep --ignore-case securi |wc --lines'
        - 'apt-get --simulate dist-upgrade |grep "^Inst" |wc --lines'

    Configuration / Usage
    * The property 'package_management:apt' must be in the 'esm.config.json' file with the property
        of 'true' for this library to execute correctly. See the example 'esm.config.json' for the
        syntax.
    * Status Values & Messages:
        - `0` - 'Success' will be set if everything was successful.
        - `1` - 'Disabled' will be set if this library is disabled.
        - `2` - 'Failure' will be set if apt-check failed to execute. Can have additional
            information appended to the message.
        - `3` - 'Not Available' will be set if apt-check could not be located.
        - `-1` - 'Unknown' or other messages will be set for any other reason.
    * The JSON return structure schema:
        - (int) `status` - the status of the call, can be negative.
        - (string) `message` - the result of the call in freetext form.
        - (int) `standard` - the number of packages awaiting upgrade. Optional
        - (int) `security` - on success, the number of security packages awaiting upgrade. Optional
*/

$configKey = 'package_management:apt';
$optionalUpdateKey = 'package_management:apt_update_before_check';

// The command paths. Intentionally not configurable to prevent remote execution bugs.
$apt_get_root_path = '/bin/apt-get';
$apt_get_usr_path = '/usr/bin/apt-get';
$apt_get_path = '';
$apt_check_path = '/usr/lib/update-notifier/apt-check';

// Quickly find the apt-get path, just in case.
if(file_exists($apt_get_root_path)) {
    $apt_get_path = $apt_get_root_path;
} else if( file_exists($apt_get_usr_path)) {
    $apt_get_path = $apt_get_usr_path;
} 

$datas = array();

// TODO Determine how to test for the existence of a key before trying to access it. If you load a
// non-existent key with $Config->get($configKey), you'll get the entire configuration back. If you
// try to load a partially existing key (e.g. the 'package_management' header exists but not the
// 'apt' key) you'll get nothing back. Return a status of -1, message of 'not configured'.
if ($Config->get($configKey) == false ) {
    $datas['status'] = 1;
    $datas['message'] = 'Disabled';
} elseif ($Config->get($configKey) == true ) {
    // Check each command path for existance & if it's executable.
    if( file_exists($apt_check_path) && is_executable($apt_check_path) ) {
        $command_path = $apt_check_path;
        // apt-check outputs to stderr, 2>&1 concat's stderr to stdout
        $options = '2>&1';

        $command = $command_path . " " . $options;

        $execresult = exec($command, $output, $retval);

        if( $retval == 0 ) {
            $items = explode(';', $output[0]);
            $datas['status'] = 0;
            $datas['message'] = 'Success';
            $datas['standard'] = $items[0];
            $datas['security'] = $items[1];
        } else {
            $datas['status'] = 2;
            $datas['message'] = 'apt-check failure - error code ' . $retval;
        }
    } else if ( $apt_get_path != '' && file_exists($apt_get_path) && is_executable($apt_get_path) ) {
        // If requested in config, update apt (getting latest infos from apt server) before doing an apt.
        // WARNING (security potential issue): sudo apt-get will then need to be allowed for www-data user
        if ($Config->get($optionalUpdateKey) == true) {
            $updateCommand = 'sudo --non-interactive ' . $apt_get_path . ' --quiet --yes update';
            $execresult = exec($updateCommand, $output, $retval);
            if ( $retval != 0 ) {
                error_log("Failed to execute '$updateCommand' from php script");
            }
        }

        $command_path = $apt_get_path;

        $options = "--simulate dist-upgrade";
        $command = $command_path . " " . $options;

        $execresult = exec($command, $output, $retval);

        if( $retval == 0 ) {
            // Success - now filter the results
            $standard = preg_grep('/^Inst/', $output);
            $security = preg_grep('/securi/i', $standard);
    
            $datas['status'] = 0;
            $datas['message'] = 'Success';
            $datas['standard'] = sizeof($standard);
            $datas['security'] = sizeof($security);
        } else {
            $datas['status'] = 2;
            $datas['message'] = 'apt-get failure - error code ' . $retval;
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
