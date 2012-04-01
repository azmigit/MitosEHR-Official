//******************************************************************************
// Billing.ejs.php
// Billing Forms
// v0.0.1
// Author: Emmanuel J. Carrasquillo
// Modified:
// MitosEHR (Electronic Health Records) 2011
//******************************************************************************
Ext.define('App.view.fees.Billing', {
    extend       : 'App.classes.RenderPanel',
    id           : 'panelBilling',
    pageTitle    : 'Billing',
    uses         : [ 'App.classes.GridPanel' ],

    initComponent: function() {
        var me = this;

//		me.billingGrid = Ext.create('Ext.form.Panel', {
//			title    : 'Billing History',
//            defaults:{
//                bodyStyle:'padding:15px',
//                bodyBorder:true,
//                labelWidth:110
//            },
//            items:[
//                {
//                    xtype:'container',
//                    margin:'5 5 0 5',
//                    layout:'hbox',
//                    defaults:{
//                        flex:1,
//                        height:300,
//                        stripeRows:true
//                    },
//                    items:[
//                        {
//                            xtype:'grid',
//                            title:'Search Criteria',
//                            itemId:'leftCol',
//                            margin:'0 5 0 0',
//                            multiSelect:true,
//
//                            store:me.cptCodesGridStore,
//                            viewConfig:{
//                                copy:true,
//                                plugins:[
//                                    {
//                                        ptype:'gridviewdragdrop',
//                                        dragGroup:'firstSearchCriteriaGridDDGroup',
//                                        dropGroup:'secondSearchCriteriaGridDDGroup'
//                                    }
//                                ],
//                                listeners:{
//                                    scope:me,
//                                    drop:me.onSearchCriteriaDrop
//                                }
//                            },
//                            columns:[
//                                {
//                                    text:"Criteria Id",
//                                    width:100,
//                                    sortable:true,
//                                    dataIndex:'criteria_id'
//                                },
//                                {
//                                    text:"Criteria Description",
//                                    flex:1,
//                                    sortable:true,
//                                    dataIndex:'criteria_description_medium'
//                                }
//                            ]
//                        },
//                        {
//                            xtype:'grid',
//                            title:'Current Selected Criteria',
//                            itemId:'rightCol',
//
//                            store:me.secondGridStore,
//                            columns:[
//                                {
//                                    text:"Criteria Id",
//                                    width:100,
//                                    sortable:true,
//                                    dataIndex:'criteria_id'
//                                },
//                                {
//                                    text:"Description",
//                                    flex:1,
//                                    sortable:true,
//                                    dataIndex:'criteria_description'
//                                }
//                            ],
//                            viewConfig:{
//                                itemId:'view',
//                                plugins:[
//                                    {
//                                        ptype:'gridviewdragdrop',
//                                        dragGroup:'secondSearchCriteriaGridDDGroup',
//                                        dropGroup:'firstSearchCriteriaGridDDGroup'
//                                    }
//
//                                ],
//                                listeners:{
//                                    scope:me,
//                                    drop:me.onCurrentSelectedCriteriaDrop
//                                }
//                            }
//                        }
//                    ]
//                },
//                {
//                    xtype  : 'grid',
//                    title  : 'Payments Found',
//                    height : 180,
//                    margin : '5 5 5 5',
//                    columns: [
//                        {
//                            header : 'Payment Number'
//                        },
//                        {
//                            header : 'Date'
//                        }
//                    ]
//                }
//            ]
//		});

	    me.patientListStore = Ext.create('App.store.fees.Billing');



        me.encountersGrid = Ext.create('Ext.grid.Panel', {
            title: 'Encounters test',
            store: me.patientListStore,
            columns: [
                {
	                header: 'First Name',
	              dataIndex: 'fname',
                    flex: 1
                },
                {
	              header: 'Middle Name',
	              dataIndex: 'mname',
	              flex: 1
                },
                {
	              header: 'Last Name',
	              dataIndex: 'lname',
                    flex: 1
                },
                {
	              header: 'SS',
	              dataIndex: 'ss',
                    flex: 1
                }
            ],
            tbar:[
                {
                    xtype:'patienlivetsearch',
                    emptyText: 'Patient Live Search...',
                    width: 300,
                    margin: '0 5 0 0'

                },
                {
                    xtype:'datefield',
                    fieldLabel:'From',
                    labelWidth: 40,
	                action:'datefrom'
                },
                {
                    xtype:'datefield',
                    fieldLabel:'To',
                    labelWidth: 30,
	                action:'dateto'

                },
                '->',
                {
                    xtype: 'tbtext',
                    text: 'Past due:'
                },
                {
                    text: '30+',
                    enableToggle: true,
                    toggleGroup: 'pastduedates',
	                enableKeyEvents:true,
	                listeners:{

		                scope:me,
		                toggleHandler:function(button,state){

			                var datefrom = this.query('datefield[action="datefrom"]'),
				                dateto = this.query('datefield[action="dateto"]');
							say(button);
			                datefrom[0].reset();
			                dateto[0].reset();


		                }



	                }
                },
                {
                    text: '60+',
                    enableToggle: true,
                    toggleGroup: 'pastduedates'
                },
                {
                    text: '120+',
                    enableToggle: true,
                    toggleGroup: 'pastduedates'
                },
                {
                    text: '180+',
                    enableToggle: true,
                    toggleGroup: 'pastduedates'

                }



            ],
            plugins: Ext.create('App.classes.grid.RowFormEditing', {
                autoCancel  : false,
                errorSummary: false,
                clicksToEdit: 1,
                formItems   : [
                    Ext.create('App.view.patientfile.encounter.CurrentProceduralTerminology',{
                        height:350
                    })

                ]

            })

        });

        me.pageBody = [ me.encountersGrid ];
        me.callParent(arguments);
    }, // end of initComponent

    onSearchCriteriaDrop:function () {
        app.msg('Criteria removed from Current Selected Criteria');
    },

    onCurrentSelectedCriteriaDrop:function (node, data) {
        var me = this,
            index;

        app.msg('Search Criteria added to Current Selected Criteria');
        me.cptFormEdit.cancelEdit();
        index = me.secondGridStore.indexOf(data.records[0]);
        me.cptFormEdit.startEdit(index, 0);
    },

    /**
     * This function is called from MitosAPP.js when
     * this panel is selected in the navigation panel.
     * place inside this function all the functions you want
     * to call every this panel becomes active
     */
    onActive     : function(callback) {
        callback(true);
    }
}); //ens oNotesPage class