mxcCore.grid.events = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'mxcalendars-grid-events'
        ,url: mxcCore.config.connectorUrl
        ,baseParams: {action: 'mgr/events/getList'}
        ,fields: ['id','description','title','context','calendar_id','form_chunk','categoryid','source','feeds_id','feeds_uid',{name:'startdate', type: 'date', dateFormat:'timestamp'},'startdate_date','startdate_time',{name:'enddate', type: 'date', dateFormat:'timestamp'},'enddate_date','enddate_time','repeating','repeattype','repeaton','repeatfrequency',{name:'repeatenddate', type: 'date', dateFormat:'timestamp'},'repeatdates','menu','name','map','link','linkrel','linktarget','location_name','location_address','address','active','catfriendly']
        ,paging: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'name'
        ,save_action: 'mgr/events/updatefromgrid' // Support the inline editing
	,autosave: true // Support the inline editing
        ,columns: [
			// add the grid columns to the display
			 {header: _('id'),dataIndex: 'id',sortable: true,width:40}
                        ,{header: _('mxcalendars.source'),dataIndex: 'source',sortable: true,width:50}
			,{header: _('mxcalendars.name'),dataIndex: 'title',sortable: true,width:110,editor: {xtype: 'textfield'}}
                        ,{header: _('mxcalendars.grid_col_context'), dataIndex:'context',editor: { xtype: 'mxc-combo-context', renderer: true }}
                        ,{header: _('mxcalendars.grid_col_calendar'), dataIndex:'calendar_id',editor: { xtype: 'mxc-combo-calendar', renderer: true }}
                        ,{header: _('mxcalendars.categoryid_col_label'),dataIndex: 'catfriendly',sortable: true,width:80}
			//,{header: _('mxcalendars.startdate_col_label'),dataIndex: 'startdate',sortable: true}
			,{header: _('mxcalendars.startdate_col_label'),dataIndex: 'startdate',sortable: true,width:60, xtype : 'datecolumn',format:mxcCore.config.mgr_dateformat, editable:false, editor:{xtype:'datefield', format:mxcCore.config.mgr_dateformat}}
			,{header: _('mxcalendars.starttime_col_label'), dataIndex: 'startdate_time', sortable:false,width:60, editor:{ xtype:'timefield', format: mxcCore.config.mgr_timeformat}}
			,{header: _('mxcalendars.enddate_col_label'),dataIndex: 'enddate',sortable: true,width:60, xtype : 'datecolumn',format:mxcCore.config.mgr_dateformat, editable:false, editor:{xtype:'datefield',format:mxcCore.config.mgr_dateformat}}
			,{header: _('mxcalendars.endtime_col_label'),dataIndex: 'enddate_time',sortable: false,width:60, editor:{ xtype:'timefield', format: mxcCore.config.mgr_timeformat}}
			,{header: _('mxcalendars.repeating_col_label'),dataIndex: 'repeating',sortable: true,width:30}
                        ,{header: _('mxcalendars.repeating_last_occ_col_label'),dataIndex: 'repeatenddate', sortable: true,width:60, xtype : 'datecolumn',format:mxcCore.config.mgr_dateformat}
                        ,{header: _('mxcalendars.category_active_col_label'),dataIndex: 'active', sortable: true,width:30,editor: { xtype: 'modx-combo-boolean', renderer: true}}
                        ,{hidden:true, header: _('mxcalendars.label_forms'), dataIndex:'form_chunk'}
                        ,{hidden:true, header: _('mxcalendars.label_repeating_event'), dataIndex:'repeating'}
                        ,{hidden:true, header: _('mxcalendars.label_repeat_type'), dataIndex:'repeattype'}
                        ,{hidden:true, header: _('mxcalendars.label_repeaton'), dataIndex:'repeaton'}
                        ,{hidden:true, header: _('mxcalendars.label_repeat_frequency'), dataIndex:'repeatfrequency'}
                        ,{hidden:true, header: _('mxcalendars.label_display_map'), dataIndex:'map'}
                        ,{hidden:true, header: _('mxcalendars.label_link'), dataIndex:'link'}
                        ,{hidden:true, header: _('mxcalendars.label_link_rel'), dataIndex:'linkrel'}
                        ,{hidden:true, header: _('mxcalendars.label_link_target'), dataIndex:'linktarget'}
                        ,{hidden:true, header: _('mxcalendars.label_location')+' '+_('mxcalendars.label_name'), dataIndex:'location_name'}
                        ,{hidden:true, header: _('mxcalendars.label_address'), dataIndex:'location_address'}
                        ,{hidden:true, header: _('mxcalendars.label_location')+' '+_('mxcalendars.label_address'), dataIndex:'address'}
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
                    text:_('mxcalendars.btn_show_past_events')
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
                    text:_('mxcalendars.btn_show_upcoming_events')
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
                   ,handler:this.createCal
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
			text: _('mxcalendars.event_title_update')
			,handler: this.updateCal
		},'-',{
                    text: _('mxcalendars.event_title_duplicate')
                    ,handler: this.duplicateEvent
                },'-',{
			text: _('mxcalendars.event_title_remove')
			,handler: this.removeCal
		}];
		this.addContextMenuItem(m);
		return true;
	},createCal: function(btn,e) {
		mxcCore.eventId = 0;
                if (this.createCalWindow) {
                    this.createCalWindow.close();
		}
                this.createCalWindow = MODx.load({
                    xtype: 'mxcalendars-window-mxcalendar-create'
                    ,record: {'repeaton':''}
                });
		this.createCalWindow.show(e.target);
	},updateCal: function(btn,e) {
		mxcCore.eventId = this.menu.record.id;
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
	},duplicateEvent:function(btn,e){
            if (this.createCalWindow) {
                    this.createCalWindow.close();
		}
                cloneRec = this.menu.record;
                cloneRec.title = '('+_('mxcalendars.label_duplicate')+') '+cloneRec.title;
                this.createCalWindow = MODx.load({
                    xtype: 'mxcalendars-window-mxcalendar-create'
                    ,record: cloneRec
                });
		this.createCalWindow.show(e.target);
        }
});
Ext.reg('mxcalendars-grid-events',mxcCore.grid.events);


