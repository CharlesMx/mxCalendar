<hr />
<div class="event" [[+inlinecss:notempty=`style="[[+inlinecss]]"`]] >
    
    <div class="date">
        <div class="month"><p>[[+startdate:date=`%h`]]<span>[[+startdate:date=`%Y`]]</span></p></div>
        <p class="[[+durDay:notempty=`multi`]]">[[+startdate:date=`%e`]][[+durDay:notempty=`[[+dateseperator]][[+enddate:date=`%e`]]`]]</p>
        [[+repeating:is=`1`:then=`<span class="repeating" title="repeating event"></span>`]]
    </div>
    <h6 style="font-size: 100%;"><a href="[[+detailURL]]">[[+title]]</a></h6>
        <p>[[+startdate:date=`%b %e %l:%M %p`]] - [[+enddate:date=`[[+durDay:notempty=`%b %e `]] %b %e %l:%M %p`]]</p>

        <p>Duration: [[+durYear:notempty=`[[+durYear]] Years `]][[+durMonth:notempty=`[[+durMonth]] Months `]][[+durDay:notempty=`[[+durDay]] Days `]][[+durHour:notempty=`[[+durHour]] Hours `]][[+durMin:notempty=`[[+durMin]] Minutes `]]
	<p>[[+description]]</p>
        <p><span style="[[+foregroundcss:notempty=`color:[[+foregroundcss]];`]][[+backgroundcss:notempty=`background-color:[[+backgroundcss]];`]]">[[+category]]</span></p>

        [[+images]]
        
</div>