<?php


class mxCalendars {
    
    public $modx;
    public $config = array();
    public $tz;
    private $scriptProperties = array();
    private $dowMatch = array('Mon'=>1,'Tue'=>2,'Wed'=>3,'Thu'=>4,'Fri'=>5,'Sat'=>6,'Sun'=>7);
    
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;
        
        $basePath = $this->modx->getOption('mxcalendars.core_path',$config,$this->modx->getOption('core_path').'components/mxcalendars/');
        $assetsUrl = $this->modx->getOption('mxcalendars.assets_url',$config,$this->modx->getOption('assets_url').'components/mxcalendars/');
        $descriptionEditorMode = $this->modx->getOption('mxcalendars.event_desc_type','htmleditor');
        $categoryRequired = $this->modx->getOption('mxcalendars.category_required','true');
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
            'event_desc_type' => $descriptionEditorMode            
        ),$config);
        $this->modx->addPackage('mxcalendars',$this->config['modelPath']);
        $this->modx->getService('lexicon','modLexicon');
        $this->modx->lexicon->load('mxcalendars:default');
    }
    
	/*
	 * MANAGER: Initialize the manager view for calendar item management
	 */
	public function initialize($ctx = 'web') {
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
        
        private function _getMap($address=null, $gmapLib='http://maps.google.com/maps/api/js?sensor=false'){
            $googleMap = '';
            //-- Add google Map API
            if($address){
                    include_once('google_geoloc.class.inc');
                    //-- Output the Address results
                    if(class_exists("geoLocator") && $address){
                        //-- Split addresses for multiple points on the map
                        $addressList = explode('|', $address);

                        $mygeoloc = new geoLocator;
                        //$mygeoloc->host = $this->config['GOOGLE_MAP_HOST'];
                        //$mygeoloc->apikey = $this->config['GOOGLE_MAP_KEY'];
                        //$mygeoloc->canvas = $this->config['mxcGoogleMapDisplayCanvasID'];
                        //$mygeoloc->autofitmap = (count($addressList) > 1 ? true : false);

                        foreach($addressList as $loc){
                            $mygeoloc->getGEO($loc);
                        }

                        $googleMap = '<div id="map_canvas" style="width:500px; height:500px;"></div>';
                          
                    } else {
                        $googleMap = 'No class found.';
                    }
                    return $googleMap.'<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
                        <script type="text/javascript">
                                // -- mxCalendar :: location map -- //
                                function initialize() {
                                  '.$mygeoloc->mapJSv3.'
                                };

                                window.onload = initialize;

                        </script>';
            }
        }
        
        public function addShadowBox(){
            $shadowPath = $this->config['assetsUrl'].'js/web/shadowbox/sa/';
            $this->modx->regClientHTMLBlock('<link rel="stylesheet" type="text/css" href="'.$shadowPath.'shadowbox.css">
                <script type="text/javascript" src="'.$shadowPath.'shadowbox.js"></script>
                <script type="text/javascript">
                var modalActive = true;
                Shadowbox.init({
                    skipSetup: true
                });  
                 window.onload = function() {
                    Shadowbox.setup(".mxcmodal", {
                        modal: true,
                    });
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
            return str_replace('%O', date('S', $t),strftime($f,$t));
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
        public function makeEventDetail($events=array(),$occurance=0, $tpls=array()){
            $o = '';
            $tpls = (object)$tpls;
            if(count($events)){
                $occ=0;
                foreach($events AS $e){
                    if($debug) $o .= 'Check: '.$occ.'<br />';
                    if($occ == $occurance || ($occurance == 0 && $occ ==0)){
                        $detailPH = $e[0];
                        $detailPH['allplaceholders'] = implode(', ',array_keys($e[0]));
                        if($e[0]['map']){
                            $detailPH['map'] = $this->_getMap($e[0]['location_address']);
                        }
                        $o .= $this->getChunk($tpls->tplDetail,$detailPH);
                            break;
                    }
                    $occ++;
                }
            } else { return 'No Details'; }
            return $o;
        }
        public function makeEventList($limit=5, $events=array(),$tpls=array()){
            $o = '';
            $tpls = (object)$tpls;
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
                        $o .= $this->getChunk($tpls->tplElItem,$e[$rvar]);
                        $i++;
                        $rvar++;
                        
                    } while ($rvar < count($e) && $i < $limit);
                    if($i >= $limit) break;
                }
            } else { return 'No Events'; }
            return $this->getChunk($tpls->tplElWrap, array('eventList'=>$o));
        }
        public function getEventCalendarDateRange($activeMonthOnlyEvents=false){
            $startDate = $_REQUEST['dt'] ? $_REQUEST['dt'] : strftime('%Y-%m');
            $mStartDate = strftime('%Y-%m',strtotime($startDate)) . '-01 00:00:01';
            $nextMonth = strftime('%Y-%m', strtotime('+1 month',strtotime($mStartDate)));
            $prevMonth = strftime('%Y-%m', strtotime('-1 month',strtotime($mStartDate)));
            $startDOW = $this->dowMatch[strftime('%a', strtotime($mStartDate))];
            $lastDayOfMonth = strftime('%Y-%m',strtotime($mStartDate)) . '-'.date('t',strtotime($mStartDate)) .' 23:59:59';
            $startMonthCalDate = $startDOW <= 6 ? strtotime('- '.$startDOW.' day', strtotime($mStartDate)) : strtotime($mStartDate)	;
            $endMonthCalDate = strtotime('+ 6 weeks', $startMonthCalDate);
            if($debug) echo 'Active Month Only: '.$mStartDate.' :: '.$lastDayOfMonth.'  All displayed dates: '.strftime('%Y-%m-%d',$startMonthCalDate).' :: '.strftime('%Y-%m-%d',$endMonthCalDate).'<br />';
            if($activeMonthOnlyEvents) return array('start'=>strtotime($mStartDate), 'end'=>strtotime($lastDayOfMonth)); else return array('start'=>$startMonthCalDate, 'end'=>$endMonthCalDate);
        }
        public function makeEventCalendar($events=array(),$resourceId=null,$tpls=array('event'=>'month.inner.container.row.day.eventclean','day'=>'month.inner.container.row.day','week'=>'month.inner.container.row','month'=>'month.inner.container','heading'=>'month.inner.container.row.heading'), $calFilter=null, $conFilter=null, $highlightToday=true){
            $startDate = $_REQUEST['dt'] ? $_REQUEST['dt'] : strftime('%Y-%m-%d');
            $mStartDate = strftime('%Y-%m',strtotime($startDate)) . '-01 00:00:01';
            $mCurMonth = strftime('%m', strtotime($mStartDate));
            $nextMonth = strftime('%Y-%m', strtotime('+1 month',strtotime($mStartDate)));
            $prevMonth = strftime('%Y-%m', strtotime('-1 month',strtotime($mStartDate)));
            $startDOW = $this->dowMatch[strftime('%a', strtotime($mStartDate))];
            $lastDayOfMonth = strftime('%Y-%m',strtotime($mStartDate)) . '-'.date('t',strtotime($mStartDate)) .' 23:59:59';
            $endDOW = $this->dowMatch[strftime('%a', strtotime($lastDayOfMonth))];
            $tpls=(object)$tpls;
            $out = '';
            $startMonthCalDate = $startDOW <= 6 ? strtotime('- '.$startDOW.' day', strtotime($mStartDate)) : strtotime($mStartDate)	;
            $endMonthCalDate = strtotime('+ '.(6 - $endDOW).' day', strtotime($lastDayOfMonth));
            //------//
            $headingLabel = strtotime($mStartDate);
            $globalParams = array('conf'=>$conFilter, 'calf'=>$calFilter);
            $todayLink = $this->modx->makeUrl($resourceId,'', array_merge($globalParams, array('dt' => strftime('%Y-%m'), 'cid'=>$_REQUEST['cid'])));
            $prevLink = $this->modx->makeUrl($resourceId,'', array_merge($globalParams, array('dt' => $prevMonth, 'cid'=>$_REQUEST['cid'])));
            $nextLink = $this->modx->makeUrl($resourceId,'', array_merge($globalParams, array('dt' => $nextMonth, 'cid'=>$_REQUEST['cid'])));
            
            $chunkEvent = $this->loadChunk($tpls->event);
            $chunkDay = $this->loadChunk($tpls->day);
            $chunkWeek = $this->loadChunk($tpls->week);
            $chunkMonth = $this->loadChunk($tpls->month);
            
            $heading = '';
            for($i=0;$i<7;$i++){
                    if($debug) echo '&nbsp;&nbsp;'.strftime('%A ', strtotime('+ '.$i.' day', $startMonthCalDate)).'<br />';
                    $heading.=$this->getChunk($tpls->heading, array('dayOfWeekId'=>'','dayOfWeekClass'=>'mxcdow', 'dayOfWeek'=>strftime('%a ', strtotime('+ '.$i.' day', $startMonthCalDate))));
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
                if($debug) echo '---------------<br />';
                if($debug) echo 'Week '.($var + 1).'<br />';
                if($debug) echo '---------------<br />';
                // Week Start date
                $iWeek = strtotime('+ '.$var.' week', $startMonthCalDate);
                $diw = 0;
                $days = '';
                do{
                    // Get the week's days
                    $iDay = strtotime('+ '.$diw.' day', $iWeek);
                    $thisMonth = strftime('%m', $iDay);
                    if($debug) echo strftime('%a %b %d', $iDay).'<br />';
                    $eventList = '';
                    if(count($events[strftime('%Y-%m-%d', $iDay)])){
                        //-- Echo each event item
                        $e = $events[strftime('%Y-%m-%d', $iDay)];
                        
                        foreach($e AS $el){
                            if($debug) echo '&nbsp;&nbsp;<span style="color:green;">++</span>&nbsp;&nbsp;'.$el['title'].'<br />';
                            //$eventList.=$chunkEvent->process($el);
                            //@TODO -- FIX: Add check for display of current month
                            $eventList.=$this->getChunk($tpls->event, $el);
                        }
                    } else { if($debug) echo '&nbsp;&nbsp;<span style="color:red;">--&nbsp;&nbsp;'.strftime('%m-%d', $iDay).'</span><br />'; }
                    //-- Set additional day placeholders for day
                    $isToday = (strftime('%m-%d') == strftime('%m-%d', $iDay) && $highlightToday==true ? 'today ' : '');
                    $phDay = array(
                        'dayOfMonth'=> str_replace('0', ' ', (strftime('%d',$iDay) == 1 ? strftime('%b %d',$iDay) : strftime('%d',$iDay)))
                        ,'dayOfMonthID'=>'dom-'.strftime('%A%d',$iDay)
                        ,'events'=>$eventList 
                        ,'class'=>($mCurMonth == $thisMonth ? $isToday.(!empty($eventList) ? 'hasEvents' : 'noEvents') : 'ncm')
                        );
                    //$days.=$chunkDay->process($phDay);
                    $days.=$this->getChunk($tpls->day, $phDay);
                } while (++$diw < 7);
                if($debug) echo '<br />';
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
        
}


?>
