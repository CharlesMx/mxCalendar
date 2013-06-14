mxcCore.grid.categories = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'mxcalendars-grid-categories'
        ,url: mxcCore.config.connectorUrl
        ,baseParams: { action: 'mgr/category/getList' }
        ,fields: ['id','isdefault','name','foregroundcss','backgroundcss','inlinecss','disable','active','menu']
        ,paging: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'name'
        ,save_action: 'mgr/category/updatefromgrid' // Support the inline editing
	,autosave: true // Support the inline editing
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
        },{
            header: _('mxcalendars.category_isdefault_col_label')
            ,dataIndex: 'isdefault'
            ,sortable: true
            ,editor: { xtype: 'modx-combo-boolean', renderer: true}
        },{
            header: _('mxcalendars.category_name_col_label')
            ,dataIndex: 'name'
            ,sortable: true
            ,editor: { xtype: 'textfield' }
        },{
            header: _('mxcalendars.category_foregroundcss_col_label')
            ,dataIndex: 'foregroundcss'
            ,sortable: true
            ,editor: { xtype: 'textfield' }
        },{
            header: _('mxcalendars.category_backgroundcss_col_label')
            ,dataIndex: 'backgroundcss'
            ,sortable: true
            ,editor: { xtype: 'textfield' }
        },{
            header: _('mxcalendars.category_inlinecss_col_label')
            ,dataIndex: 'inlinecss'
            ,sortable: true
            ,editor: { xtype: 'textfield' }
        },{
            header: _('mxcalendars.category_disabled_col_label')
            ,dataIndex: 'disable'
            ,sortable: true
            ,editor: { xtype: 'modx-combo-boolean', renderer: true}
        },{
            header: _('mxcalendars.category_active_col_label')
            ,dataIndex: 'active'
            ,sortable: true
            ,editor: { xtype: 'modx-combo-boolean', renderer: true}
        }],tbar:[{
			xtype: 'textfield'
			,id: 'mxcalendars-search-categories-filter'
			,emptyText:_('mxcalendars.default_category_search')
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
		   text:_('mxcalendars.btn_create_cat')
		   ,handler: { xtype: 'mxcalendars-window-category-create' ,blankValues: true }
		}]
    });
    mxcCore.grid.categories.superclass.constructor.call(this,config)
};
Ext.extend(mxcCore.grid.categories,MODx.grid.Grid,{
    search: function(tf,nv,ov) {textfield
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },getMenu: function() {
		var m = [{
			text: _('mxcalendars.menu_update_category')
			,handler: this.updateCat
		},'-',{
			text: _('mxcalendars.menu_remove_category')
			,handler: this.removeCat
		}];
		this.addContextMenuItem(m);
		return true;
	},updateCat: function(btn,e) {
		if (!this.updateCatWindow) {
			this.updateCatWindow = MODx.load({
				xtype: 'mxcalendars-window-category-update'
				,record: this.menu.record
				,listeners: {
					'success': {fn:this.refresh,scope:this}
				}
			});
		} else {
			this.updateCatWindow.setValues(this.menu.record);
		}
		this.updateCatWindow.show(e.target);
	},removeCat: function() {
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
Ext.reg('mxcalendars-grid-categories',mxcCore.grid.categories);


//---------------------------------------//
//-- Create the Update Category Window --//
//---------------------------------------//
mxcCore.window.UpdateCat = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: ''+_('mxcalendars.label_window_create')
        ,url: mxcCore.config.connectorUrl
        ,width: 'auto'
        ,baseParams: {
            action: 'mgr/category/update'
        }
        ,fields: [{xtype:'hidden',name:'id'},{
            xtype: 'textfield'
            ,fieldLabel:_('mxcalendars.category_name_col_label')
            ,name: 'name'
        }/*,{
          xtype: 'combo',
          displayField: 'name',
          valueField: 'id',
          forceSelection: true,
          store: new Ext.data.JsonStore({
                  root: 'results',
                  idProperty: 'id',
                  url: mxcCore.config.connectorUrl,
                  baseParams: {
                        action: 'stores/getcalendars' 
                  },
                  fields: [
                        'id', 'name'
                  ]
          }),
          mode: 'remote',
          triggerAction: 'all',
          fieldLabel: _('mxcalendars.grid_col_calendar'),
          name: 'calendarid',
          hiddenName: 'calendarid',
          id: 'ucalendarid',
          allowBlank: false,
          typeAhead:true,
          minChars:1,
          emptyText:_('mxcalendars.label_select_calendar'),
          valueNotFoundText:_('mxcalendars.label_select_calendar_err'),
          anchor:'100%',
          value: config.record.calendarid
        }*/,{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.category_isdefault_col_label')
            ,name: 'isdefault'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('mxcalendars.category_foregroundcss_col_label')
            ,name: 'foregroundcss'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('mxcalendars.category_backgroundcss_col_label')
            ,name: 'backgroundcss'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('mxcalendars.category_inlinecss_col_label')
            ,name: 'inlinecss'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.category_disabled_col_label')
            ,name: 'disable'
            ,checked: false
            ,value: 1
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.category_active_col_label')
            ,name: 'active'
            ,checked: true
            ,value: 1
        }]
    });
    mxcCore.window.UpdateCat.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.UpdateCat,MODx.Window);
Ext.reg('mxcalendars-window-category-update',mxcCore.window.UpdateCat);


//-------------------------------------------//
//-- Create the object for th new category --//
//-------------------------------------------//
mxcCore.window.CreateCat = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: ''+_('mxcalendars.label_window_create')
        ,url: mxcCore.config.connectorUrl
        ,width: 'auto'
        ,baseParams: {
            action: 'mgr/category/create'
        }
        ,fields: [{
            xtype: 'textfield'
            ,fieldLabel:_('mxcalendars.category_name_col_label')
            ,name: 'name'
        }/*,{
          xtype: 'combo',
          displayField: 'name',
          valueField: 'id',
          forceSelection: true,
          store: new Ext.data.JsonStore({
                  root: 'results',
                  idProperty: 'id',
                  url: mxcCore.config.connectorUrl,
                  baseParams: {
                        action: 'stores/getcalendars' 
                  },
                  fields: [
                        'id', 'name'
                  ]
          }),
          mode: 'remote',
          triggerAction: 'all',
          fieldLabel: _('mxcalendars.grid_col_calendar'),
          name: 'calendarid',
          hiddenName: 'calendarid',
          id: 'calendarid',
          allowBlank: false,
          typeAhead:true,
          minChars:1,
          emptyText:_('mxcalendars.label_select_calendar'),
          valueNotFoundText:_('mxcalendars.label_select_calendar_err'),
          anchor:'100%'
        }*/,{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.category_isdefault_col_label')
            ,name: 'isdefault'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('mxcalendars.category_foregroundcss_col_label')
            ,name: 'foregroundcss'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('mxcalendars.category_backgroundcss_col_label')
            ,name: 'backgroundcss'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('mxcalendars.category_inlinecss_col_label')
            ,name: 'inlinecss'
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.category_disabled_col_label')
            ,name: 'disable'
            ,checked: false
            ,value: 1
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('mxcalendars.category_active_col_label')
            ,name: 'active'
            ,checked: true
            ,value: 1
        }]
    });
    mxcCore.window.CreateCat.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.CreateCat,MODx.Window);
Ext.reg('mxcalendars-window-category-create',mxcCore.window.CreateCat);
