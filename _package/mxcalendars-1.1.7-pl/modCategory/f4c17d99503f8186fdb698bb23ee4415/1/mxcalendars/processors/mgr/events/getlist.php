<?php

$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,10);
$sort = $modx->getOption('sort',$scriptProperties,'startdate');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$query = $modx->getOption('query',$scriptProperties,'');
$historical = $modx->getOption('historical',$scriptProperties,0);
 
/*
$c = $xpdo->newQuery('Box');
$c->innerJoin('BoxOwner','Owner'); // arguments are: className, alias
$c->innerJoin('User','User','Owner.user = User.id');
// note the 3rd argument that defines the relationship in the innerJoin
 
$c->where(array(
   'Box.width' => 5,
   'User.user' => 2,
));
$c->sortby('Box.name','ASC');
$c->limit(5,5);
$boxes = $xpdo->getCollection('Box',$c);
*/ 
 //$user->isMember('UserGroupName')

//-- Restrict Access Based on User Group Access
$userWUG_arr = $modx->user->getUserGroupNames();
$userContextACL_arr = array();
$userid = $modx->user->get('id');
$ug = $modx->newQuery('modUserGroup');
$ug->where(array(
    'name:IN' => $userWUG_arr,
));
$mxc_groups = $modx->getIterator('modUserGroup', $ug);
if(count($mxc_groups)){
    foreach($mxc_groups AS $mxg){

            $webContextAccess = $modx->newQuery('modAccessContext');
            $webContextAccess->where(array(
                'principal' => $mxg->get('id'),
                'AND:target:!=' => 'mgr',
            ));
            $mxc_cntx = $modx->getIterator('modAccessContext', $webContextAccess);
			
            if(count($mxc_cntx)){
                foreach($mxc_cntx AS $acl){
                    if(!in_array($acl->get('target'), $userContextACL_arr))
                        $userContextACL_arr[] = $acl->get('target');
                }
            }

    }
}
if($modx->user->isMember('Administrator')) { $userContextACL_arr[] = ''; }


/* build query */
$c = $modx->newQuery('mxCalendarEvents');
$c->select(array(
	'mxCalendarEvents.*',
	//'CategoryId.name', 	'CategoryId.foregroundcss', 'CategoryId.backgroundcss', 'CategoryId.inlinecss'
));
//$c->innerJoin('mxCalendarCategories','CategoryId');
//$c->innerJoin('categoryid','id','mxCalendarEvents.categoryid = CategoryId');
if (!empty($query)) {
    $c->where(array(
        'title:LIKE' => '%'.$query.'%',
        'OR:description:LIKE' => '%'.$query.'%',
        //'OR:CategoryId.name:LIKE'=>'%'.$query.'%',
    ));
} else {
    if($historical){
        $c->where(array(
            'repeating:=' => 0
            ,'AND:enddate:<=' => time()
            ,array(
                'OR:repeating:='=>1
                ,'AND:repeatenddate:<=' => time()
            )
        ));
    } else {
        $c->where(array(
            'repeating:=' => 0
            ,'AND:enddate:>=' => time()
            ,array(
                'OR:repeating:='=>1
                ,'AND:repeatenddate:>=' => time()
            )
        ));
    }
}
if(count($userContextACL_arr)){
    $c->where(array('context:IN' => $userContextACL_arr));
}

$count = $modx->getCount('mxCalendarEvents',$c);
$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit,$start);
$mxcalendars = $modx->getIterator('mxCalendarEvents', $c);
 
//-- Get Settings Date and Time formats
$dateFormat = $modx->getOption('mxcalendars.mgr_dateformat', '', 'm/d/Y');
$timeFormat = $modx->getOption('mxcalendars.mgr_timeformat', '', 'g:i a');

/* iterate */
$list = array();
foreach ($mxcalendars as $mxc) {
    $mxcArray = $mxc->toArray();
    //-- Split the single unix time stamp into date and time for UI
    $mxcArray['startdate_date'] = date($dateFormat,$mxc->get('startdate'));  
    $mxcArray['startdate_time'] = date($timeFormat,$mxc->get('startdate'));
    $mxcArray['startdate'] = $mxc->get('startdate');

    $mxcArray['enddate_date'] = date($dateFormat,$mxc->get('enddate'));  
    $mxcArray['enddate_time'] = date($timeFormat,$mxc->get('enddate'));
    $mxcArray['enddate'] = $mxc->get('enddate');

    $ed = $mxc->get('repeatenddate');
    $mxcArray['repeatenddate'] = !empty($ed) ? $mxc->get('repeatenddate') : null;
    
    //-- Get the names of the Categories for friendly output
    $catFriendly = array();
    $curCatIds = $mxc->get('categoryid');
    if(!empty($curCatIds)){
        $cats = explode(',', $curCatIds);
        if(count($cats)){
            foreach($cats AS $c){
                $obj = $modx->getObject('mxCalendarCategories',$c);
                if($obj){
                    $catFriendly[] = $obj->get('name');
                }
            }
        }
    }
    
    $mxcArray['catfriendly'] = implode(', ', $catFriendly);
    
    $list[]= $mxcArray;
}
return $this->outputArray($list,$count);

?>
