[eZ Server Monitor](http://www.ezservermonitor.com) (eSM) is a script for monitoring Linux servers. It is available in [Bash](http://www.ezservermonitor.com/esm-sh/features) version and [Web](http://www.ezservermonitor.com/esm-web/features) application.

# eZ Server Monitor `Web

In its [Web](http://www.ezservermonitor.com/esm-web/features) version eSM is a PHP script that displays on a Web page information such as:

![](http://www.ezservermonitor.com/uploads/esm_web/esm-web_dashboard-complete.png)

- **System** : hostname, OS, kernel version, uptime, last boot date, number of current user(s), server datetime
- **Load average** : gauges showing the CPU load with the percentage (1 minute, 5 minutes et 15 minutes)
- **Network usage** : displaying the IP address of each network interface with the data transmitted and received
- **CPU** : model, frequency, cores number, cache L2, bogomips, temperature
- **Disk usage** : table of each mount point with the space available, used and total
- **Memory** : table containing the available used and total of RAM
- **Swap** : table containing the available used and total of Swap
- **Last login** : display last 5 user connections
- **Ping** : ping the hosts defined in the configuration file
- **Services** : displays the status (up or down) services defined in the configuration file

Several themes are available !

![](http://www.ezservermonitor.com/uploads/esm_web/esm-web_themes.png)

Each block can be reloaded manually.

You can download the last version [here](http://www.ezservermonitor.com/esm-web/downloads). The [requirements](http://www.ezservermonitor.com/esm-web/documentation) are simple : a Linux environment, a web server (Apache2, Lighttpd, Nginx, ...) and PHP.

The [documentation](http://www.ezservermonitor.com/esm-web/documentation) explains all the parameters of *esm.config.json*.

Changelog is available [here](http://www.ezservermonitor.com/esm-web/changelog).

**View more information on the [official website](http://www.ezservermonitor.com/esm-web/features).**