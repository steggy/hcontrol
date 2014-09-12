#! /usr/bin/php

<?
//Testing the background proccess 
/*while (true) 
{
echo ".\n";
sleep(1);
}*/


if (isset($argv[1])) {
	fclose(STDIN);
	fclose(STDOUT);
	fclose(STDERR);
	$STDIN = fopen('/dev/null', 'r');
	$STDOUT = fopen(getcwd() ."/scene.log", 'wb');
	$STDERR = fopen(getcwd() ."/sceneer.log", 'wb');
    runscene($argv[1]);
}else{
	echo "NO ARG\n";
}


//'*******************************************************************************
function runscene($num)
{
$dir = dir(getcwd() ."/scene");
$farray = array();
$cmdarray = array();
echo "Scene " .$num ." Start " .date('y-m-d H:i:s'); 
echo "\n";
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
                echo "\x07";
            }
            echo "End For\n";
    }
    echo "\nScene End\n";
    return "Scene " .$num ." Complete";
}
//'*******************************************************************************

?>
