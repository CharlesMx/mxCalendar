<?php


class mxCalendars {
    
    public $modx;
    public $config = array();
    public $tz;
    public $loggingEnabled = 0;
    private $scriptProperties = array();
    private $dowMatch = array('Mon'=>1,'Tue'=>2,'Wed'=>3,'Thu'=>4,'Fri'=>5,'Sat'=>6,'Sun'=>7);
    public $debug = false;
    
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;
        
        $basePath = $this->modx->getOption('mxcalendars.core_path',$config,$this->modx->getOption('core_path').'components/mxcalendars/');
        $assetsUrl = $this->modx->getOption('mxcalendars.assets_url',$config,$this->modx->getOption('assets_url').'components/mxcalendars/');
        $descriptionEditorMode = $this->modx->getOption('mxcalendars.event_desc_type','htmleditor');
        $categoryRequired = $this->modx->getOption('mxcalendars.category_required','true');
        $this->loggingEnabled = $this->modx->getOption('mxcalendars.mgr_log_enable', 0);
        $this->config = array_merge(array(
            'basePath' => $basePath,
            'corePath' => $basePath,
            'modelPath' => $basePath.'model/',
            'processorsPath' => $basePath.'processors/',
            'chunksPath' => $basePath.'elements/chunks/',
            'jsUrl' => $assetsUrl.'js/',
            'cssUrl' => $assetsUrl.'css/',
            'assetsUrl' => $assetsUrl,
            'connectorUrl' => $assetsUrl.'connector.php',
            'category_required' => $categoryRequired,
            'event_desc_type' => $descriptionEditorMode,
            'mgr_dateformat' => $this->modx->getOption('mxcalendars.mgr_dateformat', '', 'm/d/Y'),
            'mgr_timeformat' => $this->modx->getOption('mxcalendars.mgr_timeformat', '', 'g:i a'),
            'isAdministrator' => $this->modx->user->isMember('Administrator'),
        ),$config);
        $this->modx->addPackage('mxcalendars',$this->config['modelPath']);
        $this->modx->getService('lexicon','modLexicon');
        $this->modx->lexicon->load('mxcalendars:default');
        
        
    }
    
	/*
	 * MANAGER: Initialize the manager view for calendar item management
	 */
	public function initialize($ctx = 'web') {
            
            $this->processFeeds();
            
            switch ($ctx) {
			case 'mgr':
                                $this->modx->lexicon->load('mxcalendars:default');
				if (!$this->modx->loadClass('mxcalendarControllerRequest',$this->config['modelPath'].'mxcalendars/request/',true,true)) {
				   return 'Could not load controller request handler. ['.$this->config['modelPath'].'mxcalendars/request/]';
				}
                                $this->request = new mxcalendarControllerRequest($this);
                                
				return $this->request->handleRequest();
			break;
		}
		return true;
	}
        
        public function setProperties($p=array()){
            $this->scriptProperties = $p;
        }
    
	/*
	 * GLOBAL HELPER FUNCTIONS: do what we can to making life easier
	 */ 
        public function loadChunk($name) {
		$chunk = null;
		if (!isset($this->chunks[$name])) {
			$chunk = $this->_getTplChunk($name);
			if (empty($chunk)) {
				$chunk = $this->modx->getObject('modChunk',array('name' => $name));
				if ($chunk == false) return false;
			}
			$this->chunks[$name] = $chunk->getContent();
		} else {
			$o = $this->chunks[$name];
			$chunk = $this->modx->newObject('modChunk');
			//$chunk->set('name', $name);
                        $chunk->setContent($o);
                        //$chunk->save();
		}
		$chunk->setCacheable(false);
                return $chunk;
	}
        //@TODO remove; not used
        public function parseChunk($name,$properties=array()){
		return $this->modx->getChunk($name,$properties);
        }
        
        public function getChunk($name,$properties = array()) {
		$chunk = null;
		if (!isset($this->chunks[$name])) {
			$chunk = $this->_getTplChunk($name);
			if (empty($chunk)) {
				$chunk = $this->modx->getObject('modChunk',array('name' => $name));
				if ($chunk == false) return false;
			}
			$this->chunks[$name] = $chunk->getContent();
		} else {
			$o = $this->chunks[$name];
			$chunk = $this->modx->newObject('modChunk');
			$chunk->setContent($o);
		}
		$chunk->setCacheable(false);
		return $chunk->process($properties);
	}
	 
	private function _getTplChunk($name,$postfix = '.chunk.tpl') {
		$chunk = false;
		$f = $this->config['chunksPath'].strtolower($name).$postfix;
		if (file_exists($f)) {
			$o = file_get_contents($f);
			$chunk = $this->modx->newObject('modChunk');
			$chunk->set('name',$name);
			$chunk->setContent($o);
		}
		return $chunk;
	}
        
        private function _getMap($address=null,$gmapRegion='',$width='500px',$height='500px', $gmapLib='http://maps.google.com/maps/api/js?sensor=false'){
            $googleMap = '';
            $gmapLocations = '';
            //-- Add google Map API
            if($address){
                    include_once('google_geoloc.class.inc');
                    //-- Output the Address results
                    if(class_exists("geoLocator") && $address){
                        //-- Split addresses for multiple points on the map
                        $addressList = explode('|', $address);

                        $mygeoloc = new geoLocator;
                        $mygeoloc->region = $gmapRegion;
                        //$mygeoloc->host = $this->config['GOOGLE_MAP_HOST'];
                        //$mygeoloc->apikey = $this->config['GOOGLE_MAP_KEY'];
                        //$mygeoloc->canvas = $this->config['mxcGoogleMapDisplayCanvasID'];
                        //$mygeoloc->autofitmap = (count($addressList) > 1 ? true : false);

                        foreach($addressList as $loc){
                            $mygeoloc->getGEO($loc);
                        }

                        $googleMap = '<div id="map_canvas" style="width:'.$width.'; height:'.$height.';"></div>';
                        $gmapLocations = $mygeoloc->mapJSv3;  
                    } else {
                        $googleMap = 'No class found.';
                    }
                    return $googleMap.'<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
                        <script type="text/javascript">
                                // -- mxCalendar :: location map -- //
                                function initialize() {
                                    //'.$gmapLocations.'
                                };

                                if (window.attachEvent) {window.attachEvent(\'onload\', initialize);}
                                else if (window.addEventListener) {window.addEventListener(\'load\', initialize, false);}
                                else {document.addEventListener(\'load\', initialize, false);}

                        </script>';
            }
        }
        
        public function addShadowBox($initialWidth,$initialHeight){
            $shadowPath = $this->config['assetsUrl'].'js/web/shadowbox/sa/';
            $this->modx->regClientHTMLBlock('<link rel="stylesheet" type="text/css" href="'.$shadowPath.'shadowbox.css">
                <script type="text/javascript" src="'.$shadowPath.'shadowbox.js"></script>
                <script type="text/javascript">
                var modalActive = true;
                Shadowbox.init({
                    skipSetup: true,
                }); 
                
                var sbOptions = {
                    modal: true,
                    '.(!empty($initialHeight)? 'initialHeight: '.$initialHeight.',height: '.$initialHeight.',' : '').'
                    '.(!empty($initialWidth) ? 'initialWidth: '.$initialWidth.',width: '.$initialWidth.',' : '').'
                };
                
                 window.onload = function() {
                    Shadowbox.setup(".mxcmodal", sbOptions);
                };
                </script>');
        }
        public function disableModal(){
            $this->modx->regClientHTMLBLock('<script>var modalActive = false;</script>');  
        }
        
        public function addLightBox(){
            $assetsPath = $this->config['assetsUrl'].'js/web/lightbox/';
            $this->modx->regClientHTMLBlock('<link rel="stylesheet" type="text/css" href="'.$assetsPath.'css/jquery.lightbox-0.5.css" media="screen" />
                    <script type="text/javascript" src="'.$assetsPath.'js/jquery.lightbox-0.5.min.js"></script>
                    <script type="text/javascript">
                    $(function() {
                            // Use this example, or...
                            //$("a[@rel*=lightbox]").lightBox(); // Select all links that contains lightbox in the attribute rel
                            // This, or...
                            $("a.mxcmodal").lightBox(); // Select all links with lightbox class

                    });
                    </script>');
        }
        
        
        /*
         * SNIPPET FUNCTIONS
         */
        public function setTimeZone($newTZ='UTC',$debug=false){
            if(date_default_timezone_get() != $newTZ) {
                $this->tz = date_default_timezone_get();
                date_default_timezone_set($newTZ);
                if($debug) echo 'mxCalendar: TIMEZONE CHANGED: changed timezone for duration of this extra from '.$this->tz.' '.date_default_timezone_get ().' ['.$newTZ.']<br />';
            } else {
                if($debug) echo "mxCalendar: TIMEZONE: No Change (".date_default_timezone_get().")<br />";
            }
        }
        public function restoreTimeZone($debug=false){
            if(!empty($this->tz)) { 
                date_default_timezone_set($this->tz);
                if($debug) echo "mxCalendar: TIMEZONE RESET: ".$this->tz."<br />";
            }
        }
        public function getFormatedDate($f,$t){
            $isDST = $this->timezoneDoesDST(date_default_timezone_get());
            
            if($isDST){
                $t = $t -3600;
            }
            return str_replace('%O', date('S', $t),strftime($f,$t));
        }
        public function timezoneDoesDST($tzId) {
            $tz = new DateTimeZone($tzId);
            $trans = $tz->getTransitions();
            return ((count($trans) && $trans[count($trans) - 1]['ts'] > time()));
        }
        public function custom_sort($a,$b){
            return $a['date']>$b['date'];
        }
        //-- Custom function to get somewhat valid duration; it's fuzzy and can be updated to be more accurate
        public function datediff($datefrom, $dateto, $using_timestamps = false)
        {
                /*
                 * Returns an array with:years, months,days,hours,minutes
                 */
                if (!$using_timestamps) {
                        $datefrom = strtotime($datefrom, 0);
                        $dateto = strtotime($dateto, 0);
                }
                $difference = $dateto - $datefrom; // Difference in seconds
                //-- Year check and adjustment
                if( floor($difference / 31536000) > 0){
                    $diff['years'] = floor($difference / 31536000);
                    $difference -= floor($difference / 31536000)*31536000;
                } else { $diff['years']=null; }
                //@TODO update this to a more accurate calculation (strftime('%y%m'))
                //-- Month check and adjustment
                if(floor($difference / 2678400) > 0){
                    $diff['months'] = floor($difference / 2678400);
                    $difference -=    floor($difference / 2678400)*2678400;
                } else { $diff['months']=null; }
                //-- Day check and adjustment
                if(floor($difference / ((60 * 60)*24)) > 0){
                    $diff['days'] = floor($difference / ((60 * 60)*24));
                    $difference -=  floor($difference / ((60 * 60)*24))*((60 * 60)*24);
                } else { $diff['days']=null; }
                //-- Hours check and adjustment
                if(floor($difference / (60 * 60)) > 0){
                    $diff['hours'] = floor($difference / (60 * 60));
                    $difference -=   floor($difference / (60 * 60))*(60 * 60);
                } else { $diff['hours']=null; }
                //-- Minutes check and adjustment
                if(floor($difference / 60) > 0){
                    $diff['minutes'] = floor($difference / 60);
                    $difference -=     floor($difference / 60)*60;
                } else { $diff['minutes']=null; }
                //-- Seconds, that should be all we have left
                $diff['seconds'] = $difference;
                
                return $diff;
        }
        public function makeEventDetail($events=array(),$occurance=0, $tpls=array(),$mapWidth,$mapHeight,$gmapRegion=''){
            $o = '';
            $tpls = (object)$tpls;
            if(count($events)){
                $occ=0;
                foreach($events AS $e){
                    $output_images = '';
                    
                    if($debug) $o .= 'Check: '.$occ.'<br />';
                    if($occ == $occurance || ($occurance == 0 && $occ ==0)){
                        $detailPH = $e[0];
                        $detailPH['allplaceholders'] = implode(', ',array_keys($e[0]));
                        if($e[0]['map']){
                            $detailPH['map'] = $this->_getMap($e[0]['location_address'],$gmapRegion,$mapWidth,$mapHeight);
                        }
                        
                        // Check for images
                        $images = $this->modx->getCollection('mxCalendarEventImages', array('event_id' => $e[0]['id'], 'active'=>1) );
                        if($images){
                            $imgIdx = 1;
                            foreach($images AS $image){
                                $detailPH['images_'.$imgIdx] = $output_images .= $this->getChunk($tpls->tplImage, $image->toArray() );
                                
                                $imgIdx++;
                            }
                            $detailPH['images'] = $output_images;
                            $detailPH['imagesTotal'] = $imgIdx;
                        } 
                        
                        $detailPH['datetest'] = date('Y-m-d h:i a', $e[0]['startdate']).' '.  date_default_timezone_get();
                        
                        $o .= $this->getChunk($tpls->tplDetail,$detailPH);
                            break;
                    }
                    $occ++;
                }
            } else { return 'No Details'; }
            return $o;
        }
        public function makeEventList($limit=5, $events=array(),$tpls=array(),$startDate=null,$endDate=null){
            $o = '';
            $tpls = (object)$tpls;
            $output_images='';
            if(count($events)){
                $preHead = '';
                $i=0;
                foreach($events AS $e){
                    //-- Now we need to loop all occurances on a single date
                    $rvar=0;
                    do {
                        if(strftime('%b',$e[$rvar]['startdate']) != $preHead && !empty($tpls->tplElMonthHeading)){
                            // Load list heading
                            if($preHead == '')
                                $e[$rvar]['altmonthheading'] = 'first';
                            $o.= $this->getChunk($tpls->tplElMonthHeading,$e[$rvar]);
                            $preHead = strftime('%b',$e[$rvar]['startdate']);
                        }
                        // check for images
                        $images = $this->modx->getCollection('mxCalendarEventImages', array('event_id' => $e[$rvar]['id'], 'active'=>1) );
                        if($images){
                            foreach($images AS $image){
                                $output_images .= $this->getChunk($tpls->tplImage, $image->toArray() );
                            }
                        } else {
                            //echo 'no images for '.$e[$rvar]['id'].'<br />';
                        }
                        
                        // Add the category css properties here as well
                        $categoryInlineCSS = array();

                        // Get the events category css info
                        if(!empty($e[$rvar]['categoryid'])){
                            $categories = explode(',', $e[$rvar]['categoryid']);
                            if(count($categories)){
                                foreach($categories AS $catid){
                                    $catQuery = $this->modx->newQuery('mxCalendarCategories');
                                    $catQuery->where(array('id:IN'=>$categories));
                                    $catproperties = $this->modx->getCollection('mxCalendarCategories',$catQuery);
                                    if($catproperties){
                                        foreach($catproperties AS $catCSS){
                                            $categoryInlineCSS['inlinecss'] .= $catCSS->get('inlinecss');
                                            $categoryInlineCSS['foregroundcss'] .= $catCSS->get('foregroundcss');
                                            $categoryInlineCSS['backgroundcss'] .= $catCSS->get('backgroundcss');
                                        }
                                    }
                                }
                            }
                        }
                        
                        $o .= $this->getChunk($tpls->tplElItem, array_merge($e[$rvar], array('images'=>$output_images), $categoryInlineCSS));
                        $i++;
                        $rvar++;
                        
                    } while ($rvar < count($e) && $i < $limit);
                    if($i >= $limit) break;
                }
            } else { return $this->getChunk($tpls->tplNoEvents, array('startdate'=>$startDate,'enddate'=>$endDate)); }
            return $this->getChunk($tpls->tplElWrap, array('startdate'=>$startDate,'enddate'=>$endDate,'eventList'=>$o));
        }
        public function getEventCalendarDateRange($activeMonthOnlyEvents=false){
            $startDate = $_REQUEST['dt'] ? $_REQUEST['dt'] : strftime('%Y-%m');
            $mStartDate = strftime('%Y-%m',strtotime($startDate)) . '-01 00:00:01';
            $nextMonth = strftime('%Y-%m', strtotime('+1 month',strtotime($mStartDate)));
            $prevMonth = strftime('%Y-%m', strtotime('-1 month',strtotime($mStartDate)));
            $startDOW = strftime('%u', strtotime($mStartDate));
            $lastDayOfMonth = strftime('%Y-%m',strtotime($mStartDate)) . '-'.date('t',strtotime($mStartDate)) .' 23:59:59';
            $startMonthCalDate = $startDOW <= 6 ? strtotime('- '.$startDOW.' day', strtotime($mStartDate)) : strtotime($mStartDate)	;
            $endMonthCalDate = strtotime('+ 6 weeks', $startMonthCalDate);
            if($debug) echo 'Active Month Only: '.$mStartDate.' :: '.$lastDayOfMonth.'  All displayed dates: '.strftime('%Y-%m-%d',$startMonthCalDate).' :: '.strftime('%Y-%m-%d',$endMonthCalDate).'<br />';
            if($activeMonthOnlyEvents) return array('start'=>strtotime($mStartDate), 'end'=>strtotime($lastDayOfMonth)); else return array('start'=>$startMonthCalDate, 'end'=>$endMonthCalDate);
        }
        public function makeEventCalendar($events=array(),$resourceId=null,$ajaxMonthResourceId=null,$tpls=array('event'=>'month.inner.container.row.day.eventclean','day'=>'month.inner.container.row.day','week'=>'month.inner.container.row','month'=>'month.inner.container','heading'=>'month.inner.container.row.heading'), $conFilter=null, $calFilter=null, $highlightToday=true){
            $startDate = $_REQUEST['dt'] ? $_REQUEST['dt'] : strftime('%Y-%m-%d');
            $mStartDate = strftime('%Y-%m',strtotime($startDate)) . '-01 00:00:01';
            $mCurMonth = strftime('%m', strtotime($mStartDate));
            $nextMonth = strftime('%Y-%m', strtotime('+1 month',strtotime($mStartDate)));
            $prevMonth = strftime('%Y-%m', strtotime('-1 month',strtotime($mStartDate)));
            $startDOW = strftime('%u', strtotime($mStartDate));
            $lastDayOfMonth = strftime('%Y-%m',strtotime($mStartDate)) . '-'.date('t',strtotime($mStartDate)) .' 23:59:59';
            $endDOW = strftime('%u', strtotime($lastDayOfMonth));
            $tpls=(object)$tpls;
            $out = '';
            $startMonthCalDate = $startDOW <= 6 ? strtotime('- '.$startDOW.' day', strtotime($mStartDate)) : strtotime($mStartDate)	;
            $endMonthCalDate = strtotime('+ '.(6 - $endDOW).' day', strtotime($lastDayOfMonth));
            //------//
            $headingLabel = strtotime($mStartDate);
            $globalParams = array('conf'=>$conFilter, 'calf'=>$calFilter);
            $todayLink = $this->modx->makeUrl($ajaxMonthResourceId,'', array_merge($globalParams, array('dt' => strftime('%Y-%m'), 'cid'=>$_REQUEST['cid'])));
            $prevLink = $this->modx->makeUrl($ajaxMonthResourceId,'', array_merge($globalParams, array('dt' => $prevMonth, 'cid'=>$_REQUEST['cid'])));
            $nextLink = $this->modx->makeUrl($ajaxMonthResourceId,'', array_merge($globalParams, array('dt' => $nextMonth, 'cid'=>$_REQUEST['cid'])));
            
            $chunkEvent = $this->loadChunk($tpls->event);
            $chunkDay = $this->loadChunk($tpls->day);
            $chunkWeek = $this->loadChunk($tpls->week);
            $chunkMonth = $this->loadChunk($tpls->month);
            
            $heading = '';
            for($i=0;$i<7;$i++){
                    if($this->debug) echo '&nbsp;&nbsp;'.strftime('%A', strtotime('+ '.$i.' day', $startMonthCalDate)).'<br />';
                    $thisDOW = trim('mxcalendars.label_'.strtolower(strftime('%A', strtotime('+ '.$i.' day', $startMonthCalDate))));
                    $heading.=$this->getChunk($tpls->heading, array('dayOfWeekId'=>'','dayOfWeekClass'=>'mxcdow', 'dayOfWeek'=> $this->modx->lexicon($thisDOW) ));
            }
            //-- Set additional day placeholders for week
            $phHeading = array(
                'weekId'=>''
                ,'weekClass'=>''
                ,'days'=>$heading 
                );
            //$weeks.=$chunkWeek->process($phWeek);
            $heading=$this->getChunk($tpls->week, $phHeading);

            $weeks = '';
            //-- Start the Date loop
            $var=0;
            do {
                if($this->debug) echo '---------------<br />';
                if($this->debug) echo 'Week '.($var + 1).'<br />';
                if($this->debug) echo '---------------<br />';
                // Week Start date
                $iWeek = strtotime('+ '.$var.' week', $startMonthCalDate);
                $diw = 0;
                $days = '';
                do{
                    // Get the week's days
                    $iDay = strtotime('+ '.$diw.' day', $iWeek);
                    $thisMonth = strftime('%m', $iDay);
                    if($this->debug) echo strftime('%a %b %d', $iDay).'<br />';
                    $eventList = '';
                    if(isset($events[strftime('%Y-%m-%d', $iDay)]) && count($events[strftime('%Y-%m-%d', $iDay)])){
                        //-- Echo each event item
                        $e = $events[strftime('%Y-%m-%d', $iDay)];
                        
                        foreach($e AS $el){
                            if($this->debug) echo '&nbsp;&nbsp;<span style="color:green;">++</span>&nbsp;&nbsp;'.$el['title'].'<br />';
                            //$eventList.=$chunkEvent->process($el);
                            //@TODO -- FIX: Add check for display of current month
                            
                            $categoryInlineCSS = array();
                            
                            // Get the events category css info
                            if(!empty($el['categoryid'])){
                                $categories = explode(',', $el['categoryid']);
                                if(count($categories)){
                                    foreach($categories AS $catid){
                                        $catQuery = $this->modx->newQuery('mxCalendarCategories');
                                        $catQuery->where(array('id:IN'=>$categories));
                                        $catproperties = $this->modx->getCollection('mxCalendarCategories',$catQuery);
                                        if($catproperties){
                                            foreach($catproperties AS $catCSS){
                                                $categoryInlineCSS['inlinecss'] .= $catCSS->get('inlinecss');
                                                $categoryInlineCSS['foregroundcss'] .= $catCSS->get('foregroundcss');
                                                $categoryInlineCSS['backgroundcss'] .= $catCSS->get('backgroundcss');
                                            }
                                        }
                                    }
                                }
                            }
                            $eventList.=$this->getChunk($tpls->event, array_merge($el,$categoryInlineCSS));
                        }
                    } else { if($this->debug) echo '&nbsp;&nbsp;<span style="color:red;">--&nbsp;&nbsp;'.strftime('%m-%d', $iDay).'</span><br />'; }
                    //-- Set additional day placeholders for day
                    $isToday = (strftime('%m-%d') == strftime('%m-%d', $iDay) && $highlightToday==true ? 'today ' : '');
                    $dayMonthName = strftime('%b',$iDay);
                    $dayMonthDay =  strftime('%d',$iDay);
                    $dayMonthDay = (strftime('%d',$iDay) == 1 ? strftime('%b ',$iDay).( substr($dayMonthDay,0,1) == '0' ? ' '.substr($dayMonthDay,1) : $dayMonthDay ) : ( substr($dayMonthDay,0,1) == '0' ? ' '.substr($dayMonthDay,1) : $dayMonthDay ));
                    $phDay = array(
                        //'dayOfMonth'=> str_replace('0', ' ', (strftime('%d',$iDay) == 1 ? strftime('%b %d',$iDay) : strftime('%d',$iDay)))
                        'dayOfMonth' => $dayMonthDay
                        ,'dayOfMonthID'=>'dom-'.strftime('%A%d',$iDay)
                        ,'events'=>$eventList 
                        ,'fulldate'=>strftime('%m/%d/%Y', $iDay)
                        ,'tomorrow'=>strftime('%m/%d/%Y', strtotime('+1 day',  $iDay ))
                        ,'yesterday'=>strftime('%m/%d/%Y', strtotime('-1 day', $iDay ))
                        ,'class'=>($mCurMonth == $thisMonth ? $isToday.(!empty($eventList) ? 'hasEvents' : 'noEvents') : 'ncm')
                        );
                    //$days.=$chunkDay->process($phDay);
                    $days.=$this->getChunk($tpls->day, $phDay);
                } while (++$diw < 7);
                if($this->debug) echo '<br />';
                //-- Set additional day placeholders for week
                $phWeek = array(
                    'weekId'=>'mxcWeek'.$var
                    ,'weekClass'=>strftime('%A%d',$iDay)
                    ,'days'=>$days 
                    );
                //$weeks.=$chunkWeek->process($phWeek);
                $weeks.=$this->getChunk($tpls->week, $phWeek);
            } while (++$var < 6); //Only advance 5 weeks giving total of 6 weeks
            //-- Set additional day placeholders for month
            $phMonth = array(
                'containerID'=>strftime('%a',$iDay)
                ,'containerClass'=>strftime('%a%Y',$iDay)
                ,'weeks'=>$heading.$weeks 
                ,'headingLabel'=>$headingLabel
                ,'todayLink'=>$todayLink
                ,'todayLabel'=> $this->modx->lexicon('mxcalendars.label_today')
                ,'prevLink'=>$prevLink
                ,'nextLink'=>$nextLink
                );
            //return $chunkMonth->process($phMonth);
            return $this->getChunk($tpls->month, $phMonth);
        }
        
        public function makeCategoryList($labelCategory=null,$filteredCategoryId=null,$resourceId=null,$tpls=array()){
            $output = '';
            $tpls = (object)$tpls;
            // build category query
            $c = $this->modx->newQuery('mxCalendarCategories');
            $c->where(array(
                    //'name:LIKE' => '%'.$query.'%',
                    'disable' => 0,
                    'active' => 1,
            ));
            $c->sortby('name','ASC');
            $mxcalendarsCats = $this->modx->getCollection('mxCalendarCategories', $c);
            // iterate
            // $list = array();
            // $output .= '<ul><li class="'.(!$filteredCategoryId ? 'mxcactivecat' : '').'"><a href="'.$this->modx->makeUrl($resourceId,'','' ).'">View All</a></li>';
            $name = $this->modx->lexicon('mxcalendars.label_category_viewAll');
            $catClass = (!$filteredCategoryId ? 'mxcactivecat' : '');
            $link = $this->modx->makeUrl($resourceId,'','');
            $output .= $this->getChunk($tpls->tplCategoryItem, array('class'=> $catClass,'link'=>$link, 'name'=>$name) );
            foreach ($mxcalendarsCats as $mxc) {
                // $list[] = $mxc->toArray();
                $id = $mxc->get('id');;
                $vals = $mxc->toArray();
                $vals['link'] = $this->modx->makeUrl($resourceId,'',array('cid' => $id ) );
                $vals['class'] = ($filteredCategoryId == $id ? 'mxcactivecat' : '');
                $output .= $this->getChunk($tpls->tplCategoryItem, $vals );
                // $output .= '<li class="'.($filteredCategoryId == $id ? 'mxcactivecat' : '').'"><a href="'.$catURL.'">'.$mxc->get('name').'</a></li>';
            }
            // $output .= json_encode($list);
            return $this->getChunk($tpls->tplCategoryWrap, array('heading'=>$labelCategory, 'categories'=>$output ));
            //return $output.'</ul>';
        }
        
        public function processFeeds($setFeedTZ=null){

            require_once dirname(__FILE__).'/mxcalendars.ics.class.php';
            
            $f = $this->modx->newQuery('mxCalendarFeed');
            $f->where(  array('active:=' => 1,'nextrunon:<=' => time()) );
            $f->prepare();
   
            $mxcfeeds = $this->modx->getCollection('mxCalendarFeed', $f);
            
            if($this->loggingEnabled){
                $this->logEvent('feed','feeds processor called\n\nSQL:\n'.$f->toSql());
            }
            
            if(!empty($setFeedTZ) && is_array(json_decode($setFeedTZ, true))){
                $feedTzSettings = json_decode($setFeedTZ, true);
            } else {
                $feedTzSettings = null;
            }
            
            //$this->modx->setLogLevel(modX::LOG_LEVEL_INFO);
            foreach($mxcfeeds AS $feed){
                $hadmodifications = 0;
                if($feed->get('type') == 'ical'){
                    
                    $activeUrl = $feed->get('feed');

                    $myics = file_get_contents($activeUrl);

                    // Cache the response for giggles
                    //$this->modx->cacheManager->set('mxcfeed-'.$feed->get('id'),$myics,3600);

                    //echo '<h2>CURRENT TIMEZONE: '.date_default_timezone_get().'</h2><br />';
                    $currentTZ = date_default_timezone_get();
                    
                    if(!empty($feedTzSettings) && array_key_exists($feed->get('id'), $feedTzSettings)){
                        //echo '<h2>CURRENT TIMEZONE: '.date_default_timezone_get().'</h2><br />';
                        date_default_timezone_set($feedTzSettings[$feed->get('id')]);
                        //echo '<h2>NEW TIMEZONE: '.date_default_timezone_get().'</h2><br />';
                    }
                    
                    $config    = array( "unique_id" => 'mxcfeed-'.$feed->get('id').'-'.time(),
                                        "url"       => $activeUrl,
                                    );
                    $vcalendar = new vcalendar( $config );
                    $vcalendar->parse();

                    
                    //echo '<pre>'.print_r($vcalendar,true).'</pre>';
                    //echo '<br /><br />=========================================================<br /><br />';
                    
                    //$this->modx->setLogLevel(modX::LOG_LEVEL_INFO);
                    //$this->modx->log(modX::LOG_LEVEL_INFO,'Parsing feed #'.$feed->get('id').' events. ['.$feed->get('feed').']\n\nResponse:\n'.$myics);
                    
                    if($this->loggingEnabled) $this->logEvent('feed parse','Parsing feed #'.$feed->get('id').' events. ['.$feed->get('feed').']\n\nResponse:\n'.$myics);

                    while( $vevent = $vcalendar->getComponent( "vevent" )) {
                        
                        if(!empty($feedTzSettings) && array_key_exists($feed->get('id'), $feedTzSettings)){
                            //echo '<h2>CURRENT TIMEZONE: '.date_default_timezone_get().'</h2><br />';
                            //date_default_timezone_set($feedTzSettings[$feed->get('id')]);
                            //echo '<h2>NEW TIMEZONE: '.date_default_timezone_get().'</h2><br />';
                        }
                        
                        if($vevent->dtstart['value']){
                        $start     =   strtotime(
                                        implode('-',array($vevent->dtstart['value']['year'],$vevent->dtstart['value']['month'],$vevent->dtstart['value']['day']))
                                        .'T'.
                                        implode(':',array($vevent->dtstart['value']['hour'],$vevent->dtstart['value']['min'],$vevent->dtstart['value']['sec']))
                                        . $vevent->dtstart['value']['tz']
                                        );// 2013-03-18T11:19:28-04:00  $this->getFormatedDate(null,,true);

                        /*
                                        mktime(
                                                $vevent->dtstart['value']['hour'],
                                                $vevent->dtstart['value']['min'],
                                                $vevent->dtstart['value']['sec'],
                                                $vevent->dtstart['value']['month'],
                                                $vevent->dtstart['value']['day'],
                                                $vevent->dtstart['value']['year']
                                                ,0
                                        );      // one occurrence
                        */
                        } else { $start=''; }
                        //echo '<br />NY: '.date('Y-m-d h:i a', $start).' ==> '.$start;
                        
                        if($vevent->dtend['value']){
                        $end =strtotime(
                                        implode('-',array($vevent->dtend['value']['year'],$vevent->dtend['value']['month'],$vevent->dtend['value']['day']))
                                        .'T'.
                                        implode(':',array($vevent->dtend['value']['hour'],$vevent->dtend['value']['min'],$vevent->dtend['value']['sec']))
                                        . $vevent->dtend['value']['tz']
                                        );
                        /*
                                    mktime(
                                                $vevent->dtend['value']['hour'],
                                                $vevent->dtend['value']['min'],
                                                $vevent->dtend['value']['sec'],
                                                $vevent->dtend['value']['month'],
                                                $vevent->dtend['value']['day'],
                                                $vevent->dtend['value']['year']
                                                ,0
                                        );
                         */
                        } else { $end = ''; }
                        
                        if($vevent->lastmodified['value']){
                            $lastchange = mktime(
                                                $vevent->lastmodified['value']['hour'],
                                                $vevent->lastmodified['value']['min'],
                                                $vevent->lastmodified['value']['sec'],
                                                $vevent->lastmodified['value']['month'],
                                                $vevent->lastmodified['value']['day'],
                                                $vevent->lastmodified['value']['year']
                                                ,0
                                        );
                        } else {$lastchange = ''; }
                        
                        if($vevent->created['value']){
                            $createdDate = mktime(
                                                $vevent->created['value']['hour'],
                                                $vevent->created['value']['min'],
                                                $vevent->created['value']['sec'],
                                                $vevent->created['value']['month'],
                                                $vevent->created['value']['day'],
                                                $vevent->created['value']['year']
                                                ,0
                                        );
                        } else { $createdDate = ''; }
                        
                        $description = str_replace(array("\r\n", "\n", "\r","\\r\\n","\\n","\\r"), '<br />', $vevent->getProperty( "description" ));  // one occurrence
                        $location = $vevent->getProperty( "location" );
                        $title = $vevent->getProperty( "summary" );
                        $feedEventUID = $vevent->getProperty("uid");

                        //-- Multiple Occurances
                        //while( $comment = $vevent->getProperty( "comment" )) { // MAY occur more than once
                        //   echo json_encode($comment).'<br /><hr /><br />';
                        //}

                        // Output for testing
                        $event = array(
                                    'title'=>$title,
                                    'description'=>(!empty($description) ?  $description : ''),
                                    'location_name'=>$location,
                                    'startdate'=>$start,
                                    'enddate'=>$end,
                                    'source'=>'feed',
                                    'lastedit'=>$lastchange,
                                    'feeds_id'=>$feed->get('id'),
                                    'feeds_uid'=>$feedEventUID,
                                    'context'=>'',
                                    //'categoryid'=>$feed->get('defaultcategoryid'),
                                    'createdon'=>$createDate,
                                    'repeattype'=>0,
                                    'repeaton'=>'',
                                    'repeatfrequency'=>0
                                    );
                        //echo 'Title: '.$title.'<br />'.json_encode($event).'<br /><hr><br /><br />';
                        
                        //-- Save the new event
                        if(!empty($feedEventUID)){
                            $existingEvent = $this->modx->getObject('mxCalendarEvents',array('feeds_uid' => $feedEventUID));
                            //if(!is_object($existingEvent)){
                            //    $existingEvent = $this->modx->getObject('mxCalendarEvents',array('title' => $title));
                            //}
                        } //else {
                            // Disable the TITLE as a valid itdentifier for duplicated events as it breaks the repeating events
                            // $existingEvent = $this->modx->getObject('mxCalendarEvents',array('title' => $title));
                        //}
                        if(is_object($existingEvent)){
                            // Check and modify existing event if modified since last update
                            if($existingEvent->get('lastedit') <= $lastchange){
                                // Event has been updated so lets just update all properties
                                $existingEvent->fromArray($event);
                                $existingEvent->save();
                                if($this->loggingEnabled){
                                    $this->logEvent('feed','Update Event ('.$existingEvent->get('id').') for feed #'.$feed->get('id').'\n\nEvent JSON:\n'.json_encode($event));
                                }
                                $hadmodifications++;
                            }
                        } else {
                            // Create the newly found event from the feed
                            $feedEvent = $this->modx->newObject('mxCalendarEvents');
                            
                            $event['categoryid']=$feed->get('defaultcategoryid');
                            
                            $feedEvent->fromArray($event);
                            $feedEvent->save();
                            if($this->loggingEnabled){
                                $this->logEvent('feed','New Event ('.$feedEvent->get('id').') for feed #'.$feed->get('id').'\n\nEvent JSON:\n'.json_encode($event));
                            }
                            $hadmodifications++;
                        }
                       
                        
                    }
                    
                    // Set back current TIME ZONE
                    date_default_timezone_set($currentTZ);
                    
                    //-- Update the feed next run time
                    $nextTime = strtotime('+'.$feed->get('timerint').' '.$feed->get('timermeasurement'));
                    $feed->set('lastrunon',time());
                    $feed->set('nextrunon',$nextTime);
                    $feed->save();

                    if($hadmodifications){
                        $this->logEvent('feed','Parsing feed #'.$feed->get('id').' had <strong>'.$hadmodifications.'</strong> event'.($hadmodifications > 1 ? 's' : '').' added/updated ['.$feed->get('feed').']');
                    } else {
                        $this->logEvent('feed','Parsing feed #'.$feed->get('id').' had no changes. ['.$feed->get('feed').']');
                    }
                    
                } else {
                    //-- ==================== --//
                    //-- Process the XML feed --//
                    //-- ==================== --//
                    $activeUrl = $feed->get('feed');
                    $xmlEvents = file_get_contents($activeUrl);
                    $events = new SimpleXMLElement($xmlEvents);
                    $idx = 0;
                    foreach ($events->event as $event) {
                        if(strtolower($event->timebegin) !== 'all day'){
                            $startDateTime = strtotime($event->date.' '.$event->timebegin); 
                            $endDateTime = strtotime($event->date.' '.str_replace('- ', '', $event->timeend)); 
                        } else {
                            $startDateTime = strtotime($event->date.' 00:00:00'); 
                            $endDateTime = strtotime($event->date.' 23:59:59');
                        }
                        $lastchange = (!empty($event->lastedit) ? $event->lastedit : time());
                        // Output for testing
                        $eventdata = array(
                                    'title'=>$event->title,
                                    'description'=>(!empty($event->description) ? $event->description : ''),
                                    'location_name'=>(!empty($event->location) ? $event->location : ''),
                                    'startdate'=>$startDateTime,
                                    'enddate'=>$endDateTime,
                                    'source'=>'feed',
                                    'lastedit'=>$lastchange,
                                    'feeds_id'=>$feed->get('id'),
                                    'feeds_uid'=> (!empty($event->eventid) ? $event->eventid : ''),
                                    'context'=>'',
                                    'categoryid'=>$feed->get('defaultcategoryid'),
                                    'createdon'=>(!empty($event->createDate) ? $event->createDate : time()),
                                    'repeattype'=>0,
                                    'repeaton'=>'',
                                    'repeatfrequency'=>0
                                    );
                        //-- Save the new event
                        if(!empty($event->eventid) && isset($event->eventid) ){
                                $q = $this->modx->newQuery('mxCalendarEvents');
                                $title = (string)$event->title;
                                $feeduid = (string)$event->eventid;
                                $q->where(array(
                                    'mxCalendarEvents.title'        => $title,
                                    'mxCalendarEvents.feeds_id'     => $feed->get('id'),
                                    'mxCalendarEvents.feeds_uid'    => $feeduid
                                ));
                                $q->prepare();
                                //echo 'SQL ['.$event->title.' '.$event->eventid.']: <br />'.$q->toSQL().'<br /><br />';
                                $existingEvent = $this->modx->getObject('mxCalendarEvents',$q);
                            //$existingEvent = $this->modx->getObject('mxCalendarEvents',array());
                        } else {
                            $existingEvent = false;
                        }
                        if(is_object($existingEvent)){
                            // Check and modify existing event if modified since last update
                            if($existingEvent->get('lastedit') <= $lastchange){
                                // Event has been updated so lets just update all properties
                                $existingEvent->fromArray($eventdata);
                                $existingEvent->save();
                                if($this->loggingEnabled){
                                    $this->logEvent('feed','Update Event ('.$existingEvent->get('id').')['.$event->eventid.'] for feed #'.$feed->get('id').'\n\nEvent JSON:\n'.json_encode($event));
                                }
                                $hadmodifications++;
                            }
                        } else {
                            // Create the newly found event from the feed
                            $feedEvent = $this->modx->newObject('mxCalendarEvents');
                            $feedEvent->fromArray($eventdata);
                            $feedEvent->save();
                            if($this->loggingEnabled){
                                $this->logEvent('feed','New Event ('.$feedEvent->get('id').') for feed #'.$feed->get('id').'\n\nEvent JSON:\n'.json_encode($event));
                            }
                            $hadmodifications++;
                        }
                        //unset($event);
                        $idx++;
                    }
                    //-- Update the feed next run time
                    $nextTime = strtotime('+'.$feed->get('timerint').' '.$feed->get('timermeasurement'));
                    $feed->set('lastrunon',time());
                    $feed->set('nextrunon',$nextTime);
                    $feed->save();

                    if($hadmodifications){
                        $this->logEvent('feed','Parsing feed #'.$feed->get('id').' had <strong>'.$hadmodifications.'</strong> event'.($hadmodifications > 1 ? 's' : '').' added/updated ['.$feed->get('feed').']');
                    } else {
                        $this->logEvent('feed','Parsing feed #'.$feed->get('id').' had no changes. ['.$feed->get('feed').']');
                    }
                    
                }
            }
            
            
        }
        
        public function logEvent($itemType='',$log=''){
                $mxclog = $this->modx->newObject('mxCalendarLog',array(
                'itemtype' => $itemType,
                'log' => $log,  
                'datetime' => time(),
                ));
                $mxclog->save();
        }
}