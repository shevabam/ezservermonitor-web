<?php
require __DIR__.'/../autoload.php';
$config = Config::instance();

$max = $config->get('last_sftp_login:max');

$datas = array();

# 'Aug 25 10:37:48 myserver sshd[14951]: pam_unix(sshd:session): session opened for user user1 by (uid=0)'
# [a-z]\+ \d\+ [0-9:]\+ [a-z_-]\+ sshd\[\d\+]: pam_unix(sshd:session): session opened for user \(.*\)
# Sep  8 09:57:40 myserver sshd[13295]: pam_unix(sshd:session): session opened for user user2 by (uid=0)
$pattern = <<<'PATTERN'
`(?<month>[a-z]+)\s\s?  # the month
 (?<day>\d+)\s          # the day
 (?<time>[:0-9]+)\s    # the HH:ii:ss
 (?<host>[a-z_-]+)\s   # hostname
 sshd\[\d+\]:\spam_unix\(sshd:session\):\ssession\sopened\sfor\suser\s(?<username>.+)\sby\s\(uid=0\)
 `xsUi
PATTERN;

if (PHP_SAPI == 'cli')
{
	$auth_files = [
		'/var/log/auth.log.1',
		'/var/log/auth.log',
	];
	$datas = [];
	foreach($auth_files as $auth_file)
	{
		$auth = file_get_contents($auth_file);
		if (preg_match_all($pattern, $auth, $matches))
		{
			//print_r($matches);
			//die("FOUND something ");
			foreach($matches[0] as $num => $match)
			{
				$datas[] = [
					'user' => $matches['username'][$num],
					'date' => $matches['day'][$num].' '.$matches['month'][$num].' '.$matches['time'][$num],
					'src'  => $auth_file,
				];
			}

		}
		else
		{
			die("\n\tfor file $auth_file, no match with pattern\n $pattern \n");
		}
	}
	$datas = array_reverse($datas);
	Misc::cache('sftp_last_login', $datas);
}
$datas = Misc::cache('sftp_last_login');
//        for ($i = 0; $i < count($users) && $i <= $max; $i++)
//        {
//            list($user, $date) = $users;
//
//            $datas[] = array(
//                'user' => $user,
//                'date' => $date,
//            );
//        }

if (!isset($_SERVER['argv']) || !in_array('--quiet', $_SERVER['argv']))
	echo json_encode($datas);
