<?php
/**
* @package modular
* @subpackage build
*/
function getChunkContent($filename) {
    $o = file_get_contents(strtolower($filename));
    $o = trim(str_replace(array('<?php','?>'),'',$o));
    return $o;
}
$chunks = array();

//-- Add the individual list item chunk 
$chunks[1]= $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => 1,
    'name' => 'tplListItem',
    'description' => 'The template that that holds the event detail properties.',
    'snippet' => getChunkContent($sources['elements'].'chunks/el.itemclean.chunk.tpl'),
),'',true,true);

//-- Add the individual list item chunk 
$chunks[2]= $modx->newObject('modChunk');
$chunks[2]->fromArray(array(
    'id' => 1,
    'name' => 'tplListHeading',
    'description' => 'This template is used to set a split between months in the event list. It returns the start date so you can apply the date output modifier to adjust. In order to remove this simply make this entry empty (null).',
    'snippet' => getChunkContent($sources['elements'].'chunks/el.listheading.chunk.tpl'),
),'',true,true);

//-- Add the individual list item chunk 
$chunks[3]= $modx->newObject('modChunk');
$chunks[3]->fromArray(array(
    'id' => 1,
    'name' => 'tplListWrap',
    'description' => 'The template to use as the outter most wrapper of the events list.',
    'snippet' => getChunkContent($sources['elements'].'chunks/el.wrap.chunk.tpl'),
),'',true,true);

//-- Add the individual list item chunk 
$chunks[4]= $modx->newObject('modChunk');
$chunks[4]->fromArray(array(
    'id' => 1,
    'name' => 'tplEvent',
    'description' => 'The inner most template to use for the individual event details.',
    'snippet' => getChunkContent($sources['elements'].'chunks/month.inner.container.row.day.eventclean.chunk.tpl'),
),'',true,true);

//-- Add the individual list item chunk 
$chunks[5]= $modx->newObject('modChunk');
$chunks[5]->fromArray(array(
    'id' => 1,
    'name' => 'tplDay',
    'description' => 'The template to use as the day of the month wrapper. Containes the tplEvent combined output.',
    'snippet' => getChunkContent($sources['elements'].'chunks/month.inner.container.row.day.chunk.tpl'),
),'',true,true);

//-- Add the individual list item chunk 
$chunks[6]= $modx->newObject('modChunk');
$chunks[6]->fromArray(array(
    'id' => 1,
    'name' => 'tplWeek',
    'description' => 'The template to use for the wrapper of all the days in a given week. Contains all the tplDay results.',
    'snippet' => getChunkContent($sources['elements'].'chunks/month.inner.container.row.chunk.tpl'),
),'',true,true);

//-- Add the individual list item chunk 
$chunks[7]= $modx->newObject('modChunk');
$chunks[7]->fromArray(array(
    'id' => 1,
    'name' => 'tplMonth',
    'description' => 'The template to use as the outter most wrapper of the weeks results.',
    'snippet' => getChunkContent($sources['elements'].'chunks/month.inner.container.chunk.tpl'),
),'',true,true);

//-- Add the individual list item chunk 
$chunks[8]= $modx->newObject('modChunk');
$chunks[8]->fromArray(array(
    'id' => 1,
    'name' => 'tplHeading',
    'description' => 'The template to use as the calendar heading and navigation controls. This could set to empty in order to return a fixed calendar whereby the user would not have direct navigation of other months.',
    'snippet' => getChunkContent($sources['elements'].'chunks/month.inner.container.row.heading.chunk.tpl'),
),'',true,true);

//-- Add the individual list item chunk 
$chunks[9]= $modx->newObject('modChunk');
$chunks[9]->fromArray(array(
    'id' => 1,
    'name' => 'tplDetail',
    'description' => 'The template to use as the event detail view.',
    'snippet' => getChunkContent($sources['elements'].'chunks/detail.chunk.tpl'),
),'',true,true);

//-- Add the individual list item chunk 
$chunks[10]= $modx->newObject('modChunk');
$chunks[10]->fromArray(array(
    'id' => 1,
    'name' => 'tplListItemTraditional',
    'description' => 'This is a carry over from the traditional mxCalendar Evo version of the fields in the list view.',
    'snippet' => getChunkContent($sources['elements'].'chunks/el.item.chunk.tpl'),
),'',true,true);

//-- Add the individual list item chunk 
$chunks[11]= $modx->newObject('modChunk');
$chunks[11]->fromArray(array(
    'id' => 1,
    'name' => 'tplEventTraditional',
    'description' => 'This is a carry over from the traditional mxCalendar Evo version of the fields in the calendar view.',
    'snippet' => getChunkContent($sources['elements'].'chunks/month.inner.container.row.day.event.chunk.tpl'),
),'',true,true);

//-- Add the individual list item chunk 
$chunks[12]= $modx->newObject('modChunk');
$chunks[12]->fromArray(array(
    'id' => 1,
    'name' => 'tplCategoryWrap',
    'description' => 'The outer container for the cateogry listing when enabled for the calendar display.',
    'snippet' => getChunkContent($sources['elements'].'chunks/category.container.chunk.tpl'),
),'',true,true);

//-- Add the individual list item chunk 
$chunks[13]= $modx->newObject('modChunk');
$chunks[13]->fromArray(array(
    'id' => 1,
    'name' => 'tplCategoryItem',
    'description' => 'The inside container that holds the category output.',
    'snippet' => getChunkContent($sources['elements'].'chunks/category.container.item.chunk.tpl'),
),'',true,true);

//-- Add the modal detail item chunk 
$chunks[14]= $modx->newObject('modChunk');
$chunks[14]->fromArray(array(
    'id' => 1,
    'name' => 'tplDetailModal',
    'description' => 'The template file for use with the AJAX modal detail view.',
    'snippet' => getChunkContent($sources['elements'].'chunks/detail.modal.chunk.tpl'),
),'',true,true);

//-- Add the image item chunk 
$chunks[15]= $modx->newObject('modChunk');
$chunks[15]->fromArray(array(
    'id' => 1,
    'name' => 'tplImageItem',
    'description' => 'Image item default chunk used in both the List and Detail views.',
    'snippet' => getChunkContent($sources['elements'].'chunks/image.chunk.tpl'),
),'',true,true);

//-- Add the no events chunk 
$chunks[16]= $modx->newObject('modChunk');
$chunks[16]->fromArray(array(
    'id' => 1,
    'name' => 'tplNoEvents',
    'description' => 'No events chunk to display in list display mode.',
    'snippet' => getChunkContent($sources['elements'].'chunks/el.noevents.chunk.tpl'),
),'',true,true);

//-- Add the search chunk 
$chunks[17]= $modx->newObject('modChunk');
$chunks[17]->fromArray(array(
    'id' => 1,
    'name' => 'tplSearch',
    'description' => 'Default search form template.',
    'snippet' => getChunkContent($sources['elements'].'chunks/search.chunk.tpl'),
),'',true,true);

return $chunks;