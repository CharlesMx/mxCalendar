var mxCalendars = function(config) {
    config = config || {};
    mxCalendars.superclass.constructor.call(this,config);
};
Ext.extend(mxCalendars,Ext.Component,{
    	initComponent: function() {
	    //this.siteId = iteId;
	    this.stores = {};
	    this.ajax = new Ext.data.Connection({
			disableCaching: true,
			extraParams: {
				//HTTP_MODAUTH: this.siteId
			}
		});
    	},
    	page:{},
    	window:{},
    	grid:{},
    	tree:{},
    	panel:{},
    	combo:{},
    	config: {}
});
Ext.reg('mxcalendars',mxCalendars);
mxcCore = new mxCalendars();


mxcCore.combo.Categories = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.JsonStore({
              root: 'results',
              idProperty: 'id',
              url: mxcCore.config.connectorUrl,
              baseParams: {
                    action: 'stores/getcategories'  
              },
              fields: [
                    'id', 'name'
              ]
        })
        ,mode: 'remote'
        ,displayField: 'name'
        ,valueField: 'id'
    });
    mxcCore.combo.Categories.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.combo.Categories,MODx.combo.ComboBox);
Ext.reg('mxc-combo-categories',mxcCore.combo.Categories);


mxcCore.combo.Section = function(config) {
    config = config || {};
    Ext.applyIf(config,{
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
        })
        ,mode: 'remote'
        ,displayField: 'name'
        ,valueField: 'id'
        ,emptyText:_('mxcalendars.label_select_calendar')
    });
    mxcCore.combo.Section.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.combo.Section,MODx.combo.ComboBox);
Ext.reg('mxc-combo-calendar',mxcCore.combo.Section);



mxcCore.combo.Section = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.JsonStore({
              root: 'results',
              idProperty: 'key',
              url: mxcCore.config.connectorUrl,
              baseParams: {
                    action: 'stores/getcontexts'  
              },
              fields: [
                    'key',
              ]
        })
        ,mode: 'remote'
        ,displayField: 'key'
        ,valueField: 'key'
        ,emptyText:_('mxcalendars.label_select_context')
    });
    mxcCore.combo.Section.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.combo.Section,MODx.combo.ComboBox);
Ext.reg('mxc-combo-context',mxcCore.combo.Section);