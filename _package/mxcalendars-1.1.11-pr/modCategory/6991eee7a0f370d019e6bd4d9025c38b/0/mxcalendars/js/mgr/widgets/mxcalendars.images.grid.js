mxcCore.grid.Images = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'mxc-images-grid'
        ,url: mxcCore.config.connectorUrl
        ,baseParams: { action: 'mgr/images/getList', eventid: mxcCore.eventId }
        ,fields: ['id','event_id','filepath','title','description','active']
        ,paging: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'title'
        ,save_action: 'mgr/images/updatefromgrid' // Support the inline editing
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
            header: _('mxcalendars.image_filepath')
            ,dataIndex: 'filepath'
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
			,id: 'mxcalendars-search-images-filter'
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
		   text:_('mxcalendars.btn_create_image')
		   ,handler: { xtype: 'mxcalendars-window-image-create' ,blankValues: true }
		}]
    });
    mxcCore.grid.Images.superclass.constructor.call(this,config)
};
Ext.extend(mxcCore.grid.Images,MODx.grid.Grid,{
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
				xtype: 'mxcalendars-window-image-update'
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
Ext.reg('mxc-images-grid',mxcCore.grid.Images);


//---------------------------------------//
//-- Create the Update Image Window --//
//---------------------------------------//

mxcCore.window.UpdateImage = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('mxcalendars.label_update')+' '+_('mxcalendars.label_select_image')
        ,url: mxcCore.config.connectorUrl
        ,width: 'auto'
        ,baseParams: {
            action: 'mgr/images/update'
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
            xtype: 'modx-combo-browser'
            ,fieldLabel: _('mxcalendars.label_select_image')
            ,name: 'filepath'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.category_active_col_label')
            ,name: 'active'
            ,checked: true
            ,value: 1
        }]
    });
    mxcCore.window.UpdateImage.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.UpdateImage,MODx.Window);
Ext.reg('mxcalendars-window-image-update',mxcCore.window.UpdateImage);



//-------------------------------------------//
//-- Create the object for the new image --//
//-------------------------------------------//
mxcCore.window.CreateImage = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('mxcalendars.label_create')+' '+_('mxcalendars.label_select_image')
        ,url: mxcCore.config.connectorUrl
        ,fileUpload: true
        ,autoHeight: false
        ,height: Ext.getBody().getViewSize().height*.85
        ,width: 650
        ,autoScroll: true
        ,baseParams: {
            action: 'mgr/images/create'
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
            xtype: 'modx-combo-browser'
            ,fieldLabel: _('mxcalendars.label_select_image')
            ,name: 'filepath'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.category_active_col_label')
            ,name: 'active'
            ,checked: true
            ,value: 1
        }]
    });
    mxcCore.window.CreateImage.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.CreateImage,MODx.Window);
Ext.reg('mxcalendars-window-image-create',mxcCore.window.CreateImage);