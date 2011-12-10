
mxcCore.grid.events = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'mxcalendars-grid-events'
        ,url: mxcCore.config.connectorUrl
        ,baseParams: { action: 'mgr/events/getList' }
        ,fields: ['id','title','categoryid','startdate','startdate_date','startdate_time','enddate','enddate_date','enddate_time','repeating','menu','name']
        ,paging: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'name'
        ,save_action: 'mgr/events/updatefromgrid' // Support the inline editing
	,autosave: true // Support the inline editing
        ,columns: [
			// add the grid columns to the display
			 {header: _('id'),dataIndex: 'id',sortable: true}
			,{header: _('mxcalendars.name'),dataIndex: 'title',sortable: true,editor: { xtype: 'textfield' } }
			,{header: _('mxcalendars.categoryid_col_label'),dataIndex: 'name',sortable: true}
			//,{header: _('mxcalendars.startdate_col_label'),dataIndex: 'startdate',sortable: true}
			,{header: _('mxcalendars.startdate_col_label'),dataIndex: 'startdate_date',sortable: true}
			,{header: _('mxcalendars.startdate_col_label'), dataIndex: 'startdate_time', sortable:true,editor:{ xtype:'timefield'} }
			,{header: _('mxcalendars.enddate_col_label'),dataIndex: 'enddate_date',sortable: true}
			,{header: _('mxcalendars.enddate_col_label'),dataIndex: 'enddate_time',sortable: true,editor:{ xtype:'timefield'}}
			,{header: _('mxcalendars.repeating_col_label'),dataIndex: 'repeating',sortable: true}

		],tbar:[{
			xtype: 'textfield'
			,id: 'mxcalendars-search-filter'
			,emptyText:_('mxcalendars.search_default_text')
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
		},{
		   text:_('mxcalendars.btn_create')
		   ,handler: { xtype: 'mxcalendars-window-mxcalendar-create',blankValues: true }
		}]
    });
    mxcCore.grid.events.superclass.constructor.call(this,config)
};
Ext.extend(mxcCore.grid.events,MODx.grid.Grid,{
    search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },getMenu: function() {
		var m = [{
			text: _('mxcalendars.mxcalendars_update')
			,handler: this.updateCal
		},'-',{
			text: _('mxcalendars.mxcalendars_remove')
			,handler: this.removeCal
		}];
		this.addContextMenuItem(m);
		return true;
	},updateCal: function(btn,e) {
		if (!this.updateCalWindow) {
			this.updateCalWindow = MODx.load({
				xtype: 'mxcalendars-window-mxcalendar-update'
				,record: this.menu.record
				,listeners: {
					'success': {fn:this.refresh,scope:this}
				}
			});
		} else {
			this.updateCalWindow.setValues(this.menu.record);
		}
		this.updateCalWindow.show(e.target);
	},removeCal: function() {
		MODx.msg.confirm({
		    title: _('mxcalendars.mxcalendars_remove')
		    ,text: _('mxcalendars.mxcalendars_remove_confirm')
		    ,url: this.config.url
		    ,params: {
		        action: 'mgr/events/remove'
		        ,id: this.menu.record.id
		    }
		    ,listeners: {
		        'success': {fn:this.refresh,scope:this}
		    }
		});
	}
});
Ext.reg('mxcalendars-grid-events',mxcCore.grid.events);


