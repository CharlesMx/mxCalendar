<?php
if(!class_exists("geoLocator")){
    class geoLocator
    {
     protected $request;
     public $apikey,$glat,$glong,$address,$db,$canvas,$autofitmap;
     // Debug Varible to output/trouble shooting
     var $debug = true;
        public function __construct()
        {
            // Debug (output more info)
            $this->debug = false;
            // Google Maps API key
            $this->apikey = '';
            // Google Maps HOST (USER MAP API)
            $this->host = 'maps.google.com';
            // Google Maps Zoom Level
            $this->mapZoom = 15;
            // Google Maps Address to get geo-location for
            $this->address = '';
            if($this->apikey)
            $this->request = 'http://'.$this->host.'/maps/geo?q=%s&output=xml&key='.$this->apikey;
            else
            $this->request = 'http://'.$this->host.'/maps/geo?q=%s&output=xml';
            // The return value
            $this->output = '';
            // The map JS Output
            $this->mapJSv3 = '';
            // Map Location Var ID Counter
            $this->locID = 1;
            // Map Default CSS ID
            $this->canvas = 'map_canvas';
            // Dynamic resize of the map zoom to fit all map points
            $this->autofitmap = false;
        }
        
        public function getGEO($address = '')
        {
            if(empty($address)){
                //-- Couuld place Default address here 
                echo '<br />No address provided.<br />';
                exit;
            }
            else
                $this->address = $address;
                
                
            // Desired address
            $reqAddress= sprintf($this->request,$address);

                $xml = simplexml_load_file(urlencode($reqAddress)) or die("url not loading");
                $status = $xml->Response->Status->code;
                if($this->debug){
                    $this->output .= '<br />URL: '.$reqAddress.'<br />';
                    if($this->debug) var_dump($status);
                    $this->output .=  '<br />';
                }
                
                if (strcmp($status, "200") == 0) {
                  // Successful geocode
                  $geocode_pending = false;
                  $coordinates = $xml->Response->Placemark->Point->coordinates;
                  $coordinatesSplit = explode(",", $coordinates);
                  // Format: Longitude, Latitude
                  $lat = $coordinatesSplit[1];
                  $lng = $coordinatesSplit[0];
                  
                  $this->output .=  '<a href="#" onClick="animate('.$lat.', '.$lng.'); return false">Longitude: '.$lng.'  Latitude: '.$lat.'</a><br />';
                  
                  if(!strlen($this->mapJSv3)){
                    $this->mapJSv3 .= 'var bounds = new google.maps.LatLngBounds();'."\n";
                    $this->mapJSv3 .= '
                   var myLatLng =  new google.maps.LatLng('.$lat.', '.$lng.');
                    var myOptions = {'."\n";
                    $this->mapJSv3 .= 'zoom: '.$this->mapZoom.','."\n";
                    $this->mapJSv3 .= 'center: myLatLng,'."\n";
                    $this->mapJSv3 .= 'mapTypeId: google.maps.MapTypeId.ROADMAP'."\n";
                    $this->mapJSv3 .= '}'."\n";
                    $this->mapJSv3 .= 'map = new google.maps.Map(document.getElementById("'.$this->canvas.'"), myOptions);'."\n";
                    $this->mapJSv3 .= 'var marker = new google.maps.Marker({
                        position: myLatLng,
                        map: map,
                        title:"'.$address.'"});
                        '.($this->autofitmap ? 'bounds.extend(myLatLng);map.fitBounds(bounds);' : '').'
                        ';
    
                  }else{
                    $this->mapJSv3 .= '
                    var myLatLng = new google.maps.LatLng('.$lat.','.$lng.');
                    var marker = new google.maps.Marker({
                        position: myLatLng,
                        map: map,
                        title:"'.$address.'"});
                        bounds.extend(myLatLng);
                        map.fitBounds(bounds);
                    '."\n";
                    
                    $this->locID++;
                  }
                  
                } else if (strcmp($status, "620") == 0) {
                  // sent geocodes too fast
                  $this->output .=  "Too many... too fast...<br />\n";
                } else {
                  // failure to geocode
                  $geocode_pending = false;
                  $this->output .=  "Address " . $address . " failed geocoding.<br />\n";
                  $this->output .=  "Received status " . $status . "<br />\n";
                }
        }
    }
}
?>
