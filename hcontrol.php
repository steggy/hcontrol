#! /usr/bin/php

<?php
//Hey you have to turn on pi-blaster
///root/bin/pi-blast/pi-blaster exec it

//Set globals
global $address;
global $port;
global $sock;
global $debug;
global $stop;
global $inifile;
global $ini_array;
global $cmdini_array;
global $cmdinifile;
global $redpin;
global $greenpin;
global $bluepin;
global $whitepin;
global $strobedelay;
global $cmd;
global $count;
global $count2;
global $randcolorpause;
global $fadepause;
global $debugmode;
global $runpid;


$GLOBALS['cmd'] = '';
$GLOBALS['count'] = 0;
$GLOBALS['count2'] = 0;

$GLOBALS['inifile']= getcwd() ."/hcontrol.ini";
$GLOBALS['cmdinifile'] = getcwd() ."/hcmd.ini";

$GLOBALS['debug'] = true;
$GLOBALS['stop'] = false;

$rl = '0';
$gl = '0';
$bl = '0';
$orl = '99';
$ogl = '99';
$obl = '99';

readini($GLOBALS['inifile']);
if (isset($argv[1])) 
{
switch ($argv[1]) {
    case '-r':
        setsock();
        $GLOBALS['debug'] = true;
        maindebug();
        break;
    case '-D':
        setsock();
        $GLOBALS['debug'] = false;
        main();
        break;
    default:
        showusage();
        break;
}
}else{
    showusage();
    exit;
}
/*************************************/
function setsock()
{
// Set time limit to indefinite execution
set_time_limit (0);
// Set the ip and port we will listen on
$GLOBALS['address'] = '0.0.0.0';
$GLOBALS['port'] = 9900;
if ($GLOBALS['debug']) 
{
    echo "Port " .$GLOBALS['port'] ."\n";     
}
// Create a TCP Stream socket
$GLOBALS['sock'] = socket_create(AF_INET, SOCK_STREAM, 0);
socket_set_option($GLOBALS['sock'], SOL_SOCKET, SO_SNDBUF, 25000);
// Bind the socket to an address/port
socket_bind($GLOBALS['sock'], $GLOBALS['address'], $GLOBALS['port']) or die('Could not bind to address');
//socket_set_nonblock($GLOBALS['sock']);
// Start listening for connections
socket_listen($GLOBALS['sock']);
socket_set_nonblock($GLOBALS['sock']);
}
/*************************************/

/*************************************/
function main()
{
//redirecting output for daemon mode
//redirecting standard out
//Make sure the user running the app has rw on the log file
fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);
$STDIN = fopen('/dev/null', 'r');
$STDOUT = fopen('/var/log/rgbled.log', 'wb');
$STDERR = fopen('/var/log/rgblederror.log', 'wb');
//dont't forget to create these log files
    while (true) 
    {    
        checksock(); 
    }
// Close the master sockets
socket_close($GLOBALS['sock']);

}
/*************************************/

/*************************************/
function maindebug()
{
    while (true) 
    {    
        checksock(); 
    }
// Close the master sockets
socket_close($GLOBALS['sock']);

}
/*************************************/

