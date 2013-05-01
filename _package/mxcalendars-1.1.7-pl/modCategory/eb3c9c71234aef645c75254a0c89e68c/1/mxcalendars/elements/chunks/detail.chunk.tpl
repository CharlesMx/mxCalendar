<h2 style="font-size: 100%;">[[+title]]</h2>

<p>[[+startdate_fstamp:date=`%b %e %l:%M %p`]] - [[+enddate_fstamp:date=`[[+durDay:notempty=`%b %e `]] %l:%M %p`]]</p>

<p>Duration: [[+durYear:notempty=`[[+durYear]] Years `]][[+durMonth:notempty=`[[+durMonth]] Months `]][[+durDay:notempty=`[[+durDay]] Days `]][[+durHour:notempty=`[[+durHour]] Hours `]][[+durMin:notempty=`[[+durMin]] Minutes `]]

<p>[[+description]]</p>

[[+imagesTotal:gt=`0`:then=`
    <h3>[[+imagesTotal]] Images Attached</h3>
    <div>[[+images]]</div>

    <h3>Sample Single Image Placeholder (first image)</h3>
    <p>[[+images_1]]</p>
`:else=``]]

<p>Type: <span style="[[+foregroundcss:notempty=`color:[[+foregroundcss]];`]][[+backgroundcss:notempty=`background-color:[[+backgroundcss]];`]]">[[+category]]</span></p>

<p>Preformatted times:<br />[[+startdate_fdate]] [[+startdate_ftime]] - [[+enddate_fdate]] [[+enddate_ftime]]</p>

<h4>[[+location_name]]</h4>
[[+map]]
