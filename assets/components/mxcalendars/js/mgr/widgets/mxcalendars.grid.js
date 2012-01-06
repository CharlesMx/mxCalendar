
mxcCore.grid.events = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'mxcalendars-grid-events'
        ,url: mxcCore.config.connectorUrl
        ,baseParams: {action: 'mgr/events/getList'}
        ,fields: ['id','description','title','categoryid','startdate','startdate_date','startdate_time','enddate','enddate_date','enddate_time','repeating','repeattype','repeaton','repeatfrequency','repeatenddate','repeatdates','menu','name','map','link','linkrel','linktarget','location_name','location_address','address']
        ,paging: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'name'
        ,save_action: 'mgr/events/updatefromgrid' // Support the inline editing
	,autosave: true // Support the inline editing
        ,columns: [
			// add the grid columns to the display
			 {header: _('id'),dataIndex: 'id',sortable: true}
			,{header: _('mxcalendars.name'),dataIndex: 'title',sortable: true,editor: {xtype: 'textfield'}}
			,{header: _('mxcalendars.categoryid_col_label'),dataIndex: 'name',sortable: true}
			//,{header: _('mxcalendars.startdate_col_label'),dataIndex: 'startdate',sortable: true}
			,{header: _('mxcalendars.startdate_col_label'),dataIndex: 'startdate_date',sortable: true}
			,{header: _('mxcalendars.startdate_col_label'), dataIndex: 'startdate_time', sortable:true,editor:{xtype:'timefield'}}
			,{header: _('mxcalendars.enddate_col_label'),dataIndex: 'enddate_date',sortable: true}
			,{header: _('mxcalendars.enddate_col_label'),dataIndex: 'enddate_time',sortable: true,editor:{xtype:'timefield'}}
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
		},'->',{
                    text:'Show Past Events'
                    ,id:'pastbnt'
                    ,visible:true
                    ,handler: function(){
                        var s = this.getStore();
                        s.baseParams.historical = 1;
                        this.getBottomToolbar().changePage(1);
                        this.refresh();
                        Ext.getCmp('pastbnt').hide();
                        Ext.getCmp('futurebnt').show();
                    }
                },{
                    text:'Show Upcoming Events'
                    ,id:'futurebnt'
                    ,hidden:true
                    ,handler: function(){
                        var s = this.getStore();
                        s.baseParams.historical = 0;
                        this.getBottomToolbar().changePage(1);
                        this.refresh();
                        Ext.getCmp('futurebnt').hide();
                        Ext.getCmp('pastbnt').show();
                    }
                },'-',{
		   text:_('mxcalendars.btn_create')
		   ,handler: {xtype: 'mxcalendars-window-mxcalendar-create',blankValues: true}
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
		if (this.updateCalWindow) {
                    this.updateCalWindow.close();
		}
                this.updateCalWindow = MODx.load({
                    xtype: 'mxcalendars-window-mxcalendar-update'
                    ,record: this.menu.record
                    ,listeners: {
                        'success': {fn:this.refresh,scope:this}
                    }
                });
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
        ,id: 'createAle'
        ,url: mxcCore.config.connectorUrl
        ,width: 'auto'
        ,baseParams: {
            action: 'mgr/events/create'
        }
        ,closeAction: 'hide'
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
			    fieldLabel: _('mxcalendars.label_title'),
			    name: 'title',
                            hiddenname: 'title',
                            id: 'ctitle',
			    allowBlank: false,
			    anchor:'100%',
                            value: config.record.title
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
			      name: 'categoryid',
                              hiddenName: 'categoryid',
                              id: 'ccategoryid',
			      allowBlank: false,
			      typeAhead:true,
			      minChars:1,
			      emptyText:'Select a category',
			      valueNotFoundText:'Select a valid category',
			      anchor:'100%',
                              value: config.record.categoryid
			    }]
			},{
			  // Right Column
			  columnWidth: .5,
			  items: [{
			    xtype: 'container',
                            labelWidth: '250',
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
                                    id        : 'cstartdate_date',
                                    format    : 'm-d-Y',
				    fieldLabel: 'Start',
				    padding: '0 5 0 0',
				    allowBlank: false,
				    width: 150,
                                    value: config.record.startdate_date
				},
				{
				    xtype     : 'timefield',
				    name      : 'startdate_time',
                                    id        : 'cstartdate_time',
				    fieldLabel: 'Time',
				    margin: '0 5 0 0',
				    allowBlank: false,
				    width: 120,
				    value: config.record.startdate_time
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
                                    id: 'cenddate_date',
                                    format    : 'm-d-Y',
				    fieldLabel: 'End',
				    padding: '0 5 0 0',
				    allowBlank: false,
				    width: 150,
                                    value: config.record.enddate_date
				},
				{
				    xtype     : 'timefield',
				    name      : 'enddate_time',
                                    id        : 'cenddate_time',
				    fieldLabel: 'End Time',
				    margin: '0 5 0 0',
				    allowBlank: false,
				    width: 120,
				    value: config.record.enddate_time
				}
			    ]
			  }]
		    }]
		    },
		    {
			xtype:'fieldset',
			checkboxToggle:true,
			title: 'Repeating Event',
			defaultType: 'textfield',
			collapsed: config.record.repeating ? false : true,
			autoHeight: true,
			defaults: {
                            layout:'fit'
                        },
                        listeners: {
                            'beforecollapse' :  function(panel,ani) {
                                // Hide all the form fields you need to hide 
                                Ext.getCmp('crepeating').setValue(0);
                                return true; // this will avoid collapse of the field set
                            },
                            'beforeexpand' : function(panel,ani) {
                                // Display all the fields
                                Ext.getCmp('crepeating').setValue(1);
                                return true; // this will avoid the default expand behaviour
                            } 
                        },
                        layout: 'form',
			items :[{name: 'repeating',id: 'crepeating',xtype:'hidden',value:0},{
			    fieldLabel: 'Occurs'
			    ,name: 'repeattype'
                            ,id: 'crepeattype'
			    ,xtype:'combo'
			    ,mode: 'local'
			    ,store: new Ext.data.ArrayStore({
				    id: 0,
				    fields: ['v', 'measure'],
				    data: [[0, 'Daily'],[1, 'Weekly'],[2, 'Monthly'],[3, 'Yearly']]
			    })
			    ,triggerAction: 'all'
			    ,displayField: 'measure'
			    ,valueField: 'v'
			    ,editable: true
			    //,anchor: '100'
			    ,width: 200
			    //,listeners:{select:function() {var numberOfDays = ReportForm.form.findField('DateRangeCombo').getValue();var newDate = DateAdd('05/04/2011', 'D', (numberOfDays * -1)); ReportForm.form.findField('StartDate').setValue(newDate);}}
                            ,value: config.record.repeattype
                            ,listeners:{select:{fn:function(combo, value) {
                                //var comboCity = Ext.getCmp('combo-city');        
                                var rt = Ext.getCmp('crepeattype');
                                //comboCity.clearValue();
                                //comboCity.store.filter('cid', combo.getValue());
                                //Ext.Msg.alert('Repeat Type Selected', 'Your event repeat type is: '+rt.getValue());
                                if(rt.getValue() === 1){
                                    Ext.getCmp('crepeaton').show();
                                } else { Ext.getCmp('crepeaton').hide(); }
                                }}
                            }
			},{
			    fieldLabel: 'Occurs on'
			    ,name: 'repeaton'
                            ,id: 'crepeaton'
			    ,xtype: 'checkboxgroup'
                            ,hidden: config.record.repeattype == 1 ? false : true // hide on load
			    ,items: [
				{boxLabel: 'Sunday', name: 'cb-auto-1', value: 0, checked: false },
				{boxLabel: 'Monday', name: 'cb-auto-2', value: 1, checked: false },
				{boxLabel: 'Tuesday', name: 'cb-auto-3', value: 2, checked: false },
				{boxLabel: 'Wednesday', name: 'cb-auto-4', value: 3, checked: false },
				{boxLabel: 'Thursday', name: 'cb-auto-5', value: 4, checked: false },
				{boxLabel: 'Friday', name: 'cb-auto-6', value: 5, checked: false },
				{boxLabel: 'Saturday', name: 'cb-auto-7', value: 6, checked: false }
			    ]
                            //,value: config.record.repeaton
			},{
			    fieldLabel: 'Repeat Every'
			    ,name: 'repeatfrequency'
                            ,id: 'crepeatfrequency'
			    ,xtype: 'combo'
			    ,mode: 'local'
			    ,store: new Ext.data.ArrayStore({
				    id: 0,
				    fields: ['counter'],
				    data: [[],[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23],[24],[25],[26],[27],[28],[29],[30]]
			    })
			    ,displayField: 'counter'
			    ,valueField: 'counter'
			    ,editable: true
			    ,width: 100
			    ,anchor: 'auto'
                            ,value: config.record.repeatfrequency
			},{
			    fieldLabel: 'Last Occurance'
			    ,name: 'repeatenddate'
                            ,id: 'crepeatenddate'
                            ,format    : 'm-d-Y'
			    ,allowBlank:false
			    ,xtype: 'datefield'
                            ,value: config.record.repeatenddate
                            ,submitValue: false
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
					    ,name: 'description'
                                            ,hiddenName: 'description'
                                            ,id: 'cdescription'
					    ,value: config.record.description
					}
				    },{
					title: 'Location'
					,defaults: {width:230}
					,defaultType:'textfield'
					,items:[{
					    fieldLabel: 'Name'
					    ,name:'location_name'
                                            ,id: 'clocation_name'
                                            ,value: config.record.location_name
					},{
					    fieldLabel: 'Address'
					    ,name:'location_address'
                                            ,id:'clocation_address'
                                            ,value: config.record.location_address
					},{
					    fieldLabel: 'Display Map'
					    ,name:'map'
                                            ,id:'cmap'
					    ,xtype:'checkbox'
                                            ,checked: config.record.map ? true : false
					}]
				    },{
					title:'Link',
					defaults: {width: 230},
					defaultType: 'textfield',
					items: [{
					    fieldLabel: 'Link'
					    ,name: 'link'
                                            ,id: 'clink'
					    ,allowBlank:false
                                            ,value: config.record.link
					},{
					    fieldLabel: 'Link Rel'
					    ,name: 'linkrel'
                                            ,id: 'clinkrel'
					    ,value: config.record.linkrel
					},{
					    fieldLabel: 'Link Target'
					    ,name: 'linktarget'
                                            ,id: 'clinktarget'
                                            ,xtype: 'combo'
                                            ,mode: 'local'
                                            ,store: new Ext.data.ArrayStore({
                                                    id: 0,
                                                    fields: ['tt'],
                                                    data: [['_self'],['_new'],['_blank']]
                                            })
                                            ,triggerAction: 'all'
                                            ,displayField: 'tt'
                                            ,valueField: 'tt'
                                            ,editable: true
                                            ,width: 'auto'
                                            ,anchor: '30%'
					    ,value: config.record.linktarget
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
                //Ext.Msg.alert('Form reset', 'Your form has been reset.');
                //Ext.getCmp('updateAle').reset();
		
                console.log(Ext.getCmp('createAle'));
	    }
	 },{
	    text: 'Save'
	    ,handler: function(){
		
                var repeatDOW = '';
                var rawRepeatOn = Ext.getCmp('crepeaton').getValue();
                console.log("RepeatOn: "+rawRepeatOn.join(","));
                for (i = 0; i < rawRepeatOn.length; i++) {
                    repeatDOW += ','+rawRepeatOn[i].value;
                    console.log(rawRepeatOn[i]);
                }
                if(repeatDOW.length){repeatDOW+=',';}
                console.log("New RepeatON: "+repeatDOW);
                var frmData = {
                        title: Ext.getCmp('ctitle').getValue()
                        ,description: Ext.getCmp('cdescription').getValue()
                        ,categoryid: Ext.getCmp('ccategoryid').getValue()
			,startdate_date: Ext.getCmp('cstartdate_date').getValue()
			,startdate_time: Ext.getCmp('cstartdate_time').getValue()
                        ,enddate_date: Ext.getCmp('cenddate_date').getValue()
			,enddate_time: Ext.getCmp('cenddate_time').getValue()
                        ,link: Ext.getCmp('clink').getValue()
                        ,linkrel: Ext.getCmp('clinkrel').getValue()
                        ,linktarget: Ext.getCmp('clinktarget').getValue()
                        ,location_name: Ext.getCmp('clocation_name').getValue()
                        ,location_address: Ext.getCmp('clocation_address').getValue()
                        ,map: Ext.getCmp('cmap').checked ? 1 : 0
                        ,repeating: Ext.getCmp('crepeating').getValue()
                        ,repeattype: Ext.getCmp('crepeattype').getValue()
                        ,repeaton: repeatDOW //Ext.getCmp('repeaton').getValue()
                        ,repeatfrequency: Ext.getCmp('crepeatfrequency').getValue()
                        ,repeatenddate: Ext.getCmp('crepeatenddate').getValue()
                        // @TODO move to proper config section
                        ,HTTP_MODAUTH: MODx.siteId
                        ,action: 'mgr/events/create'
		};
                
                console.log("frmData: "+frmData);
               
		mxcCore.ajax.request({
			url: mxcCore.config.connectorUrl,
                        extraParams: {
			    //action: 'mgr/events/update'
			},
			params: frmData,
			scope: this,
			success: function(resp, opts) {
				// resp is the XmlHttpRequest object
                                var status = Ext.decode(resp.responseText).success;
                                if(!status){
                                    errmsg = '';
                                    cnt=0;
                                    Ext.each(Ext.decode(resp.responseText).data, function(op) {
                                      //Ext.Msg.alert(op.msg);
                                      cnt++;
                                      errmsg += cnt+') '+op.msg+'<br>';
                                      var el = Ext.get('c'+op.id);
                                          el.addClass('x-form-invalid');
                                    });
                                    Ext.Msg.alert("Error",errmsg);
                                } else {
                                    //Ext.Msg.alert('Update Event', 'Your event data is: ');
                                    Ext.getCmp('createAle').hide();
                                    Ext.getCmp('mxcalendars-grid-events').refresh();                                    
                                }
			},
                        failure: function(resp, opts) {
                           console.log('server-side failure with status code ' + resp.status);
                        }                        
		});
		
	    }
	}]
    });
    mxcCore.window.CreateCal.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.CreateCal,MODx.Window);