/*************************************/
function checksock()
{
    /* Accept incoming requests and handle them as child processes */

    $client = @socket_accept($GLOBALS['sock']);
    //echo "Client " .$client ."\n";
    if (!$client === false) 
    {

    // Read the input from the client &#8211; 1024 bytes

    //$input = socket_read($client, 1024);
    $status = @socket_get_status($client);

    $input = @socket_read($client, 2048);

    // Strip all white spaces from input
    echo "RAW " .$input ."\n";
    if($input == '')
    {
        break;
    }
    //$output = ereg_replace("[ \t\n\r]","",$input).chr(0);
    //$output = ereg_replace("[ \t\n\r]","",$input);
    $output = explode(" ", $input);
    
    
        switch (strtolower($output[0])) {
            case 'white':
                $response = "Turn on white\n\n";
                socket_write($client, $response);
                socket_close($client);
                break;
            case '-get':
                    if (isset($output[1])) 
                    {
                        switch(strtolower($output[1]))
                        {
                            case 'sd':
                                    $response = "Strobe Duration " .$GLOBALS['strobedelay'] ."\n";
                                    socket_write($client, $response,strlen($response));
                                    socket_close($client);
                                break;
                        }
                    }else{
                        $response = shwhelp();
                        socket_write($client, $response,strlen($response));
                        socket_close($client);
                    }
                break;
            case '-yard':
            case '-y':
                if (isset($output[1])) 
                {
                    yardlight($output[1]);
                }else{
                    $response = "Missing power number 0-10\n";
                    socket_write($client, $response);
                    socket_close($client);
                }
                break; 
            case '-color';
            case '-c';
                echo "In Color Case\n";
                echo "OUTPUT 1 " .$output[1] ."\n";
                if (isset($output[1])) 
                {
                    echo "COLOR SET\n";
                    $colorv = explode(",", $output[1]);
                    echo "SIZE COLORV " .sizeof($colorv);
                    if (sizeof($colorv) == 3) 
                    {
                        $GLOBALS['rl'] = $colorv[0];
                        $GLOBALS['gl'] = $colorv[1];
                        $GLOBALS['bl'] = $colorv[2];
                        changecolor($colorv[0],$colorv[1],$colorv[2]);
                        $response = "Color R" .$GLOBALS['rl'] ." G" .$GLOBALS['gl'] ." B" .$GLOBALS['bl'] ."\n";
                        socket_write($client, $response);
                        socket_close($client);
                    }   
                }
                break;
                case '-setcolor';
                echo "In SetColor Case\n";
                if (isset($output[1])) 
                {
                    $colorv = explode(",", $output[1]);
                    if (sizeof($colorv) == 3) 
                    {
                        $response = "Color Set to\n";
                        $response .="R " .$colorv[0] ." G " .$colorv[1] ." B " .$colorv[2];
                        socket_write($client, $response);
                        socket_close($client);
                        readini($GLOBALS['inifile']);
                        $GLOBALS['rl'] = $colorv[0];
                        $GLOBALS['gl'] = $colorv[1];
                        $GLOBALS['bl'] = $colorv[2];
                        
                        $GLOBALS['ini_array']['color']['r']=$GLOBALS['rl'];
                        $GLOBALS['ini_array']['color']['g']=$GLOBALS['gl'];
                        $GLOBALS['ini_array']['color']['b']=$GLOBALS['bl'];
                        $result = write_ini_file($GLOBALS['ini_array'],$GLOBALS['inifile']);
                        
                    }   
                }
                break;
            case '-red';
                        $GLOBALS['rl'] = 10;
                        $GLOBALS['gl'] = 0;
                        $GLOBALS['bl'] = 0;
                        changecolor($GLOBALS['rl'],$GLOBALS['gl'],$GLOBALS['bl']);
                break;
            case 'test':
                $response = "Testing\n\n";
                socket_write($client, $response);
                socket_close($client);
                looptest();
                break;

            case "-s":
                    socket_write($client, listscene());
                    socket_close($client);
                    
                    break; 
            case "-snd":
                    socket_write($client, listsound());
                    socket_close($client);
                    break;         
                       
            case "-sl":
                
                if (isset($output[1])) {
                    socket_write($client, showscene($output[1]));
                    
                }else{
                socket_write($client, "Need Scene ");
                }
                socket_close($client);
                break;
            case "-run":
                if (isset($output[1])) {
                    socket_write($client, runscene($output[1]));
                    socket_close($client);
                }
                
                break;
            case '-p':
            case '-P':
                //try to fork or background process
                if (isset($output[1])) 
                {
                    /*socket_write($client, $output[1]);
                    socket_close($client);
                    return;*/
                    $c = getcwd() .'/bp.php ' .$output[1] .' >bp.txt 2>&1 & echo $!';
                    //socket_write($client, $c);
                    $GLOBALS['runpid'] = system($c);
                    $status = system('ps aux | grep -i ' . $GLOBALS['runpid']);
                    //echo $status;
                    socket_write($client, $GLOBALS['runpid']);
                }else{
                    socket_write($client, "Scene Number Required");
                }
                socket_close($client);
                break;
            case '-pk':
                 if (isset($output[1])) {
                    $status = system('sudo kill ' .$output[1]);
                    socket_write($client, $status);
                    socket_close($client);
                }
                break;
            case '-ps':
                if (isset($GLOBALS['runpid'])) 
                {
                    $status = system('ps aux | grep -i ' . $GLOBALS['runpid'] .' | grep -v grep');
                    socket_write($client, "PID STATUS " .$GLOBALS['runpid'] . " " .$status ." end");
                }else{
                    socket_write($client, "PID NOT SET");
                }
                socket_close($client);
                break;           
            case '-reset':
                //we should try a reset process to get all the relays back to of
                break;    
            
            case 'kill':
                $response = "Killing\n\n";
                socket_write($client, $response);
                socket_close($client);
                socket_close($GLOBALS['sock']);
                exit;
                break;    
            case "--help":
            case "-help":
            case "--h":
            case "-h":
                $response = shwhelp();
                socket_write($client, $response,strlen($response));
                socket_close($client);
                break;
            default:
                $response = "default\n\n";
                socket_write($client, $response);
                socket_close($client);
                break;
        }
    }
    // Display output back to client

    //socket_write($client, $response);

    // Close the client (child) socket

    //socket_close($client);
}

