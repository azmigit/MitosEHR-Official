<?php 
//******************************************************************************
// new.ejs.php
// New Patient Entry Form
// v0.0.1
// 
// Author: Ernest Rodriguez
// Modified: Gino Rivera
// 
// MitosEHR (Eletronic Health Records) 2011
//******************************************************************************
session_name ( "MitosEHR" );
session_start();
session_cache_limiter('private');

include_once("../../../library/I18n/I18n.inc.php");

//******************************************************************************
// Reset session count 10 secs = 1 Flop
//******************************************************************************
$_SESSION['site']['flops'] = 0;

?>
<script type="text/javascript">
// *************************************************************************************
// Sencha trying to be like a language
// using requiered to load diferent components
// *************************************************************************************
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('Ext.ux', '<?php echo $_SESSION['dir']['ux']; ?>');
Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.util.*',
    'Ext.state.*',
    'Ext.toolbar.Paging',
    'Ext.TaskManager.*',
    'Ext.ux.SlidingPager'
]);

Ext.onReady(function(){
	
	//******************************************************************************
	// Sanitizing Objects
	// Destroy them, if already exists in the browser memory.
	// This destructions must be called for all the objects that
	// are rendered on the document.body 
	//******************************************************************************
	if ( Ext.getCmp('winAddressbook') ){ Ext.getCmp('winAddressbook').destroy(); }
	
	// *************************************************************************************
	// Structure of the message record
	// creates a subclass of Ext.data.Record
	//
	// This should be the structure of the database table
	// 
	// *************************************************************************************
	var addressbookRecord = Ext.define("addressbookRecord", {extend: "Ext.data.Model", fields: [
		{name: 'id',                    type: 'int',              mapping: 'id'},
		{name: 'username',              type: 'string',           mapping: 'username'},
		{name: 'password',              type: 'string',           mapping: 'password'},
		{name: 'authorized',            type: 'string',           mapping: 'authorized'},
		{name: 'info',                  type: 'string',           mapping: 'info'},
		{name: 'source',                type: 'int',              mapping: 'source'},
		{name: 'fname',                 type: 'string',           mapping: 'fname'},
		{name: 'mname',                 type: 'string',           mapping: 'mname'},
		{name: 'lname',                 type: 'string',           mapping: 'lname'},
		{name: 'fullname',              type: 'string',           mapping: 'fullname'},
		{name: 'federaltaxid',          type: 'string',           mapping: 'federaltaxid'},
		{name: 'federaldrugid',         type: 'string',           mapping: 'federaldrugid'},
		{name: 'upin',                  type: 'string',           mapping: 'upin'},
		{name: 'facility',              type: 'string',           mapping: 'facility'},
		{name: 'facility_id',           type: 'int',              mapping: 'facility_id'},
		{name: 'see_auth',              type: 'int',              mapping: 'see_auth'},
		{name: 'active',                type: 'int',              mapping: 'active'},
		{name: 'npi',                   type: 'string',           mapping: 'npi'},
		{name: 'title',                 type: 'string',           mapping: 'title'},
		{name: 'specialty',             type: 'string',           mapping: 'specialty'},
		{name: 'billname',              type: 'string',           mapping: 'billname'},
		{name: 'email',                 type: 'string',           mapping: 'email'},
		{name: 'url',                   type: 'string',           mapping: 'url'},
		{name: 'assistant',             type: 'string',           mapping: 'assistant'},
		{name: 'organization',          type: 'string',           mapping: 'organization'},
		{name: 'valedictory',           type: 'string',           mapping: 'valedictory'},
		{name: 'fulladdress',           type: 'string',           mapping: 'fulladdress'},
		{name: 'street',                type: 'string',           mapping: 'street'},
		{name: 'streetb',               type: 'string',           mapping: 'streetb'},
		{name: 'city',                  type: 'string',           mapping: 'city'},
		{name: 'state',                 type: 'string',           mapping: 'state'},
		{name: 'zip',                   type: 'string',           mapping: 'zip'},
		{name: 'street2',               type: 'string',           mapping: 'street2'},
		{name: 'streetb2',              type: 'string',           mapping: 'streetb2'},
		{name: 'city2',                 type: 'string',           mapping: 'city2'},
		{name: 'state2',                type: 'string',           mapping: 'state2'},
		{name: 'zip2',                  type: 'string',           mapping: 'zip2'},
		{name: 'phone',                 type: 'string',           mapping: 'phone'},
		{name: 'fax',                   type: 'string',           mapping: 'fax'},
		{name: 'phonew1',               type: 'string',           mapping: 'phonew1'},
		{name: 'phonew2',               type: 'string',           mapping: 'phonew2'},
		{name: 'phonecell',             type: 'string',           mapping: 'phonecell'},
		{name: 'notes',                 type: 'string',           mapping: 'notes'},
		{name: 'cal_ui',                type: 'string',           mapping: 'cal_ui'},
		{name: 'taxonomy',              type: 'string',           mapping: 'taxonomy'},
		{name: 'ssi_relayhealth',       type: 'string',           mapping: 'ssi_relayhealth'},
		{name: 'calendar',              type: 'int',              mapping: 'calendar'},
		{name: 'abook_type',            type: 'string',           mapping: 'abook_type'},
		{name: 'pwd_expiration_date',   type: 'string',           mapping: 'pwd_expiration_date'},
		{name: 'pwd_history1',          type: 'string',           mapping: 'pwd_history1'},
		{name: 'pwd_history2',          type: 'string',           mapping: 'pwd_history2'},
		{name: 'default_warehouse',     type: 'string',           mapping: 'default_warehouse'},
		{name: 'ab_name',               type: 'string',           mapping: 'ab_name'},
		{name: 'ab_title',              type: 'string',           mapping: 'ab_title'}
	],
		idProperty: 'id',
	});

	var storeAddressbook = new Ext.data.Store({
		model		: 'addressbookRecord',
		noCache		: true,
    	autoSync	: false,
	    proxy		: {
	    	type	: 'ajax',
		    api		: {
		      read      : 'interface/miscellaneous/addressbook/data_read.ejs.php',
		      create    : 'interface/miscellaneous/addressbook/data_create.ejs.php',
		      update    : 'interface/miscellaneous/addressbook/data_update.ejs.php'
		    //destroy 	:  <- You can not destroy conatacts, HIPPA Compliant
	   	 	},
	   	 	reader: {
	            type			: 'json',
	            idProperty		: 'id',
	            totalProperty	: 'totals',
	            root			: 'row'
	    	},
	    	writer: {
				type	 		: 'json',
				writeAllFields	: true,
				allowSingle	 	: true,
				encode	 		: true,
				root	 		: 'row'
			}
	    },
	    autoLoad: true
	});
	function localck(val) {
	    if (val == 'X') {
	        return '<img src="ui_icons/yes.gif" />';
	    } else {
	        return '';
	    }
	    return val;
	}
	
	// *************************************************************************************
	// Structure, data for Titles
	// AJAX -> component_data.ejs.php
	// *************************************************************************************
	Ext.define("Titles", {extend: "Ext.data.Model", fields: [
		{name: 'option_id', type: 'string'},
	    {name: 'title', type: 'string'}
	],
		idProperty: 'option_id'
	});
	var storeTitles = new Ext.data.Store({
		model		: 'Titles',
		proxy		: {
			type	: 'ajax',
			url		: 'interface/miscellaneous/addressbook/component_data.ejs.php?task=titles',
			reader	: {
				type			: 'json',
				idProperty		: 'option_id',
				totalProperty	: 'totals',
				root			: 'row'
			}
		},
		autoLoad: true
	}); // End storeTitles
	
	// *************************************************************************************
	// Structure, data for Types
	// AJAX -> component_data.ejs.php
	// *************************************************************************************
	Ext.define("Types", {extend: "Ext.data.Model", fields: [
		{name: 'option_id', type: 'string'},
	    {name: 'title', type: 'string'}
	],
		idProperty: 'option_id'
	});
	var storeTypes = new Ext.data.Store({
		model		: 'Types',
		proxy		: {
			type	: 'ajax',
			url		: 'interface/miscellaneous/addressbook/component_data.ejs.php?task=types',
			reader	: {
				type			: 'json',
				idProperty		: 'option_id',
				totalProperty	: 'totals',
				root			: 'row'
			}
		},
		autoLoad: true
	}); // End storeTypes
	
	// *************************************************************************************
	// Facility Form
	// Add or Edit purpose
	// *************************************************************************************
	var frmAddressbook = new Ext.form.FormPanel({
	  	id          : 'frmAddressbook',
	  	bodyStyle   : 'padding: 5px;',
	  	autoWidth   : true,
		width	  	  : 495,
		border      : false,
		hideLabels  : true,
	  	defaults: {
			labelWidth: 89,
		    anchor: '100%',
		    layout: {
		    	type: 'hbox',
		        defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}
		    }
		},
	 	 items: [{
	      items: 
	      [ 
	        { xtype: 'textfield', hidden: true, id: 'id', name: 'id'},
	        { xtype: 'fieldcontainer',
	          msgTarget : 'side', 
	          items: [
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('Type'); ?>: '},
	            { width: 130, xtype: 'combo', id: 'abook_type', name: 'abook_type', autoSelect: true, displayField: 'title', valueField: 'option_id', hiddenName: 'abook_type', mode: 'local', triggerAction: 'all', store: storeTypes }
	          ] 
	        },{ xtype: 'fieldcontainer',
	          msgTarget : 'side', 
	          items: [
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('First, Middle, Last'); ?>: '},
	            { width: 50,  xtype: 'combo',     id: 'title', name: 'title', autoSelect: true, displayField: 'title', valueField: 'option_id', hiddenName: 'title', mode: 'local', triggerAction: 'all', store: storeTitles },
	            { width: 130, xtype: 'textfield', id: 'fname', name: 'fname' },
	            { width: 100, xtype: 'textfield', id: 'mname', name: 'mname' },
	            { width: 300, xtype: 'textfield', id: 'lname', name: 'lname' }
	          ] 
	        },{ 
	          xtype: 'fieldcontainer',
	          msgTarget : 'side', 
	          items: [
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('Specialty'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'specialty',    name: 'specialty' },
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('Organization'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'organization', name: 'organization' },
	            { width: 80,  xtype: 'displayfield', value: '<?php i18n('Valedictory'); ?>: '},
	            { width: 140, xtype: 'textfield', id: 'valedictory',  name: 'valedictory' }
	          ] 
	        },{html: '<hr style="margin:5px 0; border: 1px solid #ccc">', border:false},{ 
	          xtype: 'fieldcontainer',
	          items: [
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('Address'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'street',   name: 'street' },
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('Addrress Cont'); ?>: '},
	            { width: 360, xtype: 'textfield', id: 'streetb',  name: 'streetb' }
	          ] 
	        },{ 
	          xtype: 'fieldcontainer',
	          items: [
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('City'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'city',     name: 'city' },
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('State'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'state',    name: 'state' },
	            { width: 80,  xtype: 'displayfield', value: '<?php i18n('Postal Code'); ?>: '},
	            { width: 140, xtype: 'textfield', id: 'zip',      name: 'zip' }
	          ] 
	        },{html: '<hr style="margin:5px 0; border: 1px solid #ccc">', border:false},{ 
	          xtype: 'fieldcontainer',
	          items: [
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('Address'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'street2',  name: 'street2' },
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('Cont.'); ?>: '},
	            { width: 360, xtype: 'textfield', id: 'streetb2', name: 'streetb2' },
	          ] 
	        },{ 
	          xtype: 'fieldcontainer',
	          items: [
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('City'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'city2',    name: 'city2' },
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('State'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'state2',   name: 'state2' },
	            { width: 80,  xtype: 'displayfield', value: '<?php i18n('Postal Code'); ?>: '},
	            { width: 140, xtype: 'textfield', id: 'zip2',     name: 'zip2' }
	          ]
	        },{html: '<hr style="margin:5px 0; border: 1px solid #ccc">', border:false},{ 
	          xtype: 'fieldcontainer',
	          items: [
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('Home Phone'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'phone',     name: 'phone' },
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('Mobile Phone'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'phonecell', name: 'phonecell' }
	          ]
	        },{ 
	          xtype: 'fieldcontainer',
	          items: [
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('Work Phone'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'phonew1', name: 'phonew1' },
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('Work Phone'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'phonew2', name: 'phonew2' },
	            { width: 80,  xtype: 'displayfield', value: '<?php i18n('FAX'); ?>: '},
	            { width: 140, xtype: 'textfield', id: 'fax',     name: 'fax'   }
	          ]
	        },{html: '<hr style="margin:5px 0; border: 1px solid #ccc">', border:false},{ 
	          xtype: 'fieldcontainer',
	          items: [
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('Email'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'email',     name: 'email' },
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('Assistant'); ?>: '},
	            { width: 130, xtype: 'textfield', id: 'assistant', name: 'assistant' },
	            { width: 80,  xtype: 'displayfield', value: '<?php i18n('Website'); ?>: '},
	            { width: 140, xtype: 'textfield', id: 'url',       name: 'url' }
	          ]
	        },{html: '<hr style="margin:5px 0; border: 1px solid #ccc">', border:false},{ 
	          xtype: 'fieldcontainer',
	          items: [
	            { width: 100, xtype: 'displayfield', value: '<?php i18n('UPIN'); ?>: '},
	            { width: 80,  xtype: 'textfield', id: 'upin',          name: 'upin' },
	            { width: 80,  xtype: 'displayfield', value: '<?php i18n('NPI'); ?>: '},
	            { width: 80,  xtype: 'textfield', id: 'npi',           name: 'npi', },
	            { width: 80,  xtype: 'displayfield', value: '<?php i18n('TIN'); ?>: '},
	            { width: 80,  xtype: 'textfield', id: 'federaltaxid',  name: 'federaltaxid' },
	            { width: 80,  xtype: 'displayfield', value: '<?php i18n('Taxonomy'); ?>: '},
	            { width: 80,  xtype: 'textfield', id: 'taxonomy',      name: 'taxonomy' }
	          ]
	        },{html: '<hr style="margin:5px 0">', border:false},
	        { width: 705, xtype: 'htmleditor', id: 'notes', name: 'notes', emptyText: 'Notes', },
	      ]
	  }], 
	  // Window Bottom Bar
	  buttons:[{
	    text      :'<?php i18n('Save'); ?>',
	    ref       : '../save',
	    iconCls   : 'save',
	    handler: function() {
	
	      //----------------------------------------------------------------
	      // 1. Convert the form data into a JSON data Object
	      // 2. Re-format the Object to be a valid record (FacilityRecord)
	      //----------------------------------------------------------------
	      var obj = eval('(' + Ext.util.JSON.encode(frmAddressbook.getForm().getValues()) + ')');
	      var rec = new addressbookRecord(obj);
	      
	      //----------------------------------------------------------------
	      // Check if it has to add or update
	      // Update: 1. Get the record from store, 2. get the values from the form, 3. copy all the 
	      // values from the form and push it into the store record.
	      // Add: The re-formated record to the dataStore
	      //----------------------------------------------------------------
	      if (frmAddressbook.getForm().findField('id').getValue()){ // Update
	        var record = storeAddressbook.getAt(rowPos);
	        var fieldValues = frmAddressbook.getForm().getValues();
	        for (key in fieldValues){ record.set( key, fieldValues[key] ); }
	      } else { // Add
	        storeAddressbook.add( rec );
	      }
	
	      storeAddressbook.save();          // Save the record to the dataStore
	      storeAddressbook.commitChanges(); // Commit the changes
	      storeAddressbook.reload();        // Reload the dataSore from the database
	      winAddressbook.hide();            // Finally hide the dialog window
	    }
	  },{
	    text:'<?php i18n('Close'); ?>',
	    iconCls: 'delete',
	    handler: function(){ winAddressbook.hide(); }
	  }]
	});

	// *************************************************************************************
	// Message Window Dialog
	// *************************************************************************************
	var winAddressbook = new Ext.Window({
		id          : 'winAddressbook',
		width       : 773,
		autoHeight  : true,
		modal       : true,
		resizable   : false,
	  	autoScroll  : true,
		title       : '<?php i18n('Add or Edit Contact'); ?>',
		closeAction : 'hide',
		renderTo    : document.body,
		items: [ frmAddressbook ]
	}); // END WINDOW

	// *************************************************************************************
	// Create the GridPanel
	// *************************************************************************************
	var addressbookGrid = new Ext.grid.GridPanel({
  		id          : 'addressbookGrid',
  		store       : storeAddressbook,
	  	border      : false,    
	  	frame       : false,
	  	loadMask    : true,
  		viewConfig  : {stripeRows: true},
    	listeners	: {
	   		// -----------------------------------------
	   	  	// Single click to select the record
	   	  	// -----------------------------------------
	   	  	itemclick: {
	   			fn: function(DataView, record, item, rowIndex, e){
					Ext.getCmp('frmAddressbook').getForm().reset();
	   		  		var rec = storeAddressbook.getAt(rowIndex);
	   		  		Ext.getCmp('frmAddressbook').getForm().loadRecord(rec);
					currRec = rec;
            		rowPos = rowIndex;
	   		  	}
	   	  	},
	   	  	// -----------------------------------------
	   	  	// Double click to select the record, and edit the record
	   	  	// -----------------------------------------
	   	  	itemdblclick: {
	   			fn: function(DataView, record, item, rowIndex, e){
					Ext.getCmp('frmAddressbook').getForm().reset();
					Ext.getCmp('editAddressbook').enable();
					Ext.getCmp('cmdDelete').enable();
					var rec = storeAddressbook.getAt(rowIndex); // get the record from the store
					Ext.getCmp('frmAddressbook').getForm().loadRecord(rec); // load the record selected into the form
					currRec = rec;
            		rowPos = rowIndex;
					winAddressbook.setTitle('<?php i18n("Edit Contact"); ?>');
	   		  		winAddressbook.show();
	   		  	}
	  	  	}
	  	},
		columns: [
		    // Hidden cells
		    {header: 'id', sortable: false, dataIndex: 'id', hidden: true},
		    // Viewable cells
		    { width: 150, header: '<?php i18n('Name'); ?>', sortable: true, dataIndex: 'fullname' },
		    { width: 50,  header: '<?php i18n('Local'); ?>', sortable: true, dataIndex: 'username', renderer : localck },
		    { header: '<?php i18n('Type'); ?>', sortable: true, dataIndex: 'ab_title' },
		    { header: '<?php i18n('Specialty'); ?>', sortable: true, dataIndex: 'specialty' },
		    { header: '<?php i18n('Phone'); ?>', sortable: true, dataIndex: 'phonew1' },
		    { header: '<?php i18n('Mobile'); ?>', sortable: true, dataIndex: 'phonecell' },
		    { header: '<?php i18n('Fax'); ?>', sortable: true, dataIndex: 'fax' },
		    { header: '<?php i18n('Email'); ?>', sortable: true, dataIndex: 'email' },
		    { width: 150, header: '<?php i18n('Primary Address'); ?>', sortable: true, dataIndex: 'fulladdress' }
  		],
  		dockedItems: [{
	  	  	xtype: 'toolbar',
		  	dock: 'top',
		  	items: [{
				id        : 'addAddressbook',
			    text      : '<?php i18n("Add Contact"); ?>',
			    iconCls   : 'icoAddressBook',
			    handler   : function(){
			    	Ext.getCmp('frmAddressbook').getForm().reset(); // Clear the form
			      	winUsers.show();
				  	winAddressbook.setTitle('<?php i18n("Add Contact"); ?>'); 
			    }
			},'-',{
			    id        : 'editAddressbook',
			    text      : '<?php i18n("Edit Contact"); ?>',
			    iconCls   : 'edit',
			    disabled  : true,
			    handler: function(){ 
					winUsers.setTitle('<?php i18n("Edit Contact"); ?>'); 
			    	winAddressbook.show();
			    }
		  	}]					    
	  	}]
	}); // END GRID
	
	//******************************************************************************
	// Render panel
	//******************************************************************************
	var topRenderPanel = Ext.create('Ext.panel.Panel', {
		title		: '<?php i18n('Address Book'); ?>',
		renderTo	: Ext.getCmp('MainApp').body,
		layout		: 'fit',
		height		: Ext.getCmp('MainApp').getHeight(),
	  	frame 		: false,
		border 		: false,
		id			: 'topRenderPanel',
		items		: [addressbookGrid]
	});

}); // End ExtJS
</script>




