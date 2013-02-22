Ext.USE_NATIVE_JSON = false;

mxcCore.grid.feeds = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'mxcalendars-grid-feeds'
        ,url: mxcCore.config.connectorUrl
        ,baseParams: { action: 'mgr/feed/getList' }
        ,fields: ['id','feed','type','defaultcategoryid','timerint','timermeasurement',{name:'lastrunon', type: 'date', dateFormat:'timestamp'},{name:'nextrunon', type: 'date', dateFormat:'timestamp'},'active']
        ,paging: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'name'
        ,save_action: 'mgr/feed/updatefromgrid' // Support the inline editing
	,autosave: true // Support the inline editing
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 5
        },{
            header: _('mxcalendars.feed_col_feed')
            ,dataIndex: 'feed'
            ,sortable: true
            ,editor: { xtype: 'textfield' }
        },{
            header: _('mxcalendars.lastrunon_col_label')
            ,dataIndex: 'lastrunon'
            ,sortable: true
            ,width:40
            , xtype : 'datecolumn'
            ,format:mxcCore.config.mgr_dateformat+' '+mxcCore.config.mgr_timeformat
            , editable:false
            , editor:{xtype:'datefield', format:mxcCore.config.mgr_dateformat+' '+mxcCore.config.mgr_timeformat}
        },{
            header: _('mxcalendars.nextrunon_col_label')
            ,dataIndex: 'nextrunon'
            ,sortable: true
            ,width:40
            , xtype : 'datecolumn'
            ,format:mxcCore.config.mgr_dateformat+' '+mxcCore.config.mgr_timeformat
            , editable:false
            , editor:{xtype:'datefield', format:mxcCore.config.mgr_dateformat+' '+mxcCore.config.mgr_timeformat}
        },{
            header: _('mxcalendars.feed_col_type'),
            dataIndex: 'type',
            sortable: true,
            width:15,
            editor: { xtype: 'mxc-combo-feedtype', renderer: true, width:120 }}
        ,{
            header: _('mxcalendars.label_default')+' '+_('mxcalendars.categoryid_col_label')
            ,dataIndex: 'defaultcategoryid'
            ,sortable: true
            ,width:20
            ,editor: { xtype: 'mxc-combo-categories', renderer: true }}
        ,{
            header: _('mxcalendars.feed_col_active')
            ,dataIndex: 'active'
            ,sortable: true
            ,editor: { xtype: 'modx-combo-boolean', renderer: true }
            ,width: 15
        }],tbar:[{
                xtype: 'textfield'
                ,id: 'mxcalendars-search-feeds-filter'
                ,emptyText:_('mxcalendars.default_feed_search')
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
           text:_('mxcalendars.feed_btn_create')
           ,handler: { xtype: 'mxcalendars-window-feed-create' ,blankValues: true }
        }]
    });
    mxcCore.grid.feeds.superclass.constructor.call(this,config)
};
Ext.extend(mxcCore.grid.feeds,MODx.grid.Grid,{
    search: function(tf,nv,ov) {textfield
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },getMenu: function() {
		var m = [{
			text: _('mxcalendars.feed_menu_update')
			,handler: this.update
		},'-',{
			text: _('mxcalendars.feed_menu_remove')
			,handler: this.remove
		}];
		this.addContextMenuItem(m);
		return true;
	},update: function(btn,e) {
		if (!this.updateWindow) {
			this.updateWindow = MODx.load({
				xtype: 'mxcalendars-window-feed-update'
				,record: this.menu.record
				,listeners: {
					'success': {fn:this.refresh,scope:this}
				}
			});
		} else {
			this.updateWindow.setValues(this.menu.record);
		}
		this.updateWindow.show(e.target);
	},remove: function() {
		MODx.msg.confirm({
		    title: _('mxcalendars.feed_remove_title')
		    ,text: _('mxcalendars.feed_remove_confirm')
		    ,url: this.config.url
		    ,params: {
		        action: 'mgr/feed/remove'
		        ,id: this.menu.record.id
		    }
		    ,listeners: {
		        'success': {fn:this.refresh,scope:this}
		    }
		});
	}
});
Ext.reg('mxcalendars-grid-feeds',mxcCore.grid.feeds);


