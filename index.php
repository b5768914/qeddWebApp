<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
		<title>QU Earthquke Detector Demo</title> 
		<meta name="apple-touch-fullscreen" content="yes" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <script src="fingerprint2.min.js"></script>
		<script src="jquery-3.3.1.min.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/highcharts-more.js"></script>
		<script src="https://code.highcharts.com/modules/exporting.js"></script>
		<script>
		var chart1;
		var fingerprint = "";
$(function() {

	$( "#dialog-confirm" ).dialog({
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        "ok": function() {
          $( this ).dialog( "close" );
        }
      }
    });

    chart1 = Highcharts.chart('container', {
        chart: {
            type: 'spline',
            events: {
                load: function() {
                    chart1 = this; // `this` is the reference to the chart
                    requestStatsData();
                }
            }
        },
		title:{
    		text:''
		},
        xAxis: {
            type: 'datetime',
            //tickPixelInterval: 150,
            //maxZoom: 20 * 1000
        },
        yAxis: {

            //minPadding: 0.2,
            //maxPadding: 0.2,
			min: -5,
        	max: 5,
        	startOnTick: false,
        	endOnTick: false,
            title: {
                text: ''
            }
        },
        tooltip: {
            crosshairs: true,
            shared: true,
            valueSuffix: '' //was '°C'
        },

        legend: {
			enabled: false
        },
        series: [{
            data: [],
            zIndex: 1,
            marker: {
                fillColor: 'white',
                lineWidth: 2,
                lineColor: Highcharts.getOptions().colors[0]
            }
        }],
		exporting: {
        	enabled: false
    	}
    });        
});

function addChartPoint(pointValue){
	var series = chart1.series[0];
    var shift = series.data.length > 120;
	var chartDataDate = new Date().getTime();
	chart1.series[0].addPoint([chartDataDate,pointValue],true,shift);
}

var serverAlarm = 0;
function getAlertDevices(){
    $.ajax({
        url: "getTableStoreEntities.php?entity=alertDevices"
    }).then(function(data) {
        var obj2 = jQuery.parseJSON(data);
        //var serverDate = obj.Timestamp;
        var serverDate2 = new Date(obj2.Timestamp);
        var nowDate2 = new Date();
        nowDate2.setHours(nowDate2.getHours()); //was initially getHours() -2
        var dateDif2 = serverDate2.getTime() - nowDate2.getTime();
        var Seconds_from_T1_to_T22 = dateDif2 / 1000;
        var secondsDif2 = Math.abs(Seconds_from_T1_to_T22);
        var alertDevices = 0;


        if(secondsDif2<2){
            alertDevices = obj2.count;
        }

        if(parseInt(alertDevices) > 1 && serverAlarm==0){
			serverAlarm = 1;
			document.getElementById("motiontext").style.background = "#FF0000";
            document.getElementById("motiontext").innerHTML = "Server detected multiple Sensor Alarms!";
			setTimeout("serverAlarm=0;",10000);
		}
       
    });
}

