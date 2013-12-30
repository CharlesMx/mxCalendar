<?php 
/**
 * Project: mxCalendar
 * Purpose: A parser helper file to do a few little things
 */
function tstamptotime($ISO8601,$tstamp=null,$mergeTime=false) {
        // converts ISODATE to unix date
        // 1984-09-01T14:21:31
		//-- do quick sanity check
		if(empty($ISO8601) || (is_null($tstamp) && $mergeTime)) return false;
		
		sscanf($ISO8601,"%u-%u-%uT%u:%u:%u",$year,$month,$day,$hour,$min,$sec);
		
		if($mergeTime) {
			sscanf(date("H:i:s", strtotime($tstamp)),"%u:%u:%u",$hour,$min,$sec);
		}
	   
        $newtstamp=mktime($hour,$min,$sec,$month,$day,$year);
        return $newtstamp;
}

//-- Make reoccuring event list
function _getRepeatDates($frequencymode=0, $interval=1, $frequency='1',$startDate = null, $endDate = null,$onwd=array(0,1,2,3,4,5,6),$occType = 'UNIX',$options=''){
    // Lets check the timezone setting so that our date functions return correct dates for location
    /* Removed until fully supported with offset value saving in db
    if(date_default_timezone_get() != 'UTC'){
        $curTZ = date_default_timezone_get();
        if($debug) { echo 'DEBUG: Current TimeZone set to '.$curTZ.PHP_EOL; }
        date_default_timezone_set('UTC');
        if($debug) { echo 'DEBUG: SET TimeZone to '.$curTZ.' for date calculations'.PHP_EOL; }
    }
    */
    global $modx;
    
    //-- Date Output Format
    $dateFormat = 'D n-j-Y'; //'Y-m-d h:i a';
    //-- Time Output Format
    $timeFormat = 'h:ia';
    //-- Date Time Display (full=Date+Time,date=Date,time=Time)
    $dateTimeFormat = 'full';
    //-- Set Max Occurances not to exceed the end date
    //$frequency = 365;
    //-- Set the reoccurance mode (2=Months,0=Days,3=Years,1=Weeks)
    //$frequencymode = 'w';
    //-- The span (interval) between reoccurances
    $interval = (int)$interval;
    //-- Event Start Date
    //$startDate = '2010-01-11 18:00:00';
    //-- Event End Date
    //$endDate = '2010-06-11 19:30:00';
    //-- Holder of all events
    $ar_Recur = array();
    //-- Enable the debugger (Manager)
    $debug = false;

    $x = 0;
    
    $theParameter = array('MODE'=>$frequencymode, 'interval'=>$interval, 'frequency'=>$frequency, 'StartDate'=>$startDate, 'EndDate'=>$endDate, 'OnWeedkDay'=>$onwd);
    if($debug){
        echo "Date repeat function paramters are:<br />";
        foreach($theParameter AS $key=>$val)
                echo $key.'=>'.$val.'<br />'.PHP_EOL;
    }

    //-- Check the Date and build the repeat dates
    //-- prior to PHP 5.1.0 you would compare with -1, instead of false
    if (($timestamp = $startDate) === false) {
        return false;
    } else {
        SWITCH($frequencymode){
        case 0: //Daily
            while (++$x){
                $occurance = mktime(date('H', $startDate), date('i', $startDate), 0, date('m', $startDate) , date('d', $startDate)+($x*$interval), date('y', $startDate));
                if($occurance <= $endDate && $x < $frequency && $startDate < $occurance){
                    $ar_Recur[] = $occurance;
                    if($debug) echo $occurance."< -is less than (jd->".$jd."jdow->".$jdow.") -> ".$endDate.'<br />';
                }
                else{
                    if($debug) echo $occurance."||-is eq or greater (jd->".$jd."|jdow->".$jdow.") than -||".$endDate.'<br />';
                    break;
                }
            }
            break;
        case 2: //Monthly
            $occurance = $startDate;
            
            $modx->log(modX::LOG_LEVEL_ERROR,'[mxHelper] mxFormBuilder _getRpeatDate:[options]<br />'.$options);
            
            $options = !empty($options) ? json_decode($options, true) : '';
            while (++$x){

                if(!empty($options)){

			SWITCH($options['type']){
				case 'dow':
					// Day of week is simply the same day of a week 
					$occurance = strtotime($options['week']." ".strftime('%A', $occurance)." of next month", $occurance);
                                        $occurance = mktime(date('H', $startDate), date('i', $startDate), 0, date('m', $occurance) , date('d', $occurance), date('y', $occurance));
					break;
				case 'dom':
					$occurance = strtotime("next month", $occurance);
					$occurance = mktime(date('H', $startDate), date('i', $startDate), 0, date('m', $occurance) , date('d', $occurance), date('y', $occurance));
                                        break;
				default:
					$occurance = mktime(date('H', $startDate), date('i', $startDate), 0, date('m', $startDate)+($x*$interval) , date('d', $startDate), date('y', $startDate));
					break;
			}
                        
                        $modx->log(modX::LOG_LEVEL_ERROR, '[mxHelper] _getRpeatDate: Option['.$options['type'].'] :: Occurance['.$x.']=>'.date('Y-m-d h:i a', $occurance) );

                } else {
	                $occurance = mktime(date('H', $startDate), date('i', $startDate), 0, date('m', $startDate)+($x*$interval) , date('d', $startDate), date('y', $startDate));
		}

                if($occurance <= $endDate && $x < $frequency && $startDate < $occurance){
                    $ar_Recur[] = $occurance;
                    if($debug) echo $occurance."< -is less than -> ".$endDate.'<br />';
                }
                else{
                    if($debug) echo $occurance."||-is eq or greater than -||".$endDate.'<br />';
                        break;
                }
            }
            break;
        case 1: //Weekly
            $valid = true;
                            
            //-- Get the first repeat Day of Week if the same as start date's Day of Week
            $curWeek = $startWeek = strftime('%W',$startDate);
            $occurance = strftime('%Y-%m-%d %H:%M:%S',$startDate);
            $originalTime = strftime(' %H:%M:%S', $startDate);
            $nextWeek = strftime('%Y-%m-%d %H:%M:%S', strtotime('next monday', $startDate));
            if($debug) echo 'Current Week of the Start Date: '.$curWeek.'<br />';
            //-- Loop through days until the end of current week
            while($curWeek == $startWeek){
                    $occurance = strftime('%Y-%m-%d %H:%M:%S',strtotime('next day', strtotime($occurance)));
                    $curWeek= strftime('%W',strtotime($occurance));

                    //-- Get occurance day of week int
                    $thisDOW = strftime('%w',strtotime("next day",strtotime($occurance)));

                    //-- Get the valid date formated of occurance
                    $occDate = strftime('%Y-%m-%d', strtotime("next day",strtotime($occurance))).$originalTime;

                    //-- Check if the date is one of the assigned and less than the end date
                    if(in_array($thisDOW, $onwd) && $curWeek == $startWeek && strtotime($occDate) < strtotime($nextWeek) && strtotime($occDate) > strtotime($startDate)){
                            if($debug) echo $occDate." MATCH on $thisDOW (start week) :: CurWk=$curWeek :: StartWk=$startWeek :: NextWk=$nextWeek<br />";
                            $ar_Recur[] = ($occType == 'UNIX' ? strtotime($occDate) : $occDate);
                    } else {
                            if($debug  && $curWeek == $startWeek && strtotime($occDate) < strtotime($nextWeek)) 
                            echo $occDate." (start week)<br />";
                    }
            }

            $startDate  = date('Y-m-d H:i:s', strtotime(' last mon ',strtotime($occurance)));
            if($debug) echo '<strong>Start date MONDAY of that week: </strong>: '.$startDate.'<br />';
            $startDate = date('Y-m-d H:i:s', strtotime(' + '.($interval).' week',strtotime($startDate)));
            if($debug)
                echo '<strong>Next Valid Repeat Week Start Date: </strong>: '.$startDate.'<br />'.
                     'Modified start: '.$startDate.' with adjusted interval: '.($interval).' <br />'.
                     'Frequency: '.$frequency.' with the max repeat of: '.($frequency*7).'<br />';

            //-- Created a new loop to limit the possibility of almost endless loop
            $newDate = strtotime($startDate);
			$x=1;
            while($newDate <= $endDate){
                if($debug) echo "x={$x}<br />";
                $occurance = $newDate; //date('Y-m-d H:i:s', c);

                $lastweek=sprintf("%02d", (strftime('%W',$newDate) ));
                if($debug) echo 'Week of: '.$lastweek."<br />";
                $year = strftime('%Y',$occurance);
                for ($i=0;$i<=6;$i++){

                    //-- Get occurance day of week int
                    $thisDOW = strftime('%w',strtotime("+{$i} day",$occurance));

                    //-- Get the valid date formated of occurance
                    $occDate = strftime('%Y-%m-%d', strtotime("+{$i} day",$occurance)).$originalTime;

                    //-- Check if the date is one of the assigned and less than the end date
                    if(in_array($thisDOW, $onwd) && strtotime($occDate) <= $endDate){
                        if($debug) echo $occDate." MATCH on $thisDOW <br />";
                        $ar_Recur[] = ($occType == 'UNIX' ? strtotime($occDate) : $occDate);
                    } else {
                        if($debug) echo $occDate."<br />";
                    }

                    //-- If the date is past the end date end the loop
                    if(strtotime($occDate) >= $endDate){
                        if($debug) echo "\t".strtotime($occDate) .' is greater than '. $endDate."<br />";
                        $valid = false; //-- End the loop
                        break;
                    }
                    //-- Reset the date for while loop validation
                    $newDate = strtotime(' + '.$interval.' weeks',$occurance);
                }
                $x++;
                if(!$valid || $x > $frequency) break;
            }
            if($debug) echo '<strong><em>'.count($ar_Recur).'<em> total matches dates added.</strong>';
            break;
        case 3: //Yearly
            while (++$x){
                $occurance = mktime(date('H', $startDate), date('i', $startDate), 0, date('m', $startDate) , date('d', $startDate), date('y', $startDate)+($x*$interval));
                if($occurance <= $endDate && $x < $frequency && $startDate < $occurance){
                    $ar_Recur[] = $occurance;
                    if($debug) echo $occurance."< -is less than -> ".$endDate.'<br />';
                }
                else{
                    if($debug) echo $occurance."||-is eq or greater than -||".$endDate.'<br />';
                    break;
                }
            }
            break;    
        }
        //-- Display the results to validate
        if($debug){
            echo "THE OCC DATES:<br />";
            print_r($ar_Recur);
        }
        if(isset($curTZ)) date_default_timezone_set($curTZ);
        return implode(',', $ar_Recur);
    }
}

?>