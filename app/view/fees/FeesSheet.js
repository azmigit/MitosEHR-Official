//******************************************************************************
// new.ejs.php
// New Patient Entry Form
// v0.0.1
// 
// Author: Ernest Rodriguez
// Modified: GI Technologies, 2011
// 
// MitosEHR (Electronic Health Records) 2011
//******************************************************************************
Ext.define('App.view.fees.FeesSheet', {
    extend:'App.classes.RenderPanel',
    id:'panelFeesSheet',
    pageTitle:'Fees Sheet',
    uses:['App.classes.GridPanel'],

    initComponent:function () {
        var me = this;

        me.pageBody = Ext.create('Ext.form.Panel', {
            title:'Fees Form',
            defaults:{
                bodyStyle:'padding:15px',
                bodyBorder:true,
                labelWidth:110
            },
            items:[

            ],

            bbar:[
                '->',
                {
                    itemId:'move-next',
                    text:'Next',
                    handler:function () {
                        app.navigateTo('panelPayments');
                    }
                }
            ]
        });

        me.callParent(arguments);
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

}); //end FeesSheet class