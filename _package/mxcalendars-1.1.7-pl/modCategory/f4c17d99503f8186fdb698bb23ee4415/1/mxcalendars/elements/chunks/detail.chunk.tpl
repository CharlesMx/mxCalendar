<h2 style="font-size: 100%;">[[+title]]</h2>
<p>TEST: [[+startdate_fdate]]</p>

<p>[[+startdate:date=`%b %e %l:%M %p`]] - [[+enddate:date=`[[+durDay:notempty=`%b %e `]] %b %e %l:%M %p`]]</p>

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
