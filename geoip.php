<?php
  $ip = trim($_SERVER['REMOTE_ADDR']);
  if($ip === '127.0.0.1') { $ip = '132.97.88.77';$ip = '87.221.93.177';}
     $root = 'http://query.yahooapis.com/v1/public/yql?q=';
     $yql = "use 'http://thinkphp.ro/apps/YQL/ip.location2.xml' as ip.location; select * from ip.location where ip='".$ip."'";
     $url = $root . urlencode($yql). '&diagnostics=false&format=json';
     $content = get($url);
     $json = json_decode($content);
     $json = $json->query->results->Response;
     $lat = $json->Latitude;
     $lng = $json->Longitude;
     $code = $json->CountryCode;
     $city = $json->City;      
     $country = $json->CountryName;
     $region = $json->RegionName;
     $ip = $json->Ip;
     $info = 'Photos Around '. $country. ', '. $region. ', IP: '. $ip;
     $table = '<table><tbody>'.
              '<tr><td class="head">Country Code:</td><td>'.$code.'</td>'.
              '</tr><tr><td class="head">City:</td><td>'.$city.'</td></tr>'.
              '</tr><tr><td class="head">Country Name:</td><td>'.$country.'</td></tr>'.
              '</tr><tr><td class="head">Region Name:</td><td>'.$region.'</td></tr>'.
              '</tr><tr><td class="head">Latitude:</td><td>'.$lat.'</td></tr>'.
              '</tr><tr><td class="head">Longitude:</td><td>'.$lng.'</td></tr>'.
              '</tr><tr><td class="head">Ip:</td><td>'.$ip.'</td></tr>'.
              '</tbody></table>';
     $yqlphotos = 'select * from flickr.photos.search(20) where woe_id in (select place.woeid from flickr.places where lat = "'.$lat.'" and lon="'.$lng.'")';
     $data = json_decode(get($root . urlencode($yqlphotos). '&diagnostics=false&format=json'));
     $photos = build_photos($data->query->results->photo);
     $output = json_encode(array('lat'=>$lat,'lng'=>$lng,'table'=>$table));
function get($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $output = curl_exec($ch);
  curl_close($ch);
  return $output;
}//end function get
function build_photos($photos) {
   $output = '<ul>';
     if(count($photos) > 0) {
        foreach($photos as $photo) {
           $output .="<li><a title='{$photo->title}' href='http://www.flickr.com/photos/{$photo->owner}/{$photo->id}' target='_blank'><img src='http://farm{$photo->farm}.static.flickr.com/{$photo->server}/{$photo->id}_{$photo->secret}.jpg' alt='{$photo->title}' width='75' height='75'/></a></li>";
        }
     } else {
       $output .= '</li>No Photos found.</li>';
     }
       $output .= '</ul>';
  return $output;
}//end function build_photos
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Photos Around You</title>
<link rel="stylesheet" href="http://yui.yahooapis.com/2.8.0r4/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css">
<link rel="stylesheet" href="http://yui.yahooapis.com/2.7.0/build/base/base.css" type="text/css">
<style type="text/css">
html,body{font-family: georgia,verdana,sans-serif;}
#map{width: 500px;height:325px;border: 1px solid #ccc}
ul {margin-top:0px;padding:0;}
ul li{float:left;padding:1px;border:1px solid #999;list-style:none;}
table tr td{border: none}
.head {font-weight: bold}
html,body{background: #69c}
#bd{background: #fff;border: 5px solid #fff;}
h1{font-size: 32px;color: #fff}
#ft{font-size:100%;color:#111;text-align:left;margin-top:3em;font-family: georgia,arial,verdana,sans-serif}
#ft p a{color:#000;}
</style>
</head>
<body>
<div id="doc2" class="yui-t7">
   <div id="hd" role="banner"><h1><?php if(!empty($info)) {echo$info;} else echo'Trying to determine your location...';?></h1></div>
   <div id="bd" role="main">
	<div class="yui-g">
    <div class="yui-u first">
    <div id="map"></div> 
    </div>
    <div class="yui-u" id="photos">	
    <?php if(!empty($photos)) {echo$photos;} ?>
   </div>
</div>
</div>
<div id="ft" role="contentinfo"><p>Created by @<a href="http://twitter.com/thinkphp">thinkphp</a> using <a href="http://developer.yahoo.com/yql/console/#h=use%20%27http%3A//thinkphp.ro/apps/YQL/ip.location2.xml%27%20as%20ip.location%3B%20select%20*%20from%20ip.location%20where%20ip%3D%22<?php echo$ip;?>%22">YQL</a> and <a href="http://thinkphp.ro/apps/YQL/ip.location2.xml">ip.location</a> and <a href="http://code.google.com/apis/maps/documentation/javascript/reference.html">Google Maps</a></p></div>
</div>
<script src="http://maps.google.com/maps/api/js?sensor=true&amp;v=3" type="text/javascript"></script>
<script type="text/javascript">
var map;
function placeonmap(resp){
    var o = resp[0];
    var myLatlng = new google.maps.LatLng(o.lat, o.lng);
    var myOptions = {
      zoom: 10,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.TERRAIN
    }
   map = new google.maps.Map(document.getElementById("map"), myOptions);
   var marker = new google.maps.Marker({
          map: map,
          position: new google.maps.LatLng(o.lat, o.lng),
          draggable: false,
          title: 'Click Me!'
        });
   var contentString = o.table;
   var infowindow =  new google.maps.InfoWindow({
       content: contentString  
   });
   google.maps.event.addListener(marker, 'click', function(e){
          infowindow.open(map, marker);
   });
}
</script>
<script type="text/javascript">placeonmap([<?php echo $output;?>])</script>
</body>
</html>