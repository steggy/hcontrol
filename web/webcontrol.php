<?
$display = 2
?>

<html>
<head>
	<title>Remote Sound</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" type="text/css" href="webcontrol.css" />
	<script src="jquery.min.js"></script>

        <script type="text/javascript">
            function addDays(theDate, days) 
            {
                return new Date(theDate.getTime() + days*24*60*60*1000);
            }
            //var newDate = addDays(new Date(), 5);

            //For Ajax call
            function st(ii){ 
             if(document.getElementById('serverip'+ii).value == "")
                    {
                        $('#div'+ii).html("SERVER ERROR");
                        return;
                    }
                    var sip = document.getElementById('serverip'+ii).value;
                    var sport = document.getElementById('serverport'+ii).value;
                    var newDate = addDays(new Date(), 15);
                    document.cookie="server"+ii+"="+sip+"; expires="+newDate+";";
                    document.cookie="serverport"+ii+"="+sport+"; expires="+newDate+";";
                 //alert('This is vv: ' + vv);
                $.ajax({
                    type: "POST",
                    url: "webcontrolclient.php",
                    data: "st='stop'&si="+sip+"&sp="+sport,
                    success:function(data){
                        //alert('This was sent back: ' + data);
                        //Next line adds the data from PHP into the DOM
                        $('#divdiv').html(data);
                        
                    }
                    });
                }
            function mm(ii){ 
		     var selects = document.getElementById('p350'+ii);
			//var red = document.getElementById("red");
			//var green = document.getElementById("green");
			//var blue = document.getElementById("blue");
		     var vv = selects.options[selects.selectedIndex].value;
		     //var rr = red.options[red.selectedIndex].value;
		     //var gg = red.options[green.selectedIndex].value;
		     //var bb = red.options[blue.selectedIndex].value;
             if(document.getElementById('serverip'+ii).value == "")
                    {
                        $('#div'+ii).html("SERVER ERROR");
                        return;
                    }
                    var sip = document.getElementById('serverip'+ii).value;
                    var sport = document.getElementById('serverport'+ii).value;
                    var newDate = addDays(new Date(), 15);
                    document.cookie="server"+ii+"="+sip+"; expires="+newDate+";";
                    document.cookie="serverport"+ii+"="+sport+"; expires="+newDate+";";
                 //alert('This is vv: ' + vv);
                $.ajax({
                    type: "POST",
                    url: "webcontrolclient.php",
                    data: "ss="+vv+"&si="+sip+"&sp="+sport,
                    success:function(data){
                        //alert('This was sent back: ' + data);
                        //Next line adds the data from PHP into the DOM
                        //$('#div1').html(data);
                        //document.getElementById("button"+zone).background-color="green";
                        //s = data.responsetext;
                        /*var myarray = data.split(",");				
                        $('#div1').html(myarray[0]);
                        document.getElementById('button1').style.backgroundColor=myarray[1];
                        document.getElementById('button2').style.backgroundColor=myarray[2];
                        document.getElementById('button3').style.backgroundColor=myarray[3];
                        document.getElementById('button4').style.backgroundColor=myarray[4];*/
                    }
                    });
                }
            function showscene(ii){ 
             var selects = document.getElementById('p350'+ii);
             var vv = selects.options[selects.selectedIndex].value;
             if(document.getElementById('serverip'+ii).value == "")
                    {
                        $('#div'+ii).html("SERVER ERROR");
                        return;
                    }
                    var sip = document.getElementById('serverip'+ii).value;
                    var sport = document.getElementById('serverport'+ii).value;
                    var newDate = addDays(new Date(), 15);
                    document.cookie="server"+ii+"="+sip+"; expires="+newDate+";";
                    document.cookie="serverport"+ii+"="+sport+"; expires="+newDate+";";
                 alert('This is vv: ' + vv);
                $.ajax({
                    type: "POST",
                    url: "webcontrolclient.php",
                    data: "first=-sl&scene="+vv+"&si="+sip+"&sp="+sport,
                    success:function(data){
                        $('#txt'+ii).html(data);
                    }
                    });
                }
            function ww(ss){
                 //alert('This is ss: ' + ss);
                 if(document.getElementById('serverip'+ss).value == "")
                    {
                        $('#div'+ss).html("SERVER ERROR");
                        return;
                    }
                    var sip = document.getElementById('serverip'+ss).value;
                    var sport = document.getElementById('serverport'+ss).value;
                    var newDate = addDays(new Date(), 15);
                    document.cookie="server"+ss+"="+sip+"; expires="+newDate+";";
                    document.cookie="serverport"+ss+"="+sport+"; expires="+newDate+";";
                $.ajax({
                    type: "POST",
                    url: "webcontrolclient.php",
                    data: "first=-s&ww="+ss+"&si="+sip+"&sp="+sport,
                    success:function(data){
                        //alert(data);
                        //exit;
                        var myarray = data.split("*");				
                        $('#div'+ss).html(myarray[0]);
                        
                        var select = document.getElementById('p350'+ss);
                        //var options = ["Asian", "Black"];
                        select.options.length = 0;
                        var i;
                        for (i = 1; i < myarray.length; i++) {
                            var opt = myarray[i];
                            var el = document.createElement("option");
                            el.textContent = opt;
                            el.value = i;
                            select.appendChild(el);
                        }
                    }
                    });
                }    
        </script>
        <style>
        input[type=submit] 
        {
        border: 1px solid #5C755E;
        border-radius: 6px;
        box-shadow: 5px 5px 3px #856270;
        }

        button
        {
        border: 1px solid #5C755E;
        border-radius: 6px;
        box-shadow: 3px 3px 3px #856270;
        font-size:20px;
        }
        </style>
        <script type="text/javascript">

        </script>
</head>
<!--<body onload="ww('status');" bgcolor=#F7DCB4>-->
<body >
	<?
    for ($i = 1; $i < $display; $i++)
    {
        ?>
    <div id=divss>
        <span style="font-size:1em;">Halloween Prop Controlor 2014</span>
		<div class="divdiv" id="div<?=$i;?>">&nbsp;</div>
        <!--<form action="<?=$_SERVER['REQUEST_URI'];?>" method="get">-->
        <select name=playit id='p350<?=$i;?>' size="8"></select>
        <textarea id="txt<?=$i;?>" cols=20 rows=10></textarea>
        <!--<input type=submit onclick="mm()">
    </form>-->
        <br><br>
		<button id="button1" onclick="mm('<?=$i;?>')">Play</button> <button id="button9" onclick="st('<?=$i;?>')">STOP</button>
		<button id="button8" onclick="showscene('<?=$i;?>')">Show</button>
		<br><br>
		<button id="button4" onclick="ww('<?=$i;?>')">Status</button>
        <br><br>
        <?
        if (isset($_COOKIE["server" .$i])) 
        {
            $ssip = $_COOKIE["server" .$i];
        }else{
            $ssip ='';
        }
        if (isset($_COOKIE["serverport" .$i])) 
        {
            $ssport = $_COOKIE["serverport" .$i];
        }else{
            $ssport ='';
        }
        ?>
        S-IP <input class="ipserver" name="serverip<?=$i;?>" id="serverip<?=$i;?>" type=text value="<?=$ssip;?>">
        Port: <input size =4 class="ipserver" name="serverport<?=$i;?>" id="serverport<?=$i;?>" type=text value="<?=$ssport;?>">
	</div>
    <?
        if($i % 3 == 0)
        {
            echo "<br>";
        }
    }
    ?>
</body>
</html>