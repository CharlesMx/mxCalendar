<!--
<h2 style="font-size: 100%;">[[+title]]</h2>
<p>TEST: [[+startdate_fdate]]</p>

<p>[[+startdate_fstamp:date=`%b %e %l:%M %p`]] - [[+enddate_fstamp:date=`[[+durDay:notempty=`%b %e `]] %l:%M %p`]]</p>

<h6>TEST FORCED: [[+datetest]]</h6>

<p>Duration: [[+durYear:notempty=`[[+durYear]] Years `]][[+durMonth:notempty=`[[+durMonth]] Months `]][[+durDay:notempty=`[[+durDay]] Days `]][[+durHour:notempty=`[[+durHour]] Hours `]][[+durMin:notempty=`[[+durMin]] Minutes `]]

<p>[[+description]]</p>

<h3>[[+imagesTotal]] Images Attached</h3>
<p>[[+images]]</p>

<h3>Sample single image placeholder</h3>
<p>[[+images_1]]</p>

<p>Type: <span style="[[+foregroundcss:notempty=`color:[[+foregroundcss]];`]][[+backgroundcss:notempty=`background-color:[[+backgroundcss]];`]]">[[+category]]</span></p>

<p>Preformatted times:<br />[[+startdate_fdate]] [[+startdate_ftime]] - [[+enddate_fdate]] [[+enddate_ftime]]</p>

<h4>[[+location_name]]</h4>
[[+map]]
-->


[[+images]]
<h1>[[+title]]</h1>

<h4><em>[[+startdate_fstamp:date=`%b %e %l:%M %p`]] - [[+enddate_fstamp:date=`[[+durDay:notempty=`%b %e `]] %l:%M %p`]]</em></h4>
[[+location_name:eq=``:then=``:else=`<h4>Location: [[+location_name]]</h4>
[[+map]]`]]

[[+description:eq=``:then=``:else=`<p>[[+description:replace=`\n==<br/>`:replace=`http://http://==http://`]]</p>`]]

[[+form_chunk:eq=``:then=``:else=`[[$[[+form_chunk]]]]`]]

<a class="callout_button" href="[[~177]]?detail=[[+id]]">Add This Event to Your Calendar</a>