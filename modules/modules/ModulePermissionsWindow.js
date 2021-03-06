GO.modules.ModulePermissionsWindow = Ext.extend(GO.Window,{
	
	module_id : '',
	initComponent : function(){
		this.permissionsTab = new GO.grid.PermissionsPanel({
					title : GO.users.lang.useModule
				});
				
		Ext.apply(this,{
			title : GO.lang['strPermissions'],
			layout : 'fit',
			modal : false,
			height : 500,
			width : 440,
			modal:true,
			closable:false,
			closeAction:'hide',
			items : [this.permissionsTab],
			buttons : [{
				text : GO.lang['cmdOk'],
				handler : function() {
					GO.request({
						url : 'modules/module/checkDefaultModels',
						params : {
							moduleId : this.module_id
						},
						success : function(response, options, result) {
							this.hide();
						},
						scope : this
					});
				},
				scope : this
			}]
				  
		});
		
		GO.modules.ModulePermissionsWindow.superclass.initComponent.call(this);
	},
	show: function(moduleId, name, acl_id) {
		this.module_id=moduleId;
		
		this.setTitle(GO.lang['strPermissions'] + ' ' + name);		
		GO.modules.ModulePermissionsWindow.superclass.show.call(this);		
		this.permissionsTab.setAcl(acl_id);		
	}
})