//'*******************************************************************************
function runscene($num)
{
$dir = dir(getcwd() ."/scene");
$farray = array();
$cmdarray = array();
//List files in directory
while (($file = $dir->read()) !== false){
    //Make sure it's a .txt file
    if(strlen($file) < 5 || substr($file, -4) != '.scn')
        continue;    
    $farray[] = $file; 
    //$ll .= $c .") filename: " . $file ."*";
}
$dir->close();
//$file_contents = file_get_contents( getcwd() ."/scene/" .$farray[$num -1]);
$runarray = explode("\n", file_get_contents(getcwd() ."/scene/" .$farray[$num -1]));
    for ($i=0; $i < sizeof($runarray); $i++) { 
        if (substr($runarray[$i], 0, 1) !== ';')
        {
            echo $runarray[$i] ."\n"; 
            $cmdarray[] = $runarray[$i];
        } 
    }
    $loop = split('=', $cmdarray[0]);
    if ($loop[0] == 'loop')  
    {
        echo "Loop " .$loop[1] ."\n";
        $l =''; 
        while($loop[1] > 0){
            echo $loop[1] ."\n";
            for ($i=1; $i < sizeof($cmdarray); $i++) 
            { 
                $cmd = split('=', $cmdarray[$i]);
                switch(strtolower($cmd[0])){
                    case 'run':
                        $rr = split(",", $cmd[1]);
                        echo "Run Relay " .$rr[0] ." " .$rr[1] ."\n";
                        break;
                    case 'sleep':
                        echo "Sleep for " .$cmd[1] ."\n";
                        sleep($cmd[1]);
                        break;    
                }
            }
            echo "End For\n";
            $loop[1]--;
        }
    }else{
            for ($i=0; $i < sizeof($cmdarray); $i++) 
            { 
                $cmd = split('=', $cmdarray[$i]);
                switch(strtolower($cmd[0])){
                    case 'run':
                        $rr = split(",", $cmd[1]);
                        echo "Run Relay " .$rr[0] ." " .$rr[1] ."\n";
                        break;
                    case 'sleep':
                        echo "Sleep for " .$cmd[1] ."\n";
                        sleep($cmd[1]);
                        break;    
                }
            }
            echo "End For\n";
    }
    return "Scene " .$num ." Complete";
}
//'*******************************************************************************

