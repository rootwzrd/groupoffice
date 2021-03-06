/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 * @author WilmarVB <wilmar@intermesh.nl>
 */

GO.sieve.CriteriumRecord = Ext.data.Record.create([{
	name: 'test',
	type: 'string'
},
{
	name: 'not',
	type: 'string'
},
{
	name: 'type',
	type: 'string'
},
{
	name: 'arg1',
	type: 'string'
},
{
	name: 'arg2',
	type: 'string'
}]);

GO.sieve.CriteriumCreatorDialog = function(config){
	config = config || {};

	this._buildForm();

	config.title=GO.sieve.lang.setFilter;
	config.border=false;
	config.layout= 'fit';
	config.height=95;
	config.width=640;
	config.baseParams={
		task : 'addAction',
		account_id : 0,
		script_name : '',
		rule_name : '',
		script_index : 0
	};
	config.items=[this.formPanel];
	
	config.buttons = [{
		text : GO.lang['cmdOk'],
		handler : function() {
			if (this.formPanel.getForm().isValid()) {
				this.fireEvent('criteriumPrepared',this._prepareValuesForStorage());
				this.hide();
				this._resetForm();
			}
		},
		scope : this
	}, {
		text : GO.lang['cmdCancel'],
		handler : function() {
			this.hide();
			this._resetForm();
		},
		scope : this
	}];

	GO.sieve.CriteriumCreatorDialog.superclass.constructor.call(this, config);
}

