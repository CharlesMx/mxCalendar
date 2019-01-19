<h2>[[+title]]</h2>

<p>[[+startdate:date=`%b %e %l:%M %p`]] - [[+enddate:date=`[[+durDay:notempty=`%b %e `]] %b %e %l:%M %p`]]</p>

<p>Duration: [[+durYear:notempty=`[[+durYear]] Years `]][[+durMonth:notempty=`[[+durMonth]] Months `]][[+durDay:notempty=`[[+durDay]] Days `]][[+durHour:notempty=`[[+durHour]] Hours `]][[+durMin:notempty=`[[+durMin]] Minutes `]]

<p>[[+description]]</p>

[[+images:notempty=`
    <p>Images ([[+imagesTotal]]):<br>[[+images]]</p>
    [[+images_1:ne=``:then=`
    <p>Single Image: [[+images_1]]</p>
    `:else=``]]
`]]

[[+videos:notempty=`
    <p>Videos ([[+videosTotal]]):<br>[[+videos]]</p>
    [[+videos_1:ne=``:then=`
    <p>Single Video: [[+video_1]]</p>
    `:else=``]]
`]]

<p>Type: <span style="[[+foregroundcss:notempty=`color:[[+foregroundcss]];`]][[+backgroundcss:notempty=`background-color:[[+backgroundcss]];`]]">[[+category]]</span></p>

[[+map:notempty=`
    <h4>[[+location_name]]</h4>
    [[+map]]
`]]
