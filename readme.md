# mxCalendar

mxCalendar is a full feature calendaring extra with ability to 
create repeating events, assign categories, and integrate with Google Maps 
for location. This is a perfect solution for most users looking for 
an event management calendar or just a simple calendar component.

**Key Features:**

- Supports Repeating Events, even where the durations spans multiple days
- Multiple segmented calendars
- Context assignment or global
- Custom categories with associated styling through management interface
- Fully customizable look-n-feel through themed style sheets (CSS)
- Single click to duplicate complex events with repeating properties
- Event list format and Calendar views, soon to include mini-cal
- Repeating dates

## AJAX Setup

1. Install package
2. Create API-resource: 
    ```
    [[!mxcalendar?
        &debug=`0`
        &addJQ=`0`
        &modalView=`1`
        &tplDetail=`Calendar-Detail`
        &tplDetailModal=`Calendar-Detail`
        &dateformat=`%d.%m.%Y`
        &timeformat=`%H:%M`
        &dateseperator=`.`
        &gmapAPIKey=`[[++google_api_key]]`
    ]]
    ```
3. Create Calendar-resource: 
    ```
    [[!mxcalendar?
        &ajaxResourceId=`42`
        &modalView=`1`
    ]]
    ```

## Usage examples

Basic use: 
```
[[!mxcalendar?]]
```

Advanced use:

```
[[!mxcalendar?
    &debug=`0`
    &addJQ=`1`
    &ajaxResourceId=`42`
    &modalView=`1`
    &showCategories=`0`
    &displayType=`mini`
    &dateformat=`%d.%m.%Y`
    &timeformat=`%H:%M`
    &dateseperator=`.`
    
    &tplDetailModal=`Calendar-Detail`
    &tplEvent=`Calendar-Event`
    &tplMonth=`Calendar-Month`
    &tplCategoryWrap=`Nothing`
    &tplCategoryItem=`Nothing`
    
    &gmapAPIKey=`[[++google_api_key]]`
]]
```

## Documentation

See newly-started [Wiki on GitHub](https://github.com/sebastian-marinescu/mxCalendar/wiki). 

Or the MODX-documentations on mxCalendar: 
- https://docs.modx.com/extras/revo/mxcalendar
- https://docs.modx.com/extras/revo/mxcalendar/mxcalendar.placeholders
- https://docs.modx.com/extras/revo/mxcalendar/mxcalendar.examples
