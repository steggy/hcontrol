

<?php

if (isset($_POST['first'])) 
{
	switch (strtolower($_POST['first'])) {
		case "-s":
			$cstring = "-s";
			break;
		case "-sl":
			$cstring = "-sl " .$_POST['scene'];
			break;
		default:
			$cstring = "-h";
			break;
	}
	
}else{
	$cstring = "-h";	
}

sendcmd($cstring);


function sendcmd($cmd)
{
	// where is the socket server?
	$host="127.0.0.1";
	$port = 9900;
	 
	// open a client connection
	$fp = fsockopen ($host, $port, $errno, $errstr);
	 
	if (!$fp)
	{
	$result = "Error: could not open socket connection";
	$result .= "Check that rgbledsck.php is running";
	}
	else
	{
	// get the welcome message
	//fgets ($fp, 1024);
	// write the user string to the socket
	fputs ($fp, $cmd);
	// get the result
	//$result = fgets ($fp, 100000);
	$result = fread($fp, 100000);
	// close the connection
	fputs ($fp, "exit");
	fclose ($fp);
	 
	// trim the result and remove the starting ?
	//$result = trim($result);
	//$result = substr($result, 2);
	 //$r = explode("*", $result);
	 //for($i =0;$i < sizeof($r); $i++)
	// {
	//	echo $r[$i] ."\n";	 	
	// }

	 echo $result ."\n";
	// now print it to the browser
	/*}
	?>
	Server said: <b><? echo $result; ?></b>
	<?*/
	}
}


?>