mxcCore.window.CreateCal = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('mxcalendars.event_title_create')
        ,url: mxcCore.config.connectorUrl
        ,width: 'auto'
        ,baseParams: {
            action: 'mgr/events/create'
        }
	
	,bodyStyle: 'padding:15px'
        ,border: false
	,plain:true
        ,fieldDefaults: {
            //msgTarget: 'side'
        }
	,defaults: {
	    // applied to each contained item
	    // nothing this time
	    anchor:'100%'
	    ,layout: 'Form'
	    ,labelWidth: '100'
	    ,cellCls: 'valign-center'
	}
	,items:
	[   {
	    xtype: 'container',
	    anchor: '0',
	    layout: 'column',
	    defaultType: 'container',
	    defaults: {
		  layout:'form',
		  defaultType: 'textfield',
		  labelAlign: 'left',
		  anchor: '0'
		  //,msgTarget: 'side'
		},
	    items: [
		{
		  // Left Column
		  columnWidth: .5,
		  //fields: [{fieldLabel:'Title',name:'name'}],
		  items: [{
		    fieldLabel: _('mxcalendars.label_title'),
		    name: 'name',
		    id: 'name',
		    allowBlank: false,
		    anchor:'100%'
		  },{
		      xtype: 'combo',
		      displayField: 'name',
		      valueField: 'id',
		      forceSelection: true,
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
		      }),
		      mode: 'remote',
		      triggerAction: 'all',
		      fieldLabel: 'Category',
		      id: 'category-id',
		      name: 'categoryid',
		      allowBlank: false,
		      typeAhead:true,
		      minChars:1,
		      emptyText:'Select a category',
		      valueNotFoundText:'Select a valid category',
		      anchor:'100%'
		    }]
		},{
		  // Right Column
		  columnWidth: .5,
		  items: [{
		    xtype: 'container',
		    fieldLabel: _('mxcalendars.label_startdate'),
		    combineErrors: true,
		    msgTarget : 'side',
		    layout: 'hbox',
		    defaults: {
			flex: 1,
			hideLabel: true
		    },
		    items: [
			{
			    xtype     : 'datefield',
			    name      : 'startdate_date',
			    id: 'startdate_date',
			    fieldLabel: 'Start',
			    padding: '0 5 0 0',
			    allowBlank: false,
			    width: 100
			},
			{
			    xtype     : 'timefield',
			    name      : 'startdate_time',
			    id: 'startdate_time',
			    fieldLabel: 'Time',
			    margin: '0 5 0 0',
			    allowBlank: false,
			    width: 100
			    
			}
		    ]
		  },{
		    xtype: 'container',
		    fieldLabel: _('mxcalendars.label_enddate'),
		    combineErrors: true,
		    msgTarget : 'side',
		    layout: 'hbox',
		    defaults: {
			flex: 1,
			hideLabel: true
		    },
		    items: [
			{
			    xtype     : 'datefield',
			    name      : 'enddate_date',
			    fieldLabel: 'End',
			    padding: '0 5 0 0',
			    allowBlank: false,
			    width: 100
			},
			{
			    xtype     : 'timefield',
			    name      : 'enddate_time',
			    fieldLabel: 'End Time',
			    margin: '0 5 0 0',
			    allowBlank: false,
			    width: 100
			    
			}
		    ]
		  }
		    /*,{
		      xtype: 'combo',
		      displayField: 'name',
		      valueField: 'id',
		      forceSelection: true,
		      store: new Ext.data.JsonStore({
			      root: 'results',
			      idProperty: 'id',
			      url: mxcCore.config.connectorUrl,
			      baseParams: {
				      action: 'stores/chunks' 
			      },
			      fields: [
				      'id', 'name', 'description'
			      ]
		      }),
		      mode: 'remote',
		      triggerAction: 'all',
		      fieldLabel: 'Outer wrapper chunk',
		      name: 'categoryid',
		      allowBlank: false,
		      typeAhead:true,
		      emptyText:'Select a chunk...',
		      valueNotFoundText:'Select a valid chunk',
		      //renderTo:'results'
		      anchor:'100%'
		    }*/]
	    }]
	    },
	    {
		xtype:'fieldset',
		checkboxToggle:true,
		title: 'Repeating Event',
		defaultType: 'textfield',
		collapsed: true,
		autoHeight: true,
		defaults: {},
		items :[{
		    fieldLabel: 'Occurs'
		    ,name: 'occurance'
		    ,xtype:'combo'
		    ,mode: 'local'
		    ,store: new Ext.data.ArrayStore({
			    id: 0,
			    fields: ['measure'],
			    data: [['Daily'],['Weekly'],['Monthly'],['Yearly']]
		    })
		    ,triggerAction: 'all'
		    ,displayField: 'measure'
		    ,valueField: 'measure'
		    ,editable: true
		    //,anchor: '100%'
		    ,width: 200
		    //,listeners:{select:function() {var numberOfDays = ReportForm.form.findField('DateRangeCombo').getValue();var newDate = DateAdd('05/04/2011', 'D', (numberOfDays * -1)); ReportForm.form.findField('StartDate').setValue(newDate);}}
		},{
		    fieldLabel: 'Occurs on'
		    ,name: 'occurance_on'
		    ,xtype: 'checkboxgroup'
		    ,items: [
			{boxLabel: 'Sunday', name: 'cb-auto-1'},
			{boxLabel: 'Monday', name: 'cb-auto-2', checked: true},
			{boxLabel: 'Tuesday', name: 'cb-auto-3'},
			{boxLabel: 'Wednesday', name: 'cb-auto-4'},
			{boxLabel: 'Thursday', name: 'cb-auto-5'},
			{boxLabel: 'Friday', name: 'cb-auto-6'},
			{boxLabel: 'Saturday', name: 'cb-auto-7'}
		    ]
		},{
		    fieldLabel: 'Repeat Every'
		    ,name: 'repeatevery'
		    ,xtype: 'combo'
		    ,mode: 'local'
		    ,store: new Ext.data.ArrayStore({
			    id: 0,
			    fields: ['counter'],
			    data: [[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23],[24],[25],[26],[27],[28],[29],[30]]
		    })
		    ,displayField: 'counter'
		    ,valueField: 'counter'
		    ,editable: true
		    ,width: 'auto'
		    ,anchor: '20%'
		},{
		    fieldLabel: 'Last Occurance'
		    ,name: 'repeatenddate'
		    ,allowBlank:false
		    ,xtype: 'datefield'
		}]
	    },{
		xtype: 'container'
		,anchor: '100%'
		,layout: 'form'
		,items: [
		    {
			xtype:'tabpanel'
			,plain:true
			,activeTab: 0
			,height:235
			//,autoHeight: true
			,width:840
			,defaults:{bodyStyle:'padding:10px',layout:'form'}
			,items:[
			    {
				cls: 'x-plain',
				title: 'Description',
				layout: 'fit',
				items: {
				    //anchor: '100%'
				    xtype: 'htmleditor'
				    ,name: 'content'
				    //,fieldLabel: 'Biography'
				}
			    },{
				title: 'Location'
				,defaults: {width:230}
				,defaultType:'textfield'
				,items:[{
				    fieldLabel: 'Name'
				    ,name:'name'
				},{
				    fieldLabel: 'Address'
				    ,name:'address'
				},{
				    fieldLabel: 'Display Map'
				    ,name:'map'
				    ,xtype:'checkbox'
				}]
			    },{
				title:'Link',
				defaults: {width: 230},
				defaultType: 'textfield',
				items: [{
				    fieldLabel: 'Link'
				    ,name: 'link'
				    ,allowBlank:false
				    //value: ''
				},{
				    fieldLabel: 'Link Rel'
				    ,name: 'linkrel'
				    //,value: ''
				},{
				    fieldLabel: 'Link Target'
				    ,name: 'linktarget'
				    //,value: ''
				}, {
				    fieldLabel: 'Location'
				    ,name: 'email'
				    ,vtype:'email'
				}]
			    }
			]
		    }
		]
	    }
	]
	
	,buttons: [{
	    text: 'Reset'
	    ,type: 'reset'
	    ,handler: function()
	    {
		//mxcCore.window.CreateCal.getForm().reset();
		Ext.Msg.alert('Form reset', 'Your form has been reset.');
	    }
	 },{
	    text: 'Save'
	    ,handler: function(){
		var frmData = {
			oldField: 'test',
			isUpdate: 'next test',
			name: Ext.getCmp('name').getValue(),
			startdate_date: Ext.getCmp('startdate_date').getValue(),
			startdate_time: Ext.getCmp('startdate_time').getValue()
			//mandatory: Ext.getCmp('extra-mandatory').getEl().dom.checked,
			//action: 'mgr/categories/addextrafield',
			//categoryId: this.treeMenu.baseParams.parent
		};
		
		mxcCore.ajax.request({
			url: mxcCore.config.connectorUrl,
			baseParams: {
			    action: 'mgr/events/create'
			},
			params: frmData,
			scope: this,
			success: function() {
				/*
				this.extraFieldsGrid.getStore().load();
				Ext.getCmp('extra-name').setValue('');
				Ext.getCmp('extra-type').setValue('');
				Ext.getCmp('extra-values').setValue('');
				Ext.getCmp('extra-mandatory').setValue(false);

				Ext.getCmp('extra-name').reset();
				Ext.getCmp('extra-type').reset();
				Ext.getCmp('extra-values').reset();
				Ext.getCmp('extra-mandatory').reset();
				
				if (isUpdate) {
					vcCore.showMessage('Field updated.');
				} else {
					vcCore.showMessage('Field saved.');
				}*/
				Ext.Msg.alert('Submit New Event', 'Your event data is: ');
				
			}
		});
		
	    }
	}]

	});
    mxcCore.window.CreateCal.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.CreateCal,MODx.Window);
