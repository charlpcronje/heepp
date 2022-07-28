<?php

try {
	$ftp = new Ftp;

	// Opens an FTP connection to the specified host
	$ftp->connect('ftp.ed.ac.uk');

	// Login with username and password
	$ftp->login('anonymous', 'example@example.com');

	// Download file 'README' to local temporary file
	$temp = tmpfile();
	$ftp->fget($temp, 'README', Ftp::ASCII);

	// echo file
	echo '<pre>';
	fseek($temp, 0);
	fpassthru($temp);

} catch (FtpException $e) {
	echo 'Error: ', $e->getMessage();
}

//FTP for PHP (c) David Grudl, 2008 (http://davidgrudl.com)
//
//
//Introduction
//------------
//
//FTP for PHP is a very small and easy-to-use library for accessing FTP servers.
//
//
//Project at GoogleCode: http://ftp-php.googlecode.com
//My PHP blog: http://phpfashion.com
//
//
//Requirements
//------------
//- PHP (version 5 or better)
//
//
//Usage
//-----
//
//Opens an FTP connection to the specified host:
//
//	$ftp = new Ftp;
//	$ftp->connect($host);
//
//Login with username and password
//
//	$ftp->login($username, $password);
//
//Upload the file
//
//	$ftp->put($destination_file, $source_file, FTP_BINARY);
//
//Close the FTP stream
//
//	$ftp->close();
//	// or simply unset($ftp);
//
//Ftp throws exception if operation failed. So you can simply do following:
//
//	try {
//		$ftp = new Ftp;
//		$ftp->connect($host);
//		$ftp->login($username, $password);
//		$ftp->put($destination_file, $source_file, FTP_BINARY);
//
//	} catch (FtpException $e) {
//		echo 'Error: ', $e->getMessage();
//	}
//
//On the other hand, if you'd like the possible exception quietly catch, call methods with the prefix 'try':
//
//	$ftp->tryDelete($destination_file);
//
//When the connection is accidentally interrupted, you can re-establish it using method $ftp->reconnect().