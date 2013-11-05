mxcCore.grid.Videos = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'mxc-videos-grid'
        ,url: mxcCore.config.connectorUrl
        ,baseParams: { action: 'mgr/videos/getList', eventid: mxcCore.eventId }
        ,fields: ['id','event_id','video','title','description','active']
        ,paging: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'title'
        ,save_action: 'mgr/videos/updatefromgrid' // Support the inline editing
	,autosave: true // Support the inline editing
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
        },{
            header: _('mxcalendars.tab_events')+' '+_('id')
            ,dataIndex: 'event_id'
            ,sortable: true
            ,display: false
            ,hidden: true
        },{
            header: _('mxcalendars.video_filepath')
            ,dataIndex: 'video'
            ,sortable: true
            ,editor: { xtype: 'textfield' }
        },{
            header: _('mxcalendars.grid_col_title')
            ,dataIndex: 'title'
            ,sortable: true
            ,editor: { xtype: 'textfield' }
        },{
            header: _('mxcalendars.description')
            ,dataIndex: 'description'
            ,sortable: true
            ,editor: { xtype: 'textfield' }
        },{
            header: _('mxcalendars.category_active_col_label')
            ,dataIndex: 'active'
            ,sortable: true
            ,editor: { xtype: 'modx-combo-boolean', renderer: true}
        }],tbar:[{
			xtype: 'textfield'
			,id: 'mxcalendars-search-videos-filter'
			,emptyText:_('mxcalendars.default_search')
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
		   text:_('mxcalendars.btn_create_video')
		   ,handler: { xtype: 'mxcalendars-window-video-create' ,blankValues: true }
		}]
    });
    mxcCore.grid.Videos.superclass.constructor.call(this,config)
};
Ext.extend(mxcCore.grid.Videos,MODx.grid.Grid,{
    search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },getMenu: function() {
		var m = [{
			text: _('mxcalendars.menu_update')
			,handler: this.updateImage
		},'-',{
			text: _('mxcalendars.menu_remove')
			,handler: this.removeImage
		}];
		this.addContextMenuItem(m);
		return true;
	},updateImage: function(btn,e) {
		if (!this.updateImageWindow) {
			this.updateImageWindow = MODx.load({
				xtype: 'mxcalendars-window-video-update'
				,record: this.menu.record
				,listeners: {
					'success': {fn:this.refresh,scope:this}
				}
			});
		} else {
			this.updateImageWindow.setValues(this.menu.record);
		}
		this.updateImageWindow.show(e.target);
	},removeImage: function() {
		MODx.msg.confirm({
		    title: _('mxcalendars.cateogry_remove_title')
		    ,text: _('mxcalendars.cateogry_remove_confirm')
		    ,url: this.config.url
		    ,params: {
		        action: 'mgr/category/remove'
		        ,id: this.menu.record.id
		    }
		    ,listeners: {
		        'success': {fn:this.refresh,scope:this}
		    }
		});
	}
});
Ext.reg('mxc-videos-grid',mxcCore.grid.Videos);


//---------------------------------------//
//-- Create the Update Videos Window --//
//---------------------------------------//

mxcCore.window.UpdateVideo = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('mxcalendars.label_update')+' '+_('mxcalendars.label_select_video')
        ,url: mxcCore.config.connectorUrl
        ,width: 'auto'
        ,baseParams: {
            action: 'mgr/videos/update'
        }
        ,fields: [{xtype:'hidden',name:'id'},{xtype:'hidden',name:'event_id', value: mxcCore.eventId},{
            xtype: 'textfield'
            ,fieldLabel:_('mxcalendars.grid_col_title')
            ,name: 'title'
            ,anchor: '50%'
            ,width: '50%'
        },{
            xtype: 'textarea'
            ,fieldLabel: _('mxcalendars.description')
            ,name: 'description'
            ,anchor: '100%'
            ,width: '100%'
        },{
            xtype: 'textfield' //'modx-combo-browser'
            ,fieldLabel: _('mxcalendars.label_select_video')
            ,name: 'video'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.category_active_col_label')
            ,name: 'active'
            ,checked: true
            ,value: 1
        }]
    });
    mxcCore.window.UpdateVideo.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.UpdateVideo,MODx.Window);
Ext.reg('mxcalendars-window-video-update',mxcCore.window.UpdateVideo);



//-------------------------------------------//
//-- Create the object for the new video --//
//-------------------------------------------//
mxcCore.window.CreateVideo = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('mxcalendars.label_create')+' '+_('mxcalendars.label_select_video')
        ,url: mxcCore.config.connectorUrl
        ,fileUpload: true
        ,autoHeight: false
        ,height: Ext.getBody().getViewSize().height*.85
        ,width: 650
        ,autoScroll: true
        ,baseParams: {
            action: 'mgr/videos/create'
        }
        ,fields: [{xtype:'hidden',name:'event_id', value: mxcCore.eventId},{
            xtype: 'textfield'
            ,fieldLabel:_('mxcalendars.grid_col_title')
            ,name: 'title'
            ,anchor: '50%'
            ,width: '50%'
        },{
            xtype: 'textarea'
            ,fieldLabel: _('mxcalendars.description')
            ,name: 'description'
            ,anchor: '100%'
            ,width: '100%'
        },{
            xtype: 'textfield' //'modx-combo-browser'
            ,fieldLabel: _('mxcalendars.label_select_video')
            ,name: 'video'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.category_active_col_label')
            ,name: 'active'
            ,checked: true
            ,value: 1
        }]
    });
    mxcCore.window.CreateVideo.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.CreateVideo,MODx.Window);
Ext.reg('mxcalendars-window-video-create',mxcCore.window.CreateVideo);