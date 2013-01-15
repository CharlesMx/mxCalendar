mxcCore.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,items: [{
            html: '<img src="'+mxcCore.config.assetsUrl+'images/mxcalendar.png" alt="'+_('mxcalendars.management')+'" />'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,bodyStyle: 'padding: 10px'
            ,defaults: { border: false,autoHeight: true }
            ,border: true
            ,items: [{
                //-- Events Tab
                title: _('mxcalendars.tab_events')
                ,defaults: { autoHeight: true }
                ,items: [
                        // ADD DESCRIPTION INFORMATION
                        {
                                html: '<p>'+_('mxcalendars.management_desc')+'</p><br />'
                                ,border: false
                        },
                        // ADD THE GRID CONTROLLER
                        {
                           xtype: 'mxcalendars-grid-events'
                           ,preventRender: true
                        }
                        ]
                },{
                //-- Categories Tab
                title: _('mxcalendars.tab_categories')
                ,defaults: { autoHeight: true }
                    ,items: [{
                        html: '<p>'+_('mxcalendars.category_desc')+'</p><br />'
                       ,border: false
                    },{
                        xtype: 'mxcalendars-grid-categories'
                        ,preventRender: true
                    }]
                },{
                //-- Calendars Tab
                title: _('mxcalendars.tab_calendars')
                ,items: [{
                        html: '<p>'+_('mxcalendars.calendar_desc')+'</p><br />'
                       ,border: false
                    },{
                        xtype: 'mxcalendars-grid-calendars'
                        ,preventRender: true
                    }]
                },{
                //-- Feeds Tab
                title: _('mxcalendars.tab_feed')
                ,items: [{
                        html: '<p>'+_('mxcalendars.feed_desc')+'</p><br />'
                       ,border: false
                    },{
                        xtype: 'mxcalendars-grid-feeds'
                        ,preventRender: true
                    }]
                }
            ]
        }]
    });
    mxcCore.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.panel.Home, MODx.Panel);
Ext.reg('mxcalendars-panel-home', mxcCore.panel.Home);
