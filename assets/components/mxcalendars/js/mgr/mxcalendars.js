var mxCalendars = function(config) {
    config = config || {};
    mxCalendars.superclass.constructor.call(this,config);
};
Ext.extend(mxCalendars,Ext.Component,{
    	initComponent: function() {
	    this.siteId;
	    this.stores = {};
	    this.ajax = new Ext.data.Connection({
			disableCaching: true,
			extraParams: {
				HTTP_MODAUTH: this.siteId
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
