Ext.onReady(function() {
    MODx.load({ xtype: 'mxcalendars-page-home'});
	
	// Load the datastore for the chunks
	//mxcCore.stores.chunks.load();
});
 
mxcCore.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'mxcalendars-panel-home'
            ,renderTo: 'mxcalendars-panel-home-div'
        }]
    });
    mxcCore.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.page.Home,MODx.Component);
Ext.reg('mxcalendars-page-home',mxcCore.page.Home);