mxcCore.window.CreateCal = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('mxcalendars.event_title_create')
        ,autoHeight: false
        ,height: Ext.getBody().getViewSize().height*.85
        ,autoScroll: true
        ,id: 'CreateCal'
        ,url: mxcCore.config.connectorUrl
        ,width: 870
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
                              xtype: 'combo',
                              displayField: 'key',
                              valueField: 'key',
                              forceSelection: true,
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
                              }),
                              mode: 'remote',
                              triggerAction: 'all',
                              fieldLabel: _('mxcalendars.grid_col_context'),
                              name: 'context',
                              hiddenName: 'context',
                              id: 'ccontext',
                              allowBlank: mxcCore.config.isAdministrator ? true : false,
                              typeAhead:true,
                              minChars:1,
                              emptyText:_('mxcalendars.label_select_context'),
                              valueNotFoundText:_('mxcalendars.label_select_context_err'),
                              anchor:'100%',
                              value: config.record.context
                            },{	
			    fieldLabel: _('mxcalendars.label_title'),
			    name: 'title',
                            hiddenname: 'title',
                            id: 'ctitle',
			    allowBlank: false,
			    anchor:'100%',
                            value: config.record.title
			  },{
			      xtype: 'superboxselect',
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
			      fieldLabel: _('mxcalendars.grid_col_category'),
			      name: 'categoryid',
                              hiddenName: 'categoryid',
                              id: 'ccategoryid',
			      allowBlank: mxcCore.config.category_required ? true : false,
			      typeAhead:true,
			      minChars:1,
			      emptyText:_('mxcalendars.label_select_category'),
			      valueNotFoundText:_('mxcalendars.label_select_category_err'),
			      anchor:'100%',
                              value: config.record.categoryid
			    }]
			},{
			  // Right Column
			  columnWidth: .5,
			  items: [{
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
                              name: 'calendar_id',
                              hiddenName: 'calendar_id',
                              id: 'ccalendar_id',
                              allowBlank: true,
                              typeAhead:true,
                              minChars:1,
                              emptyText:_('mxcalendars.label_select_calendar'),
                              valueNotFoundText:_('mxcalendars.label_select_category_err'),
                              anchor:'100%',
                              value: config.record.calendar_id
                                },{
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
                                    format    : mxcCore.config.mgr_dateformat,
				    fieldLabel: 'Start',
				    padding: '0 5 0 0',
				    allowBlank: false,
				    width: 150,
                                    value: config.record.startdate_date,
                                    listeners:{change:{fn:function(item, value) {
                                        var ed = Ext.getCmp('cenddate_date');
                                        if(ed.getValue() === ''){
                                            ed.setValue(value);
                                        } 
                                        }}
                                    }
                                    
				},
				{
				    xtype     : 'timefield',
				    name      : 'startdate_time',
                                    id        : 'cstartdate_time',
                                    format    : mxcCore.config.mgr_timeformat,
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
                                    format    : mxcCore.config.mgr_dateformat,
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
                                    format    : mxcCore.config.mgr_timeformat,
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
			title: _('mxcalendars.label_repeating_event'),
			defaultType: 'textfield',
			collapsed: config.record.repeating ? false : true,
			autoHeight: true,
			defaults: {
                            layout:'fill'
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
			items :[{name: 'repeating',id: 'crepeating',xtype:'hidden',value:config.record.repeating?1:0},{
			    fieldLabel: 'Occurs'
			    ,name: 'repeattype'
                            ,id: 'crepeattype'
			    ,xtype:'combo'
			    ,mode: 'local'
			    ,store: new Ext.data.ArrayStore({
				    id: 0,
				    fields: ['v', 'measure'],
				    data: [[0, _('mxcalendars.label_daily')],[1, _('mxcalendars.label_weekly')],[2, _('mxcalendars.label_monthly')],[3, _('mxcalendars.label_yearly')]]
			    })
			    ,triggerAction: 'all'
			    ,displayField: 'measure'
			    ,valueField: 'v'
			    ,editable: true
			    ,width: 150
                            ,layout:'anchor'
                            ,anchor: '100% 100%'
                            ,value: config.record.repeattype
                            ,listeners:{select:{fn:function(combo, value) {
                                var rt = Ext.getCmp('crepeattype');
                                if(rt.getValue() === 1){
                                    Ext.getCmp('crepeaton').show();
                                } else { Ext.getCmp('crepeaton').hide(); }
                                }}
                            }
                            ,value: config.record.repeattype
			},{
			    fieldLabel: _('mxcalendars.label_repeaton')
			    ,name: 'repeaton'
                            ,id: 'crepeaton'
			    ,xtype: 'checkboxgroup'
                            ,hidden: config.record.repeattype == 1 ? false : true // hide on load
			    ,items: [
				{boxLabel: _('mxcalendars.label_sunday'), name: 'cb-auto-1', value: 0, checked: config.record.repeaton.indexOf(',0,')!=-1 ? true : false },
				{boxLabel: _('mxcalendars.label_monday'), name: 'cb-auto-2', value: 1, checked: config.record.repeaton.indexOf(',1,')!=-1 ? true : false },
				{boxLabel: _('mxcalendars.label_tuesday'), name: 'cb-auto-3', value: 2, checked: config.record.repeaton.indexOf(',2,')!=-1 ? true : false },
				{boxLabel: _('mxcalendars.label_wednesday'), name: 'cb-auto-4', value: 3, checked: config.record.repeaton.indexOf(',3,')!=-1 ? true : false },
				{boxLabel: _('mxcalendars.label_thursday'), name: 'cb-auto-5', value: 4, checked: config.record.repeaton.indexOf(',4,')!=-1 ? true : false },
				{boxLabel: _('mxcalendars.label_friday'), name: 'cb-auto-6', value: 5, checked: config.record.repeaton.indexOf(',5,')!=-1 ? true : false },
				{boxLabel: _('mxcalendars.label_saturday'), name: 'cb-auto-7', value: 6, checked: config.record.repeaton.indexOf(',6,')!=-1 ? true : false }
			    ]
                            ,value: config.record.repeaton
			},{
			    fieldLabel: _('mxcalendars.label_repeat_frequency')
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
			    ,width: 50
                            ,layout:'anchor'
                            ,anchor: '100% 100%'
                            ,value: config.record.repeatfrequency
                            ,msgTarget : 'crepeatfrequency'
                            ,msgDisplay: 'block'
                            ,listeners: {
                                render: function(c) {
                                  Ext.QuickTips.register({
                                    target: c.getEl(),
                                    text: _('mxcalendars.tip_repeaton')
                                  });
                                }
                            }
			},{
			    fieldLabel: _('mxcalendars.label_repeat_last_occurance')
			    ,name: 'repeatenddate'
                            ,id: 'crepeatenddate'
                            ,width:120
                            ,layout:'anchor'
                            ,anchor: '100% 100%'
                            ,format    : mxcCore.config.mgr_dateformat
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
				,height:385
				//,autoHeight: true
				,width:840
				,defaults:{bodyStyle:'padding:10px',layout:'form'}
				,items:[
				    {
					cls: 'x-plain',
					title: _('mxcalendars.label_description'),
					layout: 'fit',
					items: {
					    xtype: mxcCore.config.event_desc_type
					    ,name: 'description'
                                            ,hiddenName: 'description'
                                            ,id: 'cdescription'
					    ,value: config.record.description
					}
				    },{
					cls: 'x-plain',
					title: _('mxcalendars.label_images'),
					layout: 'fit',
					items: {
					    xtype: 'mxc-images-grid'
                                            ,id: 'gridimages'
					}
				    },{
					title: _('mxcalendars.label_location')
					,defaults: {width:230}
					,defaultType:'textfield'
					,items:[{
					    fieldLabel: _('mxcalendars.label_name')
					    ,name:'location_name'
                                            ,id: 'clocation_name'
                                            ,value: config.record.location_name
					},{
					    fieldLabel: _('mxcalendars.label_address')
					    ,name:'location_address'
                                            ,id:'clocation_address'
                                            ,value: config.record.location_address
					},{
					    boxLabel: _('mxcalendars.label_display_map')
					    ,name:'map'
                                            ,id:'cmap'
					    ,xtype:'checkbox'
                                            ,checked: config.record.map ? true : false
					}]
				    },{
					title:_('mxcalendars.label_link'),
					defaults: {width: 230},
					defaultType: 'textfield',
					items: [{
					    fieldLabel: 'Link'
					    ,name: 'link'
                                            ,id: 'clink'
					    ,allowBlank:false
                                            ,value: config.record.link
					},{
					    fieldLabel: _('mxcalendars.label_link_rel')
					    ,name: 'linkrel'
                                            ,id: 'clinkrel'
					    ,value: config.record.linkrel
					},{
					    fieldLabel: _('mxcalendars.label_link_target')
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
				    },{
                                        title:"Form"
                                        ,defaults: {width:230}
                                        ,items: [{
                                              xtype: 'combo',
                                              displayField: 'name',
                                              valueField: 'name',
                                              forceSelection: true,
                                              store: new Ext.data.JsonStore({
                                                      root: 'results',
                                                      idProperty: 'id',
                                                      url: mxcCore.config.connectorUrl,
                                                      baseParams: {
                                                            action: 'stores/getformchunks' 
                                                      },
                                                      fields: [
                                                            'id','name','description',
                                                      ]
                                              }),
                                              mode: 'remote',
                                              triggerAction: 'all',
                                              fieldLabel: _('mxcalendars.grid_col_formchunk'),
                                              name: 'form_chunk',
                                              hiddenName: 'form_chunk',
                                              id: 'cform_chunk',
                                              allowBlank: true,
                                              typeAhead:true,
                                              minChars:1,
                                              emptyText:_('mxcalendars.label_select_form'),
                                              value: config.record.form_chunk,
                                              valueNotFoundText:_('mxcalendars.label_select_form_err'),
                                              anchor:'100%'
                                            }]
                                    }
				]
			    }
			]
		    },{
                        xtype: 'checkbox'
                        ,fieldLabel: _('mxcalendars.category_active_col_label')
                        ,name: 'active'
                        ,hiddenName: 'active'
                        ,checked: true
                        ,value: 1
                    }
		]
                
        ,buttons: [{
	    text: _('mxcalendars.label_cancel')
	    ,type: 'close'
	    ,handler: function()
	    {
		Ext.getCmp('CreateCal').hide();
	    }
	 },{
	    text: _('mxcalendars.label_save')
	    ,handler: function(){
		
                var repeatDOW = '';
                var rawRepeatOn = Ext.getCmp('crepeaton').getValue();
                ////console.log("RepeatOn: "+rawRepeatOn.join(","));
                for (i = 0; i < rawRepeatOn.length; i++) {
                    repeatDOW += ','+rawRepeatOn[i].value;
                    ////console.log(rawRepeatOn[i]);
                }
                if(repeatDOW.length){repeatDOW+=',';}
                ////console.log("New RepeatON: "+repeatDOW);
                var frmData = {
                        title: Ext.getCmp('ctitle').getValue() //req
                        ,description: Ext.getCmp('cdescription').getValue()
                        ,categoryid: Ext.getCmp('ccategoryid').getValue() //req
			,startdate_date: Ext.getCmp('cstartdate_date').getValue() //req
			,startdate_time: Ext.getCmp('cstartdate_time').getValue() //req
                        ,enddate_date: Ext.getCmp('cenddate_date').getValue() //req
			,enddate_time: Ext.getCmp('cenddate_time').getValue() //req
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
                        ,context: Ext.getCmp('ccontext').getValue()
                        ,calendar_id: Ext.getCmp('ccalendar_id').getValue()
                        ,form_chunk: Ext.getCmp('cform_chunk').getValue()
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
                                      cnt++;
                                      errmsg += cnt+') '+op.msg+'<br>';
                                      var el = Ext.get('c'+op.id);
                                          el.addClass('x-form-invalid');
                                    });
                                    if(cnt > 0)  Ext.Msg.alert("Error",errmsg);
                                } else {
                                    //Ext.Msg.alert('Update Event', 'Your event data is: ');
                                    Ext.getCmp('CreateCal').hide();
                                    Ext.getCmp('mxcalendars-grid-events').refresh();                                    
                                }
			},
                        failure: function(resp, opts) {
                           ////console.log('server-side failure with status code ' + resp.status);
                        }                        
		});
		
	    }
	}]
    });
    mxcCore.window.CreateCal.superclass.constructor.call(this,config);
    this.on('activate',function() {
        if (typeof Tiny != 'undefined') { MODx.loadRTE('cdescription'); }
    });
};
Ext.extend(mxcCore.window.CreateCal,MODx.Window);
Ext.reg('mxcalendars-window-mxcalendar-create',mxcCore.window.CreateCal);

