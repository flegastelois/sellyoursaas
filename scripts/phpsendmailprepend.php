<?php
/**
  This script is a prepend file so env is set for phpsendmail.php

  Modify your php.ini file to add:
  auto_prepend_file = /usr/local/bin/phpsendmailprepend.php
*/

#if (! empty($_SERVER) && (preg_match('/phpsendmail/', @$_SERVER['SCRIPT_FILENAME']) || preg_match('/phpsendmail/', @$_SERVER['SCRIPT_NAME'])) )
if (@$_POST['action'] == 'send' || @$_POST['action'] == 'sendallconfirmed')
{
	$tmpfile='/tmp/phpsendmailprepend-'.posix_getuid().'-'.getmypid().'.tmp';
	@unlink($tmpfile);
	file_put_contents($tmpfile, var_export($_SERVER, true));
	chmod ($tmpfile, 0660);
}

// environment variables that should be available in child processes
$envVars = array(
    'HTTP_HOST',
    'SCRIPT_NAME',
    'DOCUMENT_ROOT',
    'REMOTE_ADDR',
    'REQUEST_URI'
);
// sanitizing environment variables for Bash ShellShock mitigation
// (CVE-2014-6271, CVE-2014-7169, CVE-2014-7186, CVE-2014-7187, CVE-2014-6277)

$sanitizeChars = str_split('(){};');
foreach ($envVars as $key) {
    $value = str_replace($sanitizeChars, '', @$_SERVER[$key]);
    putenv("$key=$value");
}