//'*******************************************************************************
function showscene($num)
{
    //Open directory
$dir = dir(getcwd() ."/scene");
$farray = array();
//List files in directory
while (($file = $dir->read()) !== false){
    //Make sure it's a .txt file
    if(strlen($file) < 5 || substr($file, -4) != '.scn')
        continue;    
    $farray[] = $file; 
    //$ll .= $c .") filename: " . $file ."*";
}

$dir->close();
$file_contents = file_get_contents( getcwd() ."/scene/" .$farray[$num -1]);
return $file_contents;
}
//'*******************************************************************************

//'*******************************************************************************
function listscene(){
//Open directory
//Note: when returning multiple lines, seperate with a '*'     
$dir = dir(getcwd() ."/scene");
$c=0;
$ll='Use -sl x to list the scene x is the file number*';
//List files in directory
while (($file = $dir->read()) !== false){
    //Make sure it's a .txt file
    if(strlen($file) < 5 || substr($file, -4) != '.scn')
        continue;
    $c++;    
    $ll .= $c .") " . $file ."*";
}

$dir->close();
return $ll;
}
//'*******************************************************************************

//'*******************************************************************************
function listsound(){
//Open directory
//Note: when returning multiple lines, seperate with a '*'     
$dir = dir(getcwd() ."/sound");
$c=0;
$ll=''; //Use -sl x to list the scene x is the file number*';
//List files in directory
while (($file = $dir->read()) !== false){
    //Make sure it's a .txt file
    if(strlen($file) < 5 || substr($file, -4) != '.mp3')
        continue;
    $c++;    
    $ll .= $c .") " . $file ."*";
}

$dir->close();
return $ll;
}
//'*******************************************************************************

//'*******************************************************************************
function looptest()
{
    while(true)
    {
        echo "Sleeping....." .date('H:i:s') ."\n";
        checksock();
        if($GLOBALS['stop'])
            {
                $GLOBALS['stop'] = false;
                return;
            }
        sleep(1);
    }
}
//'*******************************************************************************





//'*******************************************************************************
function strobe($r,$g,$b,$d,$t)
{
    echo "THIS IS T " .$t ."\n";
    for($i =0; $i < $t; $i++)
    {
        changecolor($r,$g,$b);
        usleep($d/2);
        changecolor(0,0,0);
        usleep($d/2);
    
    }
}
//'*******************************************************************************

//'*******************************************************************************
function strobeII()
{
$d=$GLOBALS['ini_array']['strobe']['delay'];  
echo "STROBE DELAY " .$d ."\n";  
$r = $GLOBALS['rl'];
$g = $GLOBALS['gl'];
$b = $GLOBALS['bl'];
  
    while(true)
    {
        changecolor($r,$g,$b);
        usleep($d/2);
        changecolor(0,0,0);
        usleep($d/2);
        //readcmdini($GLOBALS['cmdinifile']);
        checksock();
        if($GLOBALS['stop'])
            {
                $GLOBALS['stop'] = false;
                return;
            }
        switch(strtolower($GLOBALS['cmdini_array']['command']['cmd']))
        {
            
            case 'white':
                yardlight();
                break;
            case 'stop':
            case 'color':
                return;
                break;  
        }
        
    
    }
}
//'*******************************************************************************

//'*******************************************************************************
function updown($o,$n)
{
    if($o > $n)
    {
        return 0;
    }else{
        return 1;
    }
}
//'*******************************************************************************




//'*******************************************************************************
function readcmdini()
{
    //echo $GLOBALS['cmdinifile'];
if (!file_exists($GLOBALS['cmdinifile'])) {
    echo "*********************************************\nrgbled.php\nFile not found: " .$file ."\n\n";
    die;
}
$GLOBALS['cmdini_array'] = parse_ini_file($GLOBALS['cmdinifile'],true);
}
//'*******************************************************************************

