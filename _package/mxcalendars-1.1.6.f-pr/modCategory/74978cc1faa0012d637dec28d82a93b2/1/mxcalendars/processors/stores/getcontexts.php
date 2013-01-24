<?php
if (!$modx->user->isAuthenticated('mgr')) return $modx->error->failure($modx->lexicon('permission_denied'));
/*
$contextKeys = array();
$query = $modx->newQuery('modContext', array('key:NOT IN' => array('mgr')));
$query->select($modx->getSelectColumns('modContext', 'modContext', '', array('key')));
if ($query->prepare() && $query->stmt->execute()) {
    $contextKeys = $query->stmt->fetchAll(PDO::FETCH_COLUMN);
}

//--iterate
$list = array();
foreach ($contextKeys as $ctx) {
    $list[] = array('key'=>$ctx); 
}
*/
$list = array();

//-- Restrict Access Based on User Group Access
$userWUG_arr = $modx->user->getUserGroupNames();
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
                    if(!in_array($acl->get('target'), $list))
                        $list[] = array('key'=>$acl->get('target'));
                }
            }

    }
}
if($modx->user->isMember('Administrator')) { $list[] = array('key'=>''); }


if(!count($list)){
    $contextKeys = array();
    $query = $modx->newQuery('modContext', array('key:NOT IN' => array('mgr')));
    $query->select($modx->getSelectColumns('modContext', 'modContext', '', array('key')));
    if ($query->prepare() && $query->stmt->execute()) {
        $contextKeys = $query->stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    foreach ($contextKeys as $ctx) {
        $list[] = array('key'=>$ctx); 
    }
}

return $this->outputArray($list,sizeof($list));