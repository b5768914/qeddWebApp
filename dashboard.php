<html>
<head>

<script src="jquery-3.3.1.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script>

var chart1;
$(function() {
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
        title: {
            text: 'Live sensor aggregates'
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
                text: 'Value',
                margin: 80
            }
        },
        tooltip: {
            crosshairs: true,
            shared: true,
            valueSuffix: '' //was '°C'
        },

        legend: {
        },
        series: [{
            name: 'AverageMotion',
            data: [],
            zIndex: 1,
            marker: {
                fillColor: 'white',
                lineWidth: 2,
                lineColor: Highcharts.getOptions().colors[0]
            }
        }, {
            name: 'MotionRange',
            data: [],
            type: 'arearange',
            lineWidth: 0,
            linkedTo: ':previous',
            color: Highcharts.getOptions().colors[0],
            fillOpacity: 0.3,
            zIndex: 0,
            marker: {
                enabled: false
            }
        }]
    });        
});

function requestStatsData() {
    $.ajax({
        url: "getTableStoreEntities.php?entity=stats"
    }).then(function(data) {
        var obj = jQuery.parseJSON(data);
        //var serverDate = obj.Timestamp;
        var serverDate = new Date(obj.Timestamp);
        var nowDate = new Date();
        nowDate.setHours(nowDate.getHours()); //was initially getHours() -2
        var dateDif = serverDate.getTime() - nowDate.getTime();
        var Seconds_from_T1_to_T2 = dateDif / 1000;
        var secondsDif = Math.abs(Seconds_from_T1_to_T2);
        
        if(secondsDif<2){
            var series = chart1.series[0];
            var shift = series.data.length > 30;
            var chartDataDate = new Date().getTime();
            chart1.series[0].addPoint([chartDataDate,obj.average],true,shift);
            chart1.series[1].addPoint([chartDataDate,obj.minimum,obj.maximum],true,shift);
        }


    });
    setTimeout(requestStatsData, 1000); 
}

function getConnectedDevices(){
    $.ajax({
        url: "getTableStoreEntities.php?entity=connectedDevices"
    }).then(function(data) {
        var obj = jQuery.parseJSON(data);
        //var serverDate = obj.Timestamp;
        var serverDate = new Date(obj.Timestamp);
        var nowDate = new Date();
        nowDate.setHours(nowDate.getHours()); //was initially getHours() -2
        var dateDif = serverDate.getTime() - nowDate.getTime();
        var Seconds_from_T1_to_T2 = dateDif / 1000;
        var secondsDif = Math.abs(Seconds_from_T1_to_T2);
        var connectedDevices = 0;

        $('#debugging').html("serverDate: "+serverDate.toString()+"<BR>nowDate: "+nowDate.toString()+"<BR>secondsDif: "+secondsDif);

        if(secondsDif<2){
            connectedDevices = obj.count;
        }


       $('#connectedDevices').html("<p style='font-size:3em;'><b>"+connectedDevices+"</b></p>");
       getAlertDevices();
    });
}

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

        if(parseInt(alertDevices) > 1){blinkit();}
       $('#alertDevices').html("<p style='font-size:3em;'><b>"+alertDevices+"</b></p>");
    });
}

setInterval(getConnectedDevices,1000);

var blinking = 0;
function blinkit(){
    if(blinking==0){
        blinking = 1;
        intrvl=0;
        for(nTimes=0;nTimes<6;nTimes++){
            intrvl += 150;
            setTimeout("document.bgColor='#FF0000';",intrvl);
            intrvl += 150;
            setTimeout("document.bgColor='#FFFFFF';",intrvl);
        }
        setTimeout("blinking=0;",1950);
    }
    
}


</script>
</head>
<body>
<BR>
    <p align="right">
	&nbsp;<img src="logo.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<BR>
	
	<span style="color:#2E9AFE">
	<i>
    EARTHQUAKE DETECTOR DEMO&nbsp;&nbsp;
	</i>
	</span>
	</p>
    <BR>
<table border="0" width="100%">
    <tr>
        <td width="50%"><center><b>Connected Devices</b><div id="connectedDevices"></div></center></td>
        <td width="50%"><center><b>Alert Devices</b><div id="alertDevices"></div></center></td>
    </tr>
</table>
<BR>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<BR>
<BR>
<b>Debugging Info</b>
<div id="debugging"> </div>

<script>









</script>

</body>
</html>