Ext.reg('mxcalendars-window-mxcalendar-create',mxcCore.window.CreateCal);

mxcCore.window.UpdateCal = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('mxcalendars.event_title_update')
        ,url: mxcCore.config.connectorUrl
        ,width: 'auto'
        ,baseParams: {
            action: 'mgr/events/update'
        }
        ,border: false
	,bodyStyle: 'padding:15px'
	,plain:true
        ,fieldDefaults: {
            //msgTarget: 'side'
        }
		,defaults: {
		    // applied to each contained item
		    // nothing this time
		    anchor:'100%'
		    ,layout: 'Form'
		    ,labelWidth: '100'
		    ,cellCls: 'valign-center'
		}
		,items:
		[{
		    xtype: 'container',
		    anchor: '0',
		    layout: 'column',
		    defaultType: 'container',
		    defaults: {
			  layout:'form',
			  defaultType: 'textfield',
			  labelAlign: 'left',
			  anchor: '0'
			  //,msgTarget: 'side'
			},
		    items: [
			{
			  // Left Column
			  columnWidth: .5,
			  items: [{
			    xtype: 'hidden'
			    ,name:'id'
			  },{	
			    fieldLabel: _('mxcalendars.label_title'),
			    name: 'field1',
			    //allowBlank: false,
			    anchor:'100%'
			  },{
			      xtype: 'combo',
			      displayField: 'name',
			      valueField: 'id',
			      forceSelection: true,
			      store: new Ext.data.JsonStore({
				      root: 'results',
				      idProperty: 'id',
				      url: mxcCore.config.connectorUrl,
				      baseParams: {
					    action: 'stores/chunks' 
				      },
				      fields: [
					    'id', 'name', 'description'
				      ]
			      }),
			      mode: 'remote',
			      triggerAction: 'all',
			      fieldLabel: 'Category',
			      id: 'category-id',
			      name: 'categoryid',
			      allowBlank: false,
			      typeAhead:true,
			      minChars:1,
			      emptyText:'Select a category',
			      valueNotFoundText:'Select a valid category',
			      anchor:'100%'
			    }]
			},{
			  // Right Column
			  columnWidth: .5,
			  items: [{
			    xtype: 'container',
			    fieldLabel: _('mxcalendars.label_startdate'),
			    combineErrors: true,
			    msgTarget : 'side',
			    layout: 'hbox',
			    defaults: {
				flex: 1,
				hideLabel: true
			    },
			    items: [
				{
				    xtype     : 'datefield',
				    name      : 'startdate_date',
				    fieldLabel: 'Start',
				    padding: '0 5 0 0',
				    allowBlank: false,
				    width: 100
				},
				{
				    xtype     : 'timefield',
				    name      : 'startdate_time',
				    fieldLabel: 'Time',
				    margin: '0 5 0 0',
				    allowBlank: false,
				    width: 100
				    
				}
			    ]
			  },{
			    xtype: 'container',
			    fieldLabel: _('mxcalendars.label_enddate'),
			    combineErrors: true,
			    msgTarget : 'side',
			    layout: 'hbox',
			    defaults: {
				flex: 1,
				hideLabel: true
			    },
			    items: [
				{
				    xtype     : 'datefield',
				    name      : 'enddate_date',
				    fieldLabel: 'End',
				    padding: '0 5 0 0',
				    allowBlank: false,
				    width: 100
				},
				{
				    xtype     : 'timefield',
				    name      : 'enddate_time',
				    fieldLabel: 'End Time',
				    margin: '0 5 0 0',
				    allowBlank: false,
				    width: 100
				    
				}
			    ]
			  }
			    /*,{
			      xtype: 'combo',
			      displayField: 'name',
			      valueField: 'id',
			      forceSelection: true,
			      store: new Ext.data.JsonStore({
				      root: 'results',
				      idProperty: 'id',
				      url: mxcCore.config.connectorUrl,
				      baseParams: {
					      action: 'stores/chunks' 
				      },
				      fields: [
					      'id', 'name', 'description'
				      ]
			      }),
			      mode: 'remote',
			      triggerAction: 'all',
			      fieldLabel: 'Outer wrapper chunk',
			      name: 'categoryid',
			      allowBlank: false,
			      typeAhead:true,
			      emptyText:'Select a chunk...',
			      valueNotFoundText:'Select a valid chunk',
			      //renderTo:'results'
			      anchor:'100%'
			    }*/]
		    }]
		    },
		    {
			xtype:'fieldset',
			checkboxToggle:true,
			title: 'Repeating Event',
			defaultType: 'textfield',
			collapsed: false,
			autoHeight: true,
			defaults: {},
			items :[{
			    fieldLabel: 'Occurs'
			    ,name: 'occurance'
			    ,xtype:'combo'
			    ,mode: 'local'
			    ,store: new Ext.data.ArrayStore({
				    id: 0,
				    fields: ['measure'],
				    data: [['Daily'],['Weekly'],['Monthly'],['Yearly']]
			    })
			    ,triggerAction: 'all'
			    ,displayField: 'measure'
			    ,valueField: 'measure'
			    ,editable: true
			    //,anchor: '100%'
			    ,width: 200
			    //,listeners:{select:function() {var numberOfDays = ReportForm.form.findField('DateRangeCombo').getValue();var newDate = DateAdd('05/04/2011', 'D', (numberOfDays * -1)); ReportForm.form.findField('StartDate').setValue(newDate);}}
			},{
			    fieldLabel: 'Occurs on'
			    ,name: 'occurance_on'
			    ,xtype: 'checkboxgroup'
			    ,items: [
				{boxLabel: 'Sunday', name: 'cb-auto-1'},
				{boxLabel: 'Monday', name: 'cb-auto-2', checked: true},
				{boxLabel: 'Tuesday', name: 'cb-auto-3'},
				{boxLabel: 'Wednesday', name: 'cb-auto-4'},
				{boxLabel: 'Thursday', name: 'cb-auto-5'},
				{boxLabel: 'Friday', name: 'cb-auto-6'},
				{boxLabel: 'Saturday', name: 'cb-auto-7'}
			    ]
			},{
			    fieldLabel: 'Repeat Every'
			    ,name: 'repeatevery'
			    ,xtype: 'combo'
			    ,mode: 'local'
			    ,store: new Ext.data.ArrayStore({
				    id: 0,
				    fields: ['counter'],
				    data: [[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23],[24],[25],[26],[27],[28],[29],[30]]
			    })
			    ,displayField: 'counter'
			    ,valueField: 'counter'
			    ,editable: true
			    ,width: 'auto'
			    ,anchor: '20%'
			},{
			    fieldLabel: 'Last Occurance'
			    ,name: 'repeatenddate'
			    ,allowBlank:false
			    ,xtype: 'datefield'
			}]
		    },{
			xtype: 'container'
			,anchor: '100%'
			,layout: 'form'
			,items: [
			    {
				xtype:'tabpanel'
				,plain:true
				,activeTab: 0
				,height:235
				//,autoHeight: true
				,width:840
				,defaults:{bodyStyle:'padding:10px',layout:'form'}
				,items:[
				    {
					cls: 'x-plain',
					title: 'Description',
					layout: 'fit',
					items: {
					    //anchor: '100%'
					    xtype: 'htmleditor'
					    ,name: 'content'
					    //,fieldLabel: 'Biography'
					}
				    },{
					title: 'Location'
					,defaults: {width:230}
					,defaultType:'textfield'
					,items:[{
					    fieldLabel: 'Name'
					    ,name:'name'
					},{
					    fieldLabel: 'Address'
					    ,name:'address'
					},{
					    fieldLabel: 'Display Map'
					    ,name:'map'
					    ,xtype:'checkbox'
					}]
				    },{
					title:'Link',
					defaults: {width: 230},
					defaultType: 'textfield',
					items: [{
					    fieldLabel: 'Link'
					    ,name: 'link'
					    ,allowBlank:false
					    //value: ''
					},{
					    fieldLabel: 'Link Rel'
					    ,name: 'linkrel'
					    //,value: ''
					},{
					    fieldLabel: 'Link Target'
					    ,name: 'linktarget'
					    //,value: ''
					}, {
					    fieldLabel: 'Location'
					    ,name: 'email'
					    ,vtype:'email'
					}]
				    }
				]
			    }
			]
		    }
		]
	});
    mxcCore.window.UpdateCal.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.UpdateCal,MODx.Window);
Ext.reg('mxcalendars-window-mxcalendar-update',mxcCore.window.UpdateCal);