Ext.ns('mxcCore.window');
mxcCore.window.UpdateCal = function(config) {
    config = config || {};
    this.ale = Ext.applyIf(config,{
        title: _('mxcalendars.event_title_update')
        ,autoHeight: false
        ,height: Ext.getBody().getViewSize().height*.85
        ,autoScroll: true
        ,xtype: 'form'
        ,layout: 'form'
        ,id: 'UpdateCal'    
        ,url: mxcCore.config.connectorUrl
        ,width: 870
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
                              xtype: 'combo',
                              displayField: 'key',
                              valueField: 'key',
                              forceSelection: true,
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
                              }),
                              mode: 'remote',
                              triggerAction: 'all',
                              fieldLabel: _('mxcalendars.grid_col_context'),
                              name: 'context',
                              hiddenName: 'context',
                              id: 'context',
                              allowBlank: mxcCore.config.isAdministrator ? true : false,
                              typeAhead:true,
                              minChars:1,
                              emptyText:_('mxcalendars.label_select_context'),
                              valueNotFoundText:_('mxcalendars.label_select_context_err'),
                              anchor:'100%',
                              value: config.record.context
                            },{
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
			      xtype: 'superboxselect',
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
			      fieldLabel: _('mxcalendars.grid_col_category'),
			      name: 'categoryid',
                              hiddenName: 'categoryid',
                              id: 'categoryid',
			      allowBlank: mxcCore.config.category_required ? true : false,
			      typeAhead:true,
			      minChars:1,
			      emptyText:_('mxcalendars.label_select_category'),
			      valueNotFoundText:_('mxcalendars.label_select_category_err'),
			      anchor:'100%',
                              value: config.record.categoryid
			    }]
			},{
			  // Right Column
			  columnWidth: .5,
			  items: [{
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
                              name: 'calendar_id',
                              hiddenName: 'calendar_id',
                              id: 'calendar_id',
                              allowBlank: true,
                              typeAhead:true,
                              minChars:1,
                              emptyText:_('mxcalendars.label_select_calendar'),
                              valueNotFoundText:_('mxcalendars.label_select_category_err'),
                              anchor:'100%',
                              value: config.record.calendar_id
                            },{
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
                                    format    : mxcCore.config.mgr_dateformat,
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
                                    format    : mxcCore.config.mgr_timeformat,
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
                                    format    : mxcCore.config.mgr_dateformat,
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
                                    format    : mxcCore.config.mgr_timeformat,
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
			title: _('mxcalendars.label_repeating_event'),
			defaultType: 'textfield',
			collapsed: config.record.repeating ? false : true,
			autoHeight: true,
			defaults: {
                            layout:'fill'
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
			    fieldLabel: _('mxcalendars.label_repeat_type')
			    ,name: 'repeattype'
                            ,id: 'repeattype'
			    ,xtype:'combo'
			    ,mode: 'local'
			    ,store: new Ext.data.ArrayStore({
				    id: 0,
				    fields: ['v', 'measure'],
				    data: [[0, _('mxcalendars.label_daily')],[1, _('mxcalendars.label_weekly')],[2, _('mxcalendars.label_monthly')],[3, _('mxcalendars.label_yearly')]]
			    })
			    ,triggerAction: 'all'
			    ,displayField: 'measure'
			    ,valueField: 'v'
			    ,editable: true
			    ,width: 150
                            ,layout:'anchor'
                            ,anchor: '100% 100%'
                            ,value: config.record.repeattype
                            ,listeners:{select:{fn:function(combo, value) {
                                var rt = Ext.getCmp('repeattype');
                                if(rt.getValue() === 1){
                                    Ext.getCmp('repeaton').show();
                                } else { Ext.getCmp('repeaton').hide(); }
                                }}
                            }
			},{
			    fieldLabel: _('mxcalendars.label_repeaton')
			    ,name: 'repeaton'
                            ,id: 'repeaton'
			    ,xtype: 'checkboxgroup'
                            ,hidden: config.record.repeattype == 1 ? false : true // hide on load
			    ,items: [
				{boxLabel: _('mxcalendars.label_sunday'), name: 'cb-auto-1', value: 0, checked: config.record.repeaton.indexOf(',0,')!=-1 ? true : false },
				{boxLabel: _('mxcalendars.label_monday'), name: 'cb-auto-2', value: 1, checked: config.record.repeaton.indexOf(',1,')!=-1 ? true : false },
				{boxLabel: _('mxcalendars.label_tuesday'), name: 'cb-auto-3', value: 2, checked: config.record.repeaton.indexOf(',2,')!=-1 ? true : false },
				{boxLabel: _('mxcalendars.label_wednesday'), name: 'cb-auto-4', value: 3, checked: config.record.repeaton.indexOf(',3,')!=-1 ? true : false },
				{boxLabel: _('mxcalendars.label_thursday'), name: 'cb-auto-5', value: 4, checked: config.record.repeaton.indexOf(',4,')!=-1 ? true : false },
				{boxLabel: _('mxcalendars.label_friday'), name: 'cb-auto-6', value: 5, checked: config.record.repeaton.indexOf(',5,')!=-1 ? true : false },
				{boxLabel: _('mxcalendars.label_saturday'), name: 'cb-auto-7', value: 6, checked: config.record.repeaton.indexOf(',6,')!=-1 ? true : false }
			    ]
                            //,value: config.record.repeaton
			},{
			    fieldLabel: _('mxcalendars.label_repeat_frequency')
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
			    ,width: 50
                            ,layout:'anchor'
                            ,anchor: '100% 100%'
                            ,value: config.record.repeatfrequency
                            ,msgTarget : 'crepeatfrequency'
                            ,msgDisplay: 'block'
                            ,listeners: {
                                render: function(c) {
                                  Ext.QuickTips.register({
                                    target: c.getEl(),
                                    text: _('mxcalendars.tip_repeaton')
                                  });
                                }
                            }
			},{
			    fieldLabel: _('mxcalendars.label_repeat_last_occurance')
			    ,name: 'repeatenddate'
                            ,id: 'repeatenddate'
                            ,format    : mxcCore.config.mgr_dateformat
			    ,allowBlank:false
			    ,xtype: 'datefield'
                            ,value: config.record.repeatenddate
                            ,submitValue: false
                            ,width: 120
                            ,layout:'anchor'
                            ,anchor: '100% 100%'
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
				,height:385
				,width:840
				,defaults:{bodyStyle:'padding:10px',layout:'form'}
				,items:[
				    {
					cls: 'x-plain',
					title: _('mxcalendars.label_description'),
					layout: 'fit',
					items: {
					    xtype: mxcCore.config.event_desc_type
					    ,name: 'description'
                                            ,hiddenName: 'description'
                                            ,id: 'description'
					    ,value: config.record.description
					}
				    },{
					cls: 'x-plain',
					title: _('mxcalendars.label_images'),
					layout: 'fit',
					items: {
					    xtype: 'mxc-images-grid'
                                            ,id: 'gridimages'
					}
				    },{
					title: _('mxcalendars.label_location')
					,defaults: {width:230}
					,defaultType:'textfield'
					,items:[{
					    fieldLabel:_('mxcalendars.label_name')
					    ,name:'location_name'
                                            ,id: 'location_name'
                                            ,value: config.record.location_name
					},{
					    fieldLabel:_('mxcalendars.label_address')
					    ,name:'location_address'
                                            ,id:'location_address'
                                            ,value: config.record.location_address
					},{
					    boxLabel: _('mxcalendars.label_display_map')
					    ,name:'map'
                                            ,id:'map'
					    ,xtype:'checkbox'
                                            ,checked: config.record.map ? true : false
					}]
				    },{
					title:_('mxcalendars.label_link'),
					defaults: {width: 230},
					defaultType: 'textfield',
					items: [{
					    fieldLabel: 'Link'
					    ,name: 'link'
                                            ,id: 'link'
					    ,allowBlank:false
                                            ,value: config.record.link
					},{
					    fieldLabel: _('mxcalendars.label_link_rel')
					    ,name: 'linkrel'
                                            ,id: 'linkrel'
					    ,value: config.record.linkrel
					},{
					    fieldLabel: _('mxcalendars.label_link_target')
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
				    },{
                                        title:"Form"
                                        ,defaults: {width:230}
                                        ,items: [{
                                              xtype: 'combo',
                                              displayField: 'name',
                                              valueField: 'name',
                                              forceSelection: true,
                                              store: new Ext.data.JsonStore({
                                                      root: 'results',
                                                      idProperty: 'name',
                                                      url: mxcCore.config.connectorUrl,
                                                      baseParams: {
                                                            action: 'stores/getformchunks' 
                                                      },
                                                      fields: [
                                                            'id','name','description',
                                                      ]
                                              }),
                                              mode: 'remote',
                                              triggerAction: 'all',
                                              fieldLabel: _('mxcalendars.grid_col_formchunk'),
                                              name: 'form_chunk',
                                              hiddenName: 'form_chunk',
                                              id: 'form_chunk',
                                              allowBlank: true,
                                              typeAhead:true,
                                              minChars:1,
                                              emptyText:_('mxcalendars.label_select_form'),
                                              valueNotFoundText:_('mxcalendars.label_select_form_err'),
                                              anchor:'100%',
                                              value: config.record.form_chunk                                              
                                            }]
                                    }
				]
			    }
			]
		    },{
                        xtype: 'checkbox'
                        ,fieldLabel: _('mxcalendars.category_active_col_label')
                        ,name: 'active'
                        ,hiddenName: 'active'
                        ,checked: config.record.active ? true : false
                        ,value: 1
                    }
		]
                
        ,buttons: [{
	    text: _('mxcalendars.label_cancel')
	    ,type: 'close'
	    ,handler: function()
	    {
		Ext.getCmp('UpdateCal').hide();
	    }
	 },{
	    text: _('mxcalendars.label_save')
	    ,handler: function(){
                    
                var repeatDOW = '';
                var rawRepeatOn = Ext.getCmp('repeaton').getValue();
                //console.log("RepeatOn: "+rawRepeatOn.join(","));
                for (i = 0; i < rawRepeatOn.length; i++) {
                    repeatDOW += ','+rawRepeatOn[i].value;
                    //console.log(rawRepeatOn[i]);
                }
                if(repeatDOW.length){repeatDOW+=',';}
                //console.log("New RepeatON: "+repeatDOW);
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
                        ,context: Ext.getCmp('context').getValue()
                        ,calendar_id: Ext.getCmp('calendar_id').getValue()
                        ,form_chunk: Ext.getCmp('form_chunk').getValue()
                        // @TODO move to proper config section
                        ,HTTP_MODAUTH: MODx.siteId
                        ,action: 'mgr/events/update'
		};
                
                //console.log("frmData: "+frmData);
               
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
                                     Ext.Msg.alert("Error",Ext.decode(resp.responseText).message);
                                } else {
                                    //Ext.Msg.alert('Update Event', 'Your event data is: ');
                                    Ext.getCmp('UpdateCal').hide();
                                    Ext.getCmp('mxcalendars-grid-events').refresh();                                    
                                }
			},                        
		});
		
	    }
	}]
    });
    mxcCore.window.UpdateCal.superclass.constructor.call(this,config);
};
Ext.extend(mxcCore.window.UpdateCal,Ext.Window);
Ext.reg('mxcalendars-window-mxcalendar-update',mxcCore.window.UpdateCal);


Ext.QuickTips.init();