Ext.USE_NATIVE_JSON = false;
function openWin()
{
    pf = document.getElementById("printfriendly").innerHTML;
    pfw=window.open('','','width=550,height=600');
    pfw.document.write(pf);
    pfw.focus();
}
                                
mxcCore.grid.calendars = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'mxcalendars-grid-calendars'
        ,url: mxcCore.config.connectorUrl
        ,baseParams: { action: 'mgr/calendar/getList' }
        ,fields: ['id','name','webusergroup','active']
        ,paging: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'name'
        ,save_action: 'mgr/calendar/updatefromgrid' // Support the inline editing
	,autosave: true // Support the inline editing
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
        },{
            header: _('mxcalendars.calendar_name_col_label')
            ,dataIndex: 'name'
            ,sortable: true
            ,editor: { xtype: 'textfield' }
        }/*,{
            header: _('mxcalendars.calendar_name_col_wug')
            ,dataIndex: 'webusergroup'
            ,sortable: true
            ,editor: { xtype: 'textfield' }
        }*/,{
            header: _('mxcalendars.calendar_name_col_active')
            ,dataIndex: 'active'
            ,sortable: true
            ,editor: { xtype: 'modx-combo-boolean', renderer: true}
        }],tbar:[{
                xtype: 'textfield'
                ,id: 'mxcalendars-search-calendars-filter'
                ,emptyText:_('mxcalendars.default_calendar_search')
                ,listeners: {
                        'change': {fn:this.search,scope:this}
                        ,'render': {fn: function(cmp) {
                                new Ext.KeyMap(cmp.getEl(), {
                                        key: Ext.EventObject.ENTER
                                        ,fn: function() {
                                                this.fireEvent('change',this);
                                                this.blur();
                                                return true;
                                        }
                                        ,scope: cmp
                                });
                        },scope:this}
                }
        },'->',{
           text:_('mxcalendars.calendar_btn_create')
           ,handler: { xtype: 'mxcalendars-window-calendar-create' ,blankValues: true }
        }]
    });
    mxcCore.grid.calendars.superclass.constructor.call(this,config)
};
Ext.extend(mxcCore.grid.calendars,MODx.grid.Grid,{
    search: function(tf,nv,ov) {textfield
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },getMenu: function() {
		var m = [{
			text: _('mxcalendars.calendar_context_menu_update')
			,handler: this.updateCal
		},'-',{
			text: _('mxcalendars.calendar_context_menu_remove')
			,handler: this.removeCal
		},'-',{
			text: "View Calendar Item"
			,handler: this.viewCal
		}];
		this.addContextMenuItem(m);
		return true;
	},updateCal: function(btn,e) {
		if (!this.updateCatWindow) {
			this.updateCatWindow = MODx.load({
				xtype: 'mxcalendars-window-calendar-update'
				,record: this.menu.record
				,listeners: {
					'success': {fn:this.refresh,scope:this}
				}
			});
		} else {
			this.updateCatWindow.setValues(this.menu.record);
		}
		this.updateCatWindow.show(e.target);
	},removeCal: function() {
		MODx.msg.confirm({
		    title: _('mxcalendars.cateogry_remove_title')
		    ,text: _('mxcalendars.cateogry_remove_confirm')
		    ,url: this.config.url
		    ,params: {
		        action: 'mgr/calendar/remove'
		        ,id: this.menu.record.id
		    }
		    ,listeners: {
		        'success': {fn:this.refresh,scope:this}
		    }
		});
	}, viewCal: function(){
            //-- this is your this.menu.record.json_post object
            var json_src = '{"cust_FirstName":"Racovita","cust_LastName":"Victor","cust_PhoneDay":"","cust_Email1":"diavolss@yahoo.com","vInterest":"Pre-Owned","cust_Comments":"Dear Mr\/Ms\\r\\n\\r\\nI would like to ask how can i contact Hyundai engineering department?\\r\\nThank You","submit":"Submit","formConfigId":"contactUs","frm_chainTo":"\/request\/contact"}';
            var jsonData = Ext.util.JSON.decode(json_src);
            var win = new Ext.Window({
                title   :'My Title'
                ,width  : '80%'
                ,height : '70%'
                ,plain  : true
                ,html   : ' <div id="printfriendly">'+
                                '<h2>My HTML content: '+this.menu.record.name+'</h2>'+
                                '<p>More fancy things can go here just remember your single quotes.</p>'+
                                '<p>Sample JSON DATA: <strong>'+jsonData.cust_FirstName+' '+jsonData.cust_LastName+'</strong></p>'+
                                '</div>'+
                                '<p><a href="javascript:openWin();return false;">[ Print Me ]</a></p>'
            });
            win.show();
        }
});
Ext.reg('mxcalendars-grid-calendars',mxcCore.grid.calendars);


//---------------------------------------//
//-- Create the Update Calendar Window --//
//---------------------------------------//
mxcCore.window.UpdateCat = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: ''+_('mxcalendars.label_window_create')
        ,url: mxcCore.config.connectorUrl
        ,width: 'auto'
        ,baseParams: {
            action: 'mgr/calendar/update'
        }
        ,fields: [{xtype:'hidden',name:'id'},{
            xtype: 'textfield'
            ,fieldLabel:_('mxcalendars.calendar_name_col_label')
            ,name: 'name'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.calendar_name_col_active')
            ,name: 'active'
            ,checked: true
            ,value: 1
        }]
    });
    mxcCore.window.UpdateCat.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.UpdateCat,MODx.Window);
Ext.reg('mxcalendars-window-calendar-update',mxcCore.window.UpdateCat);


//-------------------------------------------//
//-- Create the object for th new Calendar --//
//-------------------------------------------//
mxcCore.window.CreateCal = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: ''+_('mxcalendars.label_window_create')
        ,url: mxcCore.config.connectorUrl
        ,width: 'auto'
        ,baseParams: {
            action: 'mgr/calendar/create'
        }
        ,fields: [{
            xtype: 'textfield'
            ,fieldLabel:_('mxcalendars.calendar_name_col_label')
            ,name: 'name'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.calendar_name_col_active')
            ,name: 'active'
            ,checked: true
            ,value: 1
        }]
    });
    mxcCore.window.CreateCal.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.CreateCal,MODx.Window);
Ext.reg('mxcalendars-window-calendar-create',mxcCore.window.CreateCal);
