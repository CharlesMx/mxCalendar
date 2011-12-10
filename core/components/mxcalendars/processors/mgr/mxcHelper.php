<?php 
/**
 * A parser helper file
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

?>