function getMyId(){
    $.ajax({
        url: "getSQLresults.php?entity="+fingerprint
    }).then(function(data) {
        var obj3 = jQuery.parseJSON(data);
        if(parseInt(obj3.row_num)>0){
			document.getElementById("myId").innerHTML = "<p style='font-size:3em;'>My ID: "+obj3.row_num+"</p>";
		}
       
    });
}
setInterval(getMyId,1000);
setInterval(getAlertDevices,1000);
		</script>
		<style> 
		#no {
			display: none;	
		}
		
		@media screen {
			html, body, div, span {
				margin: 0;
			  padding: 0;
			  border: 0;
			  outline: 0;
			  font-size: 100%;
			  vertical-align: baseline;
			}			
			body {
				height: auto;
		  	-webkit-text-size-adjust:none;
		  	font-family:Helvetica, Arial, Verdana, sans-serif;
		  	padding:0px;
				overflow-x: hidden;		
			}		
			
			.outer {
				background: rgba(123, 256, 245, 0.9);
				padding: 0px;
				min-height: 48px;
				
			}
			.textbox {
				position: relative;
				float: left;
				width: 100%;
				padding: 7px;
				border: 1px solid rgba(255, 255, 255, 0.6);
				background: rgba(0,255,0,0.75);
				min-height: 30px;
			}	

			.box {
				position: relative;
				float: left;
				width: 45%;
				padding: 7px;
				border: 1px solid rgba(255, 255, 255, 0.6);
				background: rgba(178,215,255,0.75);
				min-height: 160px;
			}	
			
			.box2 {
				position: relative;
				float: left;
				width: 45%;
				padding: 7px;
				border: 1px solid rgba(255, 255, 255, 0.6);
				background: rgba(178,215,255,0.75);
			}	
			
			.box span {
				display: block;
			}
			
			span.head {
				font-weight: bold;				
			}
		
		}
		</style>  
    </head>
    <body>
	<BR>
	&nbsp;<img src="logo.png">
	<p align="right">
	<span style="color:#2E9AFE">
	<i>
    EARTHQUAKE DETECTOR DEMO&nbsp;&nbsp;
	</i>
	</span>
	</p>

        		<div id="yes">
				<div id="myId"></div>
                <div class="textbox" id="motiontext">
                    no motion
                </div>
				<div class="box" id="accel">
					<span class="head">Accelerometer</span>
					<span id="xlabel"></span>
					<span id="ylabel"></span>
					<span id="zlabel"></span>
					<span id="ilabel"></span>					
					<span id="arAlphaLabel"></span>										
					<span id="arBetaLabel"></span>										
					<span id="arGammaLabel"></span>																				
				</div>		
			
				<div class="box" id="gyro">
					<span class="head">Gyroscope</span>
					<span id="alphalabel"></span>			
					<span id="betalabel"></span>
					<span id="gammalabel"></span>
				</div>
	<!--			
				<div class="box2" id="accelcolor">
					<span class="head">Color</span>
				</div>
				<div class="box2" id="gyrocolor">
					<span class="head">Color</span>
				</div>
			-->	
		</div>
		<div id="container" style="min-width: 310px; height: 150px; margin: 0 auto"></div>

		<div id="no">
			Your browser does not support Device Orientation and Motion API. Try this sample with iPhone, iPod or iPad with iOS 4.2+.    
		</div>

		<div id="dialog-confirm" title="Datenschutzerklärung">
  			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Diese App sendet Bewegungsdaten (Beschleunigungs- und Lageinformationen) ihres Gerätes ins Internet. Die Daten werden zum Zwecke des QUNIS Day 2018 Analytic Showcases erhoben und analysiert. Die Daten werden nach dem QUNIS Day 2018 gelöscht, eine andere Verwendung findet nicht statt.</p>
		</div>

		

    <script> 
			// Position Variables
			var x = 0;
			var y = 0;
			var z = 0;

			// Speed - Velocity
			var vx = 0;
			var vy = 0;
			var vz = 0;

			// Acceleration
			var ax = 0;
			var ay = 0;
			var az = 0;
            var az_old = 0;
            var az_diff = 0;
			var ai = 0;
			var arAlpha = 0;
			var arBeta = 0;
			var arGamma = 0;
            var quakeDetected = false;

			var delay = 250;
			var vMultiplier = 0.01;			var alpha = 0;
		
			var alpha = 0;
			var beta = 0;
            var gamma = 0;
            
            

            new Fingerprint2().get(function(result, components) {
                fingerprint = result;
                // this will use all available fingerprinting sources
                //console.log(result)
                // components is an array of all fingerprinting components used
                //console.log(components)
            })
			
			
			if (window.DeviceMotionEvent==undefined) {
				document.getElementById("no").style.display="block";
				document.getElementById("yes").style.display="none";
			} 
			else {
				window.ondevicemotion = function(event) {
					ax = Math.round(Math.abs(event.accelerationIncludingGravity.x * 1));
					ay = Math.round(Math.abs(event.accelerationIncludingGravity.y * 1));
                    az_old = az;
					az = Math.round(Math.abs(event.accelerationIncludingGravity.z * 1));		
					ai = Math.round(event.interval * 100) / 100;
					rR = event.rotationRate;
					if (rR != null) {
						arAlpha = Math.round(rR.alpha);
						arBeta = Math.round(rR.beta);
						arGamma = Math.round(rR.gamma);
					}

/*					
					ax = Math.abs(event.acceleration.x * 1000);
					ay = Math.abs(event.acceleration.y * 1000);
					az = Math.abs(event.acceleration.z * 1000);		
	*/				
				}
								
				window.ondeviceorientation = function(event) {
					alpha = Math.round(event.alpha);
					beta = Math.round(event.beta);
					gamma = Math.round(event.gamma);
				}				
				
				function d2h(d) {return d.toString(16);}
				function h2d(h) {return parseInt(h,16);}
				
				function makecolor(a, b, c) {
					red = Math.abs(a) % 255;
					green = Math.abs(b) % 255;
					blue = Math.abs(c) % 255;
					return "#" + d2h(red) + d2h(green) + d2h(blue);
				}
				
				function makeacceleratedcolor(a, b, c) {
					red = Math.round(Math.abs(a + az) % 255);
					green = Math.round(Math.abs(b + ay) % 255);
					blue = Math.round(Math.abs(c + az) % 255);
					return "#" + d2h(red) + d2h(green) + d2h(blue);
				}

                function detectMotion(){
                    //az_diff = Math.abs(az_old-az);
					az_diff = az_old-az;
                    if (az_diff>3 && serverAlarm==0){
                        document.getElementById("motiontext").style.background = "#FFFF00";
                        document.getElementById("motiontext").innerHTML = "shock detected! Server reports: it's calm out there";
                        quakeDetected = true;
                    }
					else if(az_diff>3){
						quakeDetected = true;
					}
					else if(az_diff<=3 && serverAlarm==0){
						document.getElementById("motiontext").style.background = "#00FF00";
                        document.getElementById("motiontext").innerHTML = "everything ok";
						quakeDetected = false;
					}
                    else{
                        quakeDetected = false;
                    }
                }

                

                function sendMotion2Ehub(){
					addChartPoint(az_diff);
                    <?php
                    function generateSasToken($uri, $sasKeyName, $sasKeyValue) 
                    { 
                        $targetUri = strtolower(rawurlencode(strtolower($uri))); 
                        $expires = time();     
                        $expiresInMins = 60; 
                        $week = 60*60*24*7;
                        $expires = $expires + $week; 
                        $toSign = $targetUri . "\n" . $expires; 
                        $signature = rawurlencode(base64_encode(hash_hmac('sha256',             
                        $toSign, $sasKeyValue, TRUE))); 
                    
                        $token = "SharedAccessSignature sr=" . $targetUri . "&sig=" . $signature . "&se=" . $expires .         "&skn=" . $sasKeyName; 
                        return $token; 
                    }
                    echo 'var token = "'.generateSasToken(getenv('naspUrl'),getenv('naspPolicyName'),getenv('naspPolicyKey')).'";';
                    ?>

                    var http = new XMLHttpRequest();
                    var headers = {Authorization : token};
                    var url = "https://<?php echo getenv('naspUrl'); ?>/<?php echo getenv('ehubName'); ?>/messages?timeout=60&api-version=2014-01";

                    http.open("POST",url, true);
                    http.setRequestHeader("Authorization",token);
                    http.send(  "{'deviceId': '"+fingerprint+"',"+
                                "'x_accel': "+ax+","+
                                "'y_accel': "+ay+","+
                                "'z_accel': "+az+","+
                                "'z_diff': "+az_diff+","+
                                "'quakeDetected': "+quakeDetected+"}");
                    
                }
 
				setInterval(function() {
					document.getElementById("xlabel").innerHTML = "X: " + ax;
					document.getElementById("ylabel").innerHTML = "Y: " + ay;
					document.getElementById("zlabel").innerHTML = "Z: " + az;										
					document.getElementById("ilabel").innerHTML = "I: " + ai;										
					document.getElementById("arAlphaLabel").innerHTML = "arA: " + arAlpha;															
					document.getElementById("arBetaLabel").innerHTML = "arB: " + arBeta;
					document.getElementById("arGammaLabel").innerHTML = "arG: " + arGamma;																									
					document.getElementById("alphalabel").innerHTML = "Alpha: " + alpha;
					document.getElementById("betalabel").innerHTML = "Beta: " + beta;
					document.getElementById("gammalabel").innerHTML = "Gamma: " + gamma;

					//document.getElementById("accelcolor").innerHTML = "Color: " + makecolor(ax, ay, az);
					//document.getElementById("accelcolor").style.background = makecolor(ax, ay, az);
					//document.getElementById("accelcolor").style.color = "#FFFFFF";
					//document.getElementById("accelcolor").style.fontWeight = "bold";

					//document.getElementById("gyrocolor").innerHTML = "Color: " + makecolor(alpha, beta, gamma);
					//document.getElementById("gyrocolor").style.background = makecolor(alpha, beta, gamma);
					//document.getElementById("gyrocolor").style.color = "#FFFFFF";
					//document.getElementById("gyrocolor").style.fontWeight = "bold";

					//document.bgColor = makecolor(arAlpha, arBeta, arGamma);
                    detectMotion();
                    sendMotion2Ehub()
				}, delay);
			} 
			</script>
    </body>
</html>