//'*******************************************************************************
function readini($file)
{
    //echo $file;
if (!file_exists($file)) {
    echo "*********************************************\nrgbled.php\nFile not found: " .$file ."\n\n";
    die;
}
$GLOBALS['ini_array'] = parse_ini_file($file,true);

}
//'*******************************************************************************

//'*******************************************************************************
function write_ini_file($assoc_arr, $path, $has_sections=TRUE) { 
    $content = ""; 
    if ($has_sections) { 
        foreach ($assoc_arr as $key=>$elem) { 
            $content .= "[".$key."]\n"; 
            foreach ($elem as $key2=>$elem2) { 
                if(is_array($elem2)) 
                { 
                    for($i=0;$i<count($elem2);$i++) 
                    { 
                        $content .= $key2."[] = \"".$elem2[$i]."\"\n"; 
                    } 
                } 
                else if($elem2=="") $content .= $key2." = \n"; 
                else $content .= $key2." = \"".$elem2."\"\n"; 
            } 
        } 
    } 
    else { 
        foreach ($assoc_arr as $key=>$elem) { 
            if(is_array($elem)) 
            { 
                for($i=0;$i<count($elem);$i++) 
                { 
                    $content .= $key2."[] = \"".$elem[$i]."\"\n"; 
                } 
            } 
            else if($elem=="") $content .= $key2." = \n"; 
            else $content .= $key2." = \"".$elem."\"\n"; 
        } 
    } 

    if (!$handle = fopen($path, 'w')) { 
    fclose($handle); 
        return false; 
    } 
    if (!fwrite($handle, $content)) { 
    fclose($handle);         
    return false; 
    } 
    fclose($handle); 
    return true; 
}
//'*******************************************************************************

//'*******************************************************************************
function showusage()
{
    /*echo "rgbsock.php Rev ". $GLOBALS['revmajor'] ."." .$GLOBALS['revminor'] ."\n";*/
    echo "hcontrol.php (Halloween Control) Rev ? \n";
    echo "Usage: hcontrol.php [option]...\n Using the Raspberry pi as a Halloween Prop Controller\n";
    echo "Mandatory arguments\n";
    echo "  -h, \t This help\n";
    echo "  -x, \t Turn off all sprinklers\n";
    echo "  -z [1-8] [0,1], \t Turn on/off zone\n";
    echo "  -c [.001-10] [.001-10] [.001-10], \t Set and turn on LED - Color values seperated by space.\n";
    echo "  -s [.001-10] [.001-10] [.001-10] [x-duration] [y-count], \t Strobe LED - Color values seperated by space. \n";
    echo "  -r, \t Used for debuging from console\n";
    echo "  -D, \t Daemon mode usualy called from sprinkd\n";
    echo "Zones and pin numbers are set in the sprink.ini file\n";      
    echo "\n\n";
}
//'*******************************************************************************

//'*******************************************************************************
function shwhelp()
{
    /*echo "rgbsock.php Rev ". $GLOBALS['revmajor'] ."." .$GLOBALS['revminor'] ."\n";*/
    $hstring = "\nhcontrolclient.php Rev 1 \n";
    $hstring .= "Usage: hcontrolclient.php [option]...\n Using the Raspberry pi as a Halloween Prop Controller\n";
    $hstring .= "This help shown from server\n";
    $hstring .= "Mandatory arguments\n";
    $hstring .= "  -h, \t This help\n";
    $hstring .= "  -s, \t Show Scenes\n";
    $hstring .= "  -sl [x], \t Show Scene contents\n";
    $hstring .= "  -p [x], \t Play/Run SceneShow Scenes\n";
    $hstring .= "  -snd, \t List sound files\n";
    $hstring .= "Pin numbers for relays are set in the " .$GLOBALS['inifile'] ." file\n";      
    $hstring .= "\n\n";
    return $hstring;
}
//'*******************************************************************************


?>