//---------------------------------------//
//-- Create the Update Feed Window --//
//---------------------------------------//
mxcCore.window.UpdateFeed = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('mxcalendars.feed_menu_update')
        ,url: mxcCore.config.connectorUrl
        ,width: 'auto'
        ,baseParams: {
            action: 'mgr/feed/update'
        }
        ,fields: [{xtype:'hidden',name:'id'},{
            xtype: 'textfield'
            ,fieldLabel:_('mxcalendars.feed_col_feed')
            ,name: 'feed'
        },
        {
            xtype: 'mxc-combo-categories',
            fieldLabel: _('mxcalendars.label_default')+' '+_('mxcalendars.categoryid_col_label'),
            name: 'defaultcategoryid',
            allowBlank: false,
            triggerAction: 'all',
            typeAhead: true,
            mode: 'remote',
            width:120,
            listWidth: 120,
            hiddenName: 'defaultcategoryid'
        },
        {
            xtype            : 'numberfield',
            fieldLabel       : _('mxcalendars.label_feedmeasurementtime'),
            name             : 'timerint',
            allowBlank       : true,
            emptyText        : '0',
            allowDecimals    : false,
            minValue         : 0,
            maxValue         : 3600,
            hiddenName: 'timerint'
        },
        {
            xtype: 'mxc-combo-measurementtype',
            fieldLabel: _('mxcalendars.label_feedmeasurementtype'),
            name: 'timermeasurement',
            allowBlank: true,
            triggerAction: 'all',
            typeAhead: true,
            mode: 'remote',
            width:120,
            listWidth: 120,
            hiddenName: 'timermeasurement'
        },
        {
            xtype: 'mxc-combo-feedtype',
            fieldLabel: _('mxcalendars.feed_col_type'),
            name: 'type',
            allowBlank: false,
            triggerAction: 'all',
            typeAhead: true,
            mode: 'local',
            width:120,
            listWidth: 120,
            hiddenName: 'type'
        }
        ,{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.feed_col_active')
            ,name: 'active'
            ,checked: true
            ,value: 1
        }]
    });
    mxcCore.window.UpdateFeed.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.UpdateFeed,MODx.Window);
Ext.reg('mxcalendars-window-feed-update',mxcCore.window.UpdateFeed);


//-------------------------------------------//
//-- Create the new Feed --//
//-------------------------------------------//
mxcCore.window.CreateFeed = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('mxcalendars.feed_btn_create')
        ,url: mxcCore.config.connectorUrl
        ,width: 'auto'
        ,baseParams: {
            action: 'mgr/feed/create'
        }
        ,fields: [{
            xtype: 'textfield'
            ,fieldLabel:_('mxcalendars.feed_col_feed')
            ,name: 'feed'
        },
        {
            xtype: 'mxc-combo-categories',
            fieldLabel: _('mxcalendars.label_default')+' '+_('mxcalendars.categoryid_col_label'),
            name: 'defaultcategoryid',
            allowBlank: false,
            triggerAction: 'all',
            typeAhead: true,
            mode: 'remote',
            width:120,
            listWidth: 120,
            hiddenName: 'defaultcategoryid'
        },
        {
            xtype            : 'numberfield',
            fieldLabel       : _('mxcalendars.label_feedmeasurementtime'),
            name             : 'timerint',
            allowBlank       : true,
            emptyText        : '0',
            allowDecimals    : false,
            minValue         : 0,
            maxValue         : 3600,
            hiddenName: 'timerint'
        },
        {
            xtype: 'mxc-combo-measurementtype',
            fieldLabel: _('mxcalendars.label_feedmeasurementtype'),
            name: 'timermeasurement',
            allowBlank: true,
            triggerAction: 'all',
            typeAhead: true,
            mode: 'remote',
            width:120,
            listWidth: 120,
            hiddenName: 'timermeasurement'
        },
        {
            xtype: 'mxc-combo-feedtype',
            fieldLabel: _('mxcalendars.feed_col_type'),
            name: 'type',
            allowBlank: false,
            editable: false,
            triggerAction: 'all',
            typeAhead: true,
            mode: 'local',
            width:120,
            listWidth: 120,
            hiddenName: 'type',
            
        }
        ,{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.feed_col_active')
            ,name: 'active'
            ,checked: true
            ,value: 1
        }]
    });
    mxcCore.window.CreateFeed.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.CreateFeed,MODx.Window);
Ext.reg('mxcalendars-window-feed-create',mxcCore.window.CreateFeed);
