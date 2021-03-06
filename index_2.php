<?php 
      $ip = trim($_SERVER['REMOTE_ADDR']);
      if($ip === '127.0.0.1') { $ip = '128.30.52.72'; $ip = '80.97.88.77';}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
   <title>Photos Around You</title>
   <link rel="stylesheet" href="http://yui.yahooapis.com/2.7.0/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css">
   <link rel="stylesheet" href="http://yui.yahooapis.com/2.7.0/build/base/base.css" type="text/css">   
   <style type="text/css">
   html,body{font-family:georgia,serif;}
   ul li{list-style: none;float: left}
   h1{font-size: 25px}
   #ft {margin-top: 30px;color: #999}
   #ft p{font-size:85%;text-align: left;}
   #ft p a {color: #69c}
   </style>
</head>
<body>
<div id="doc" class="yui-t7">
   <div id="hd" role="banner"><h1>Determining your location...</h1></div>
   <div id="bd" role="main">
	<div class="yui-g">
           <ul id="results-photos">Loading...</ul>
	</div>
	</div>
   <div id="ft" role="contentinfo"><p>Created by @<a href="http://twitter.com/thinkphp">thinkphp</a> using <a href="http://developer.yahoo.com/yql/console">YQL</a>, <a href="yqlquery.js">yqlquery</a> and  and the Open Data Table <a href="http://thinkphp.ro/apps/YQL/ip.location2.xml">ip.location</a></p></div>
</div>
<script type="text/javascript" src="yqlquery.js"></script>
<script type="text/javascript">
var ip2 = '87.248.122.122', ip = '69.147.76.210';
function $(id){return document.getElementById(id);}
var callback = function(o){
    if(window.console) {console.log(o)};
    var n = o.query.results.photo.length, 
        out = '', 
        photos = o.query.results.photo;
        for(var i=0;i<n;i++) {
               var curr = photos[i];
               var src = 'http://farm' + curr.farm + '.static.flickr.com/' + curr.server + '/' + curr.id + '_' + curr.secret + '_s.jpg';
               out += '<li><a href="http://www.flickr.com/photos/'+curr.owner+'/'+curr.id+'/" title="'+curr.title+'"><img src="'+src+'" alt="'+curr.title+'"></a></li>';
        }
    $('results-photos').innerHTML = out;
};

function receivedInfo(o) {
    $('hd').innerHTML = '<h1>Photos Around '+o.query.results.Response.CountryName+', '+o.query.results.Response.RegionName+', IP='+ o.query.results.Response.Ip +'</h1>';
    var lat = o.query.results.Response.Latitude,
        lng = o.query.results.Response.Longitude,
        yql = 'select * from flickr.photos.search(45) where woe_id in (select place.woeid from flickr.places where lat = "'+lat+'" and lon="'+lng+'")';
        new YQLQuery(yql,callback).fetch();         
}

function init() {
var yql = "use 'http://thinkphp.ro/apps/YQL/ip.location2.xml' as ip.location; select CountryName,RegionName,Latitude,Longitude,Ip from ip.location where ip='<?php echo$ip;?>'";
new YQLQuery(yql,receivedInfo).fetch();
}
init();
</script>
</body>
</html>