Ext.extend(GO.sieve.CriteriumCreatorDialog, GO.Window,{
	_recordId : -1,

	show : function(record) {
		this._recordId = -1;
		if (typeof(record)=='object') {
			
			this._recordId = record.get('id');
			
			switch(record.get('test')) {
				case 'size':
					// We know for sure this record corresponds with a size criterium
					this.cmbField.setValue('size');
					this._transForm('size');
					// Put the Kilo/Mega/Giga scalar in the right input field
					var lastChar = record.data.arg.substr(record.data.arg.length-1,1);
					var everythingBeforeTheLastChar = record.data.arg.substr(0,record.data.arg.length-1);
					if(lastChar != 'K' && lastChar != 'M' && lastChar != 'G')
					{
						everythingBeforeTheLastChar = everythingBeforeTheLastChar+lastChar;
						lastChar = 'B';
					}
					this.cmbUnderOver.setValue(record.get('type'));
					this.numberCriterium.setValue(everythingBeforeTheLastChar);
					this.rgSize.setValue(lastChar);
					break;
				case 'exists':
					// This record can be of one of the following kinds of criteria:
					// Custom, Subject, Recipient (To), Sender (From)
					var kind = record.get('arg');
					if (kind=='Subject'||kind=='From'||kind=='To'||kind=='X-Spam-Flag')
						this.cmbField.setValue(kind);
					else
						this.cmbField.setValue('custom');
					this._transForm(this.cmbField.getValue());
					this._setOperatorField(record);
					break;
				case 'header':
					// This record can be of one of the following kinds of criteria:
					// Custom, Subject, Recipient (To), Sender (From), X-Spam-Flag
					var kind = record.get('arg1');
					if (kind=='Subject'||kind=='From'||kind=='To'||kind=='X-Spam-Flag')
						this.cmbField.setValue(kind);
					else
						this.cmbField.setValue('custom');
					this._transForm(this.cmbField.getValue());
					this.txtCriterium.setValue(record.get('arg2'));
					this.txtCustom.setValue(record.get('arg1'));
					this._setOperatorField(record);
					break;
			}
		}
		GO.sieve.CriteriumCreatorDialog.superclass.show.call(this);
	},
	
	_setOperatorField : function(record) {
		var type = record.get('type');
		var not = record.get('not');
		
		switch (type) {
			case 'contains':
				if (not)
					this.cmbOperator.setValue('notcontains');
				else
					this.cmbOperator.setValue('contains');
				break;
			case 'is':
				if (not)
					this.cmbOperator.setValue('notis');
				else
					this.cmbOperator.setValue('is');
				break;
			default:
				if (not)
					this.cmbOperator.setValue('notexists')
				else
					this.cmbOperator.setValue('exists')
				break;
		}
	},
	
	/****************************************************************************
	 * Often, especially when this.cmbField changes value, different form fields
	 * must be shown than the current ones. This method does that.
	 ****************************************************************************/
	
	_transForm : function(type){
		switch(type)
		{
			case 'size':
				this._toggleFieldUse(this.txtCustom,false);
				this._toggleFieldUse(this.cmbOperator,false);
				this._toggleFieldUse(this.txtCriterium,false);
				this._toggleFieldUse(this.numberCriterium,true);
				this._toggleFieldUse(this.cmbUnderOver,true);
				this._toggleFieldUse(this.rgSize,true);
				break;
			case 'From':
			case 'To':
			case 'Subject':
//			case 'body':
				this._toggleFieldUse(this.txtCustom,false);
				this._toggleFieldUse(this.cmbOperator,true);
				this._toggleFieldUse(this.txtCriterium,!(this.cmbOperator.getValue() == 'exists' || this.cmbOperator.getValue() == 'notexists'));
				this._toggleFieldUse(this.numberCriterium,false);
				this._toggleFieldUse(this.cmbUnderOver,false);
				this._toggleFieldUse(this.rgSize,false);
				break;
			case 'X-Spam-Flag':
				this._toggleFieldUse(this.txtCustom,false);
				this._toggleFieldUse(this.cmbOperator,false);
				this._toggleFieldUse(this.txtCriterium,false);
				this._toggleFieldUse(this.numberCriterium,false);
				this._toggleFieldUse(this.cmbUnderOver,false);
				this._toggleFieldUse(this.rgSize,false);
				break;
			case 'custom':
				this._toggleFieldUse(this.txtCustom,true);
				this._toggleFieldUse(this.cmbOperator,true);
				this._toggleFieldUse(this.txtCriterium,!(this.cmbOperator.getValue() == 'exists' || this.cmbOperator.getValue() == 'notexists'));
				this._toggleFieldUse(this.numberCriterium,false);
				this._toggleFieldUse(this.cmbUnderOver,false);
				this._toggleFieldUse(this.rgSize,false);
				break;
			default:
				this._toggleFieldUse(this.txtCustom,false);
				this._toggleFieldUse(this.cmbOperator,false);
				this._toggleFieldUse(this.txtCriterium,false);
				this._toggleFieldUse(this.numberCriterium,false);
				this._toggleFieldUse(this.cmbUnderOver,false);
				this._toggleFieldUse(this.rgSize,false);
				break;
		}
		this.doLayout();
	},
	
	_resetForm : function(){
		this.formPanel.form.reset();
		this._transForm(this.cmbField.getValue());
		this._recordId=-1;
	},
	
	_toggleFieldUse : function(component,use) {
		component.setVisible(use);
		component.setDisabled(!use);
	},
	
	/****************************************************************************
	 * The following methods are needed right before passing the resulting
	 * criterium of this dialog to CriteriumGrid. Passing is done with the
	 * criteriumPrepared event.
	 ****************************************************************************/
	
	_prepareValuesForStorage : function() {
		// Build up the data before adding the data to the grid.
		var _test = '';
		var _not = true;
		var _type = '';
		var _arg = '';
		var _arg1 = this.cmbField.getValue();
		var _arg2 = this.txtCriterium.getValue();

		// Workaround for _arg2 check
		if(this.cmbOperator.getValue() == 'exists' || this.cmbOperator.getValue() == 'notexists' || this.cmbField.getValue() == 'X-Spam-Flag')
			_arg2 = 'sometext';

		// Check the input value of the txtBox
		if(_arg2 != '')
		{
			switch(this.cmbField.getValue()) {
				case 'custom':
					if(this.cmbOperator.getValue() == 'exists' || this.cmbOperator.getValue() == 'notexists') {
						_test = 'exists';
						_arg = this.txtCustom.getValue();
						_arg1 = '';
						_arg2 = '';
					} else {
						_test = 'header';
						_arg = '';
						_arg1 = this.txtCustom.getValue();
						_arg2 = this.txtCriterium.getValue();
					}
					_not = this._evaluateIfNotFields();
					_type = this._evaluateTypeFields();
					break;
				case 'X-Spam-Flag':
					_test = 'header';
					_not = false;
					_type	= this.cmbOperator.getValue();
					_arg = '';
					_arg1 = 'X-Spam-Flag';
					_arg2 = 'YES';
					break;
				default:
					if(this.cmbOperator.getValue() == 'exists' || this.cmbOperator.getValue() == 'notexists')
					{
						_test = 'exists';
						_type = '';
						_arg = this.cmbField.getValue();
						_arg1 = '';
						_arg2 = '';
						_not = this._evaluateIfNotFields();
					}
					else
					{
						_test = 'header';
						_arg = '';
						_arg1 = this.cmbField.getValue();
						_arg2 = this.txtCriterium.getValue();
						_not = this._evaluateIfNotFields();
						_type = this._evaluateTypeFields();
					}
					break;
			}
			
		} else if (this.cmbField.getValue()=='size') {
			_test = 'size';
			_not = false;
			_type	= this.cmbUnderOver.getValue();
			_arg1 = '';
			_arg2 = '';

			if(this.rgSize.getValue().inputValue == 'B')
				_arg = this.numberCriterium.getValue();
			else
				_arg = this.numberCriterium.getValue() + this.rgSize.getValue().inputValue;
		}
		
		return {
			id: this._recordId,
			test: _test,
			not:  _not,
			type: _type,
			arg:	_arg,
			arg1: _arg1,
			arg2: _arg2
		};
		
	},
	
	_evaluateIfNotFields : function() {
		return this.cmbOperator.getValue() == 'notexists' || this.cmbOperator.getValue() == 'notcontains' || this.cmbOperator.getValue() == 'notis';
	},
	
	_evaluateTypeFields : function() {
		if(this.cmbOperator.getValue() == 'contains' ||this.cmbOperator.getValue() == 'notcontains')
			return 'contains';
		else if(this.cmbOperator.getValue() == 'is' ||this.cmbOperator.getValue() == 'notis')
			return 'is';
		else
			return '';
	},
	
	/****************************************************************************
	 * Lay out the actual components
	 ****************************************************************************/
	
	_buildForm : function() {
		this.cmbField = new GO.form.ComboBox({
			hiddenName:'arg1',
			valueField:'value',
			displayField:'field',
			store: GO.sieve.cmbFieldStore,
			mode:'local',
			triggerAction:'all',
			editable:false,
			forceSelection:true,
			allowBlank:false,
			width:200,
			emptyText:GO.sieve.lang.pleaseSelectOne
		});

		this.cmbOperator = new GO.form.ComboBox({
			hiddenName:'type',
			value:'contains',
			valueField:'value',
			displayField:'field',
			store: GO.sieve.cmbOperatorStore,
			mode:'local',
			triggerAction:'all',
			editable:false,
			selectOnFocus:true,
			forceSelection:true,
			allowBlank:false,
			width:110,
			emptyText:GO.sieve.lang.operator,
			disabled: true,
			hidden: true
		});

		this.txtCriterium = new Ext.form.TextField({
			name: 'arg2' ,
			emptyText: '...',
			allowBlank:false,
			width:150,
			disabled: true,
			hidden: true
		});
		
		this.numberCriterium = new Ext.form.NumberField({
			name: 'arg2' ,
			emptyText: '...',
			allowBlank:false,
			width:150,
			disabled: true,
			hidden: true
		});

		this.txtCustom = new Ext.form.TextField({
			name: 'custom' ,
			emptyText: GO.sieve.lang.custom,
			fieldLabel:GO.sieve.lang.custom,
			allowBlank:false,
			width:140,
			disabled: true,
			hidden: true
		});

		this.cmbUnderOver = new GO.form.ComboBox({
			hiddenName:'underover',
			value:'under',
			valueField:'value',
			displayField:'field',
			store: GO.sieve.cmbUnderOverStore,
			mode:'local',
			triggerAction:'all',
			editable:false,
			selectOnFocus:true,
			forceSelection:true,
			allowBlank:false,
			width:100,
			disabled: true,
			hidden: true
		});

		this.rgSize = new Ext.form.RadioGroup({
			columns: 4,
			width:150,
			hidden: true,
			items: [
			{
				items:{boxLabel: 'B', name: 'size', inputValue: 'B', style: 'margin-left: 4px; margin-right: -2px;'}
			},
			{
				items:{boxLabel: 'KB', name: 'size', inputValue: 'K', style: 'margin-left: 4px; margin-right: -2px;', checked: true}
			},
			{
				items:{boxLabel: 'MB', name: 'size', inputValue: 'M', style: 'margin-left: 4px; margin-right: -2px;'}
			},
			{
				items:{boxLabel: 'GB', name: 'size', inputValue: 'G', style: 'margin-left: 4px; margin-right: -2px;'}
			}],
			hideLabel:true
		})

		this.cmbField.on('select', function(combo, record){
			this._transForm(record.data.value);
		},this);

		this.cmbOperator.on('select', function(combo,record){
			this._toggleFieldUse(this.txtCriterium,!(this.cmbOperator.getValue() == 'exists' || this.cmbOperator.getValue() == 'notexists'));
		},this);
	
		this.formPanel = new Ext.form.FormPanel({
			bodyStyle: 'padding:5px',
			border: false,
			items: [
				{
					xtype:'compositefield',
					items:[
						this.cmbField,
						this.txtCustom,
						this.cmbUnderOver,
						this.cmbOperator, 
						this.txtCriterium,
						this.numberCriterium,
						this.rgSize
					],
					hideLabel: true
				}
			]
		});
	}
});