Ext.reg('mxcalendars-window-mxcalendar-create',mxcCore.window.CreateCal);

Ext.ns('mxcCore.window');
mxcCore.window.UpdateCal = function(config) {
    config = config || {};
    this.ale = Ext.applyIf(config,{
        title: _('mxcalendars.event_title_update')
        ,xtype: 'form'
        ,layout: 'form'
        ,id: 'updateAle'    
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
                            ,id: 'id'
                            ,value: config.record.id
			  },{	
			    fieldLabel: _('mxcalendars.label_title'),
			    name: 'title',
                            hiddenname: 'title',
                            id: 'title',
			    allowBlank: false,
			    anchor:'100%',
                            value: config.record.title
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
			      name: 'categoryid',
                              hiddenName: 'categoryid',
                              id: 'categoryid',
			      allowBlank: false,
			      typeAhead:true,
			      minChars:1,
			      emptyText:'Select a category',
			      valueNotFoundText:'Select a valid category',
			      anchor:'100%',
                              value: config.record.categoryid
			    }]
			},{
			  // Right Column
			  columnWidth: .5,
			  items: [{
			    xtype: 'container',
                            labelWidth: '250',
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
                                    id        : 'startdate_date',
                                    format    : 'm-d-Y',
				    fieldLabel: 'Start',
				    padding: '0 5 0 0',
				    allowBlank: false,
				    width: 150,
                                    value: config.record.startdate_date
				},
				{
				    xtype     : 'timefield',
				    name      : 'startdate_time',
                                    id        : 'startdate_time',
				    fieldLabel: 'Time',
				    margin: '0 5 0 0',
				    allowBlank: false,
				    width: 120,
				    value: config.record.startdate_time
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
                                    id: 'enddate_date',
                                    format    : 'm-d-Y',
				    fieldLabel: 'End',
				    padding: '0 5 0 0',
				    allowBlank: false,
				    width: 150,
                                    value: config.record.enddate_date
				},
				{
				    xtype     : 'timefield',
				    name      : 'enddate_time',
                                    id        : 'enddate_time',
				    fieldLabel: 'End Time',
				    margin: '0 5 0 0',
				    allowBlank: false,
				    width: 120,
				    value: config.record.enddate_time
				}
			    ]
			  }]
		    }]
		    },
		    {
			xtype:'fieldset',
			checkboxToggle:true,
			title: 'Repeating Event',
			defaultType: 'textfield',
			collapsed: config.record.repeating ? false : true,
			autoHeight: true,
			defaults: {
                            layout:'fit'
                        },
                        listeners: {
                            'beforecollapse' :  function(panel,ani) {
                                // Hide all the form fields you need to hide 
                                Ext.getCmp('repeating').setValue(0);
                                return true; // this will avoid collapse of the field set
                            },
                            'beforeexpand' : function(panel,ani) {
                                // Display all the fields
                                Ext.getCmp('repeating').setValue(1);
                                return true; // this will avoid the default expand behaviour
                            } 
                        },
                        layout: 'form',
			items :[{name: 'repeating',id: 'repeating',xtype:'hidden',value:config.record.repeating?1:0},{
			    fieldLabel: 'Occurs'
			    ,name: 'repeattype'
                            ,id: 'repeattype'
			    ,xtype:'combo'
			    ,mode: 'local'
			    ,store: new Ext.data.ArrayStore({
				    id: 0,
				    fields: ['v', 'measure'],
				    data: [[0, 'Daily'],[1, 'Weekly'],[2, 'Monthly'],[3, 'Yearly']]
			    })
			    ,triggerAction: 'all'
			    ,displayField: 'measure'
			    ,valueField: 'v'
			    ,editable: true
			    //,anchor: '100'
			    ,width: 200
			    //,listeners:{select:function() {var numberOfDays = ReportForm.form.findField('DateRangeCombo').getValue();var newDate = DateAdd('05/04/2011', 'D', (numberOfDays * -1)); ReportForm.form.findField('StartDate').setValue(newDate);}}
                            ,value: config.record.repeattype
                            ,listeners:{select:{fn:function(combo, value) {
                                //var comboCity = Ext.getCmp('combo-city');        
                                var rt = Ext.getCmp('repeattype');
                                //comboCity.clearValue();
                                //comboCity.store.filter('cid', combo.getValue());
                                //Ext.Msg.alert('Repeat Type Selected', 'Your event repeat type is: '+rt.getValue());
                                if(rt.getValue() === 1){
                                    Ext.getCmp('repeaton').show();
                                } else { Ext.getCmp('repeaton').hide(); }
                                }}
                            }
			},{
			    fieldLabel: 'Occurs on'
			    ,name: 'repeaton'
                            ,id: 'repeaton'
			    ,xtype: 'checkboxgroup'
                            ,hidden: config.record.repeattype == 1 ? false : true // hide on load
			    ,items: [
				{boxLabel: 'Sunday', name: 'cb-auto-1', value: 0, checked: config.record.repeaton.indexOf(',0,')!=-1 ? true : false },
				{boxLabel: 'Monday', name: 'cb-auto-2', value: 1, checked: config.record.repeaton.indexOf(',1,')!=-1 ? true : false },
				{boxLabel: 'Tuesday', name: 'cb-auto-3', value: 2, checked: config.record.repeaton.indexOf(',2,')!=-1 ? true : false },
				{boxLabel: 'Wednesday', name: 'cb-auto-4', value: 3, checked: config.record.repeaton.indexOf(',3,')!=-1 ? true : false },
				{boxLabel: 'Thursday', name: 'cb-auto-5', value: 4, checked: config.record.repeaton.indexOf(',4,')!=-1 ? true : false },
				{boxLabel: 'Friday', name: 'cb-auto-6', value: 5, checked: config.record.repeaton.indexOf(',5,')!=-1 ? true : false },
				{boxLabel: 'Saturday', name: 'cb-auto-7', value: 6, checked: config.record.repeaton.indexOf(',6,')!=-1 ? true : false }
			    ]
                            //,value: config.record.repeaton
			},{
			    fieldLabel: 'Repeat Every'
			    ,name: 'repeatfrequency'
                            ,id: 'repeatfrequency'
			    ,xtype: 'combo'
			    ,mode: 'local'
			    ,store: new Ext.data.ArrayStore({
				    id: 0,
				    fields: ['counter'],
				    data: [[],[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23],[24],[25],[26],[27],[28],[29],[30]]
			    })
			    ,displayField: 'counter'
			    ,valueField: 'counter'
			    ,editable: true
			    ,width: 100
			    ,anchor: 'auto'
                            ,value: config.record.repeatfrequency
			},{
			    fieldLabel: 'Last Occurance'
			    ,name: 'repeatenddate'
                            ,id: 'repeatenddate'
                            ,format    : 'm-d-Y'
			    ,allowBlank:false
			    ,xtype: 'datefield'
                            ,value: config.record.repeatenddate
                            ,submitValue: false
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
					    ,name: 'description'
                                            ,hiddenName: 'description'
                                            ,id: 'description'
					    ,value: config.record.description
					}
				    },{
					title: 'Location'
					,defaults: {width:230}
					,defaultType:'textfield'
					,items:[{
					    fieldLabel: 'Name'
					    ,name:'location_name'
                                            ,id: 'location_name'
                                            ,value: config.record.location_name
					},{
					    fieldLabel: 'Address'
					    ,name:'location_address'
                                            ,id:'location_address'
                                            ,value: config.record.location_address
					},{
					    fieldLabel: 'Display Map'
					    ,name:'map'
                                            ,id:'map'
					    ,xtype:'checkbox'
                                            ,checked: config.record.map ? true : false
					}]
				    },{
					title:'Link',
					defaults: {width: 230},
					defaultType: 'textfield',
					items: [{
					    fieldLabel: 'Link'
					    ,name: 'link'
                                            ,id: 'link'
					    ,allowBlank:false
                                            ,value: config.record.link
					},{
					    fieldLabel: 'Link Rel'
					    ,name: 'linkrel'
                                            ,id: 'linkrel'
					    ,value: config.record.linkrel
					},{
					    fieldLabel: 'Link Target'
					    ,name: 'linktarget'
                                            ,id: 'linktarget'
                                            ,xtype: 'combo'
                                            ,mode: 'local'
                                            ,store: new Ext.data.ArrayStore({
                                                    id: 0,
                                                    fields: ['tt'],
                                                    data: [['_self'],['_new'],['_blank']]
                                            })
                                            ,triggerAction: 'all'
                                            ,displayField: 'tt'
                                            ,valueField: 'tt'
                                            ,editable: true
                                            ,width: 'auto'
                                            ,anchor: '30%'
					    ,value: config.record.linktarget
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
                //Ext.Msg.alert('Form reset', 'Your form has been reset.');
                //Ext.getCmp('updateAle').reset();
		
                console.log(Ext.getCmp('updateAle'));
	    }
	 },{
	    text: 'Save'
	    ,handler: function(){
		
                var repeatDOW = '';
                var rawRepeatOn = Ext.getCmp('repeaton').getValue();
                console.log("RepeatOn: "+rawRepeatOn.join(","));
                for (i = 0; i < rawRepeatOn.length; i++) {
                    repeatDOW += ','+rawRepeatOn[i].value;
                    console.log(rawRepeatOn[i]);
                }
                if(repeatDOW.length){repeatDOW+=',';}
                console.log("New RepeatON: "+repeatDOW);
                var frmData = {
                        id: Ext.getCmp('id').getValue()
                        ,title: Ext.getCmp('title').getValue()
                        ,description: Ext.getCmp('description').getValue()
                        ,categoryid: Ext.getCmp('categoryid').getValue()
			,startdate_date: Ext.getCmp('startdate_date').getValue()
			,startdate_time: Ext.getCmp('startdate_time').getValue()
                        ,enddate_date: Ext.getCmp('enddate_date').getValue()
			,enddate_time: Ext.getCmp('enddate_time').getValue()
                        ,link: Ext.getCmp('link').getValue()
                        ,linkrel: Ext.getCmp('linkrel').getValue()
                        ,linktarget: Ext.getCmp('linktarget').getValue()
                        ,location_name: Ext.getCmp('location_name').getValue()
                        ,location_address: Ext.getCmp('location_address').getValue()
                        ,map: Ext.getCmp('map').checked ? 1 : 0
                        ,repeating: Ext.getCmp('repeating').getValue()
                        ,repeattype: Ext.getCmp('repeattype').getValue()
                        ,repeaton: repeatDOW //Ext.getCmp('repeaton').getValue()
                        ,repeatfrequency: Ext.getCmp('repeatfrequency').getValue()
                        ,repeatenddate: Ext.getCmp('repeatenddate').getValue()
                        // @TODO move to proper config section
                        ,HTTP_MODAUTH: MODx.siteId
                        ,action: 'mgr/events/update'
		};
                
                console.log("frmData: "+frmData);
               
		mxcCore.ajax.request({
			url: mxcCore.config.connectorUrl,
                        extraParams: {
			    //action: 'mgr/events/update'
			},
			params: frmData,
			scope: this,
			success: function() {
				
				//Ext.Msg.alert('Update Event', 'Your event data is: ');
				Ext.getCmp('updateAle').close();
                                Ext.getCmp('mxcalendars-grid-events').refresh();
			}                        
		});
		
	    }
	}]
    });
    mxcCore.window.UpdateCal.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.UpdateCal,Ext.Window);
Ext.reg('mxcalendars-window-mxcalendar-update',mxcCore.window.UpdateCal);
