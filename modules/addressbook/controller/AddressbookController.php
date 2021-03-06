<?php
class GO_Addressbook_Controller_Addressbook extends GO_Base_Controller_AbstractModelController{
	
	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		$storeParams->debugSql();
		$multiSel = new GO_Base_Component_MultiSelectGrid(
						'books', 
						"GO_Addressbook_Model_Addressbook",$store, $params, true);		
		$multiSel->setFindParamsForDefaultSelection($storeParams);
		$multiSel->formatCheckedColumn();
		
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}
	
	protected $model = 'GO_Addressbook_Model_Addressbook';
	
	protected function remoteComboFields() {
		return array('user_id'=>'$model->user->name');
	}

	protected function beforeStore(&$response, &$params, &$store) {
		$store->setDefaultSortOrder('name','ASC');
		return parent::beforeStore($response, $params, $store);
	}
	
	protected function actionSearchSender($params) {

		$contacts = GO_Addressbook_Model_Contact::model()->findByEmail($params['email']);
		$response['success']=true;
		$response['results']=array();

		foreach($contacts as $contact)
		{
			$res_contact['id']=$contact->id;
			$res_contact['name']=$contact->name.' ('.$contact->addressbook->name.')';

			$response['results'][]=$res_contact;
		}
		return $response;
	}
	
	public function formatStoreRecord($record, $model, $store) {
		
		$record['user_name']=$model->user ? $model->user->name : 'unknown';
		if(GO::modules()->customfields){
			$record['contactCustomfields']=GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Addressbook_Model_Contact", $model->id);
			$record['companyCustomfields']=GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Addressbook_Model_Company", $model->id);
		}
		
		return parent::formatStoreRecord($record, $model, $store);
	}
	
	/**
	 * Function exporting addressbook contents to VCFs. Must be called from export.php.
	 * @param type $params 
	 */
	public function exportVCard($params) {
		$addressbook = GO_Addressbook_Model_Addressbook::model()->findByPk($params['addressbook_id']);
		
		$filename = $addressbook->name.'.vcf';
		GO_Base_Util_Http::outputDownloadHeaders(new GO_Base_FS_File($filename));		
	
		foreach ($addressbook->contacts(GO_Base_Db_FindParams::newInstance()->select('t.*')) as $contact)
			echo $contact->toVObject()->serialize();
	}
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		
//		if(isset($_FILES['files']['tmp_name'][0]))
//			$response = array_merge($response,$this->run("upload",$params,false));
		
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	protected function actionUpload($params) {
		//$params['a'] = $addressbook_id = $params['addressbook_id'];
		$import_filetype = isset($params['fileType']) ? ($params['fileType']) : null;
//				
//		
//		if (!empty($_FILES['import_file']['tmp_name']))
//			$import_filename = ($_FILES['import_file']['tmp_name']);
//		elseif (!empty($params['import_file']))
//			$import_filename = ($params['import_file']);
//		
////		$separator	= isset($params['separator']) ? ($params['separator']) : ',';
////		$quote	= isset($params['quote']) ? ($params['quote']) : '"';
//		$params['file'] = GO::config()->tmpdir.uniqid(time());
//		$response['success'] = true;
//		GO::debug($import_filename);
//
//		if(!move_uploaded_file($import_filename, $params['file'])) {
//			throw new Exception('Could not move '.$import_filename);
//	  }

//		$file = new GO_Base_Fs_File($_FILES['importFiles']['tmp_name']);
//	  $file->convertToUtf8();
		$params['file'] = $_FILES['files']['tmp_name'][0];
		ini_set('max_execution_time', 360);
		ini_set('memory_limit', '256M');
		$response = array();
		
	  switch($import_filetype) {
			case 'vcf':				
				$response = array_merge($response,$this->run("importVcf",$params,false));
				break;
			default:
				
				if($params['controller']=='GO_Addressbook_Controller_Contact')
					$controller = new GO_Addressbook_Controller_Contact();
				elseif($params['controller']=='GO_Addressbook_Controller_Company')
					$controller = new GO_Addressbook_Controller_Company();
				else
					throw new Exception("No or wrong controller given");
				
				$response = array_merge($response,$controller->run("ImportCsv",$params,false));
				break;
	  }		
		
		$response['success'] = true;
		return $response;
	}
	
	
	public function actionTruncate($params){
		$addressbook = GO_Addressbook_Model_Addressbook::model()->findByPk($params['addressbook_id']);
		
		if(!$addressbook)
			throw new GO_Base_Exception_NotFound();
		
		$addressbook->truncate();
		
		$response['success']=true;
		
		return $response;
	}
	
	
	protected function actionCheck($params){
		$model = GO::getModel($this->model)->findByPk($params["id"]);
		$model->checkDatabase();
		
		$stmt = $model->contacts;
		
		foreach($stmt as $contact)
			$contact->checkDatabase();
		
		$stmt = $model->companies;
		
		foreach($stmt as $company)
			$contact->checkDatabase();
		
		echo "Done\n";
	}
	
	/**
	 * Imports VCF file.
	 * Example command line call: /path/to/groupoffice/groupoffice addressbook/addressbook/importVcf --file=filename.txt --addressbook_id=1
	 * @param Array $params Parameters. MUST contain string $params['file'].
	 */
//	protected function actionImportVcf($params){
//		$file = new GO_Base_Fs_File($params['file']);
//		$file->convertToUtf8();
//
//		$data = $file->getContents();
//		
//		$contact = new GO_Addressbook_Model_Contact();
//		$vcard = GO_Base_VObject_Reader::read($data);
//		
//		GO_Base_VObject_Reader::convertVCard21ToVCard30($vcard);
//		
//		
//		
//		if(!empty($params["addressbook_id"]))
//			throw new Exception("Param addressbook_id may not be empty");
//		//$params['addressbook_id'] = !empty($params['a']) ? $params['a'] : 1;
//		
//		if (is_array($vcard)) {
//			foreach ($vcard as $item) {
//				$contact->importVObject(
//					$item,
//					array(
//						'addressbook_id' => $params['addressbook_id']
//					)
//				);
//			}
//		} else {
//			$contact->importVObject(
//				$vcard,
//				array(
//					'addressbook_id' => $params['addressbook_id']
//				)
//			);
//		}
//		return array('success'=>true);
//	}
//	
	
	
	public function actionRemoveDuplicates($params){
		
		GO::setMaxExecutionTime(300);
		GO::setMemoryLimit(1024);
		
		$this->render('externalHeader');
		
		$addressbook = GO_Addressbook_Model_Addressbook::model()->findByPk($params['addressbook_id']);
		
		if(!$addressbook)
			throw new GO_Base_Exception_NotFound();
		
		GO_Base_Fs_File::setAllowDeletes(false);
		//VERY IMPORTANT:
		GO_Files_Model_Folder::$deleteInDatabaseOnly=true;
		
		
		GO::session()->closeWriting(); //close writing otherwise concurrent requests are blocked.
		
		$checkModels = array(
				"GO_Addressbook_Model_Contact"=>array('first_name', 'middle_name', 'last_name', 'company_id', 'email','addressbook_id'),
			);		
		
		foreach($checkModels as $modelName=>$checkFields){
			
			if(empty($params['model']) || $modelName==$params['model']){

				echo '<h1>'.GO::t('removeDuplicates').'</h1>';

				$checkFieldsStr = 't.'.implode(', t.',$checkFields);
				$findParams = GO_Base_Db_FindParams::newInstance()
								->ignoreAcl()
								->select('t.id, count(*) AS n, '.$checkFieldsStr)
								->group($checkFields)
								->having('n>1');
				
				$findParams->getCriteria()->addCondition('addressbook_id', $addressbook->id);

				$stmt1 = GO::getModel($modelName)->find($findParams);

				echo '<table border="1">';
				echo '<tr><td>ID</th><th>'.implode('</th><th>',$checkFields).'</th></tr>';

				$count = 0;

				while($dupModel = $stmt1->fetch()){
					
					$select = 't.id';
					
					if(GO::getModel($modelName)->hasFiles()){
						$select .= ', t.files_folder_id';
					}

					$findParams = GO_Base_Db_FindParams::newInstance()
								->ignoreAcl()
								->select($select.', '.$checkFieldsStr)
								->order('id','ASC');
					
					$findParams->getCriteria()->addCondition('addressbook_id', $addressbook->id);

					foreach($checkFields as $field){
						$findParams->getCriteria()->addCondition($field, $dupModel->getAttribute($field));
					}							

					$stmt = GO::getModel($modelName)->find($findParams);

					$first = true;

					while($model = $stmt->fetch()){
						echo '<tr><td>';
						if(!$first)
							echo '<span style="color:red">';
						echo $model->id;
						if(!$first)
							echo '</span>';
						echo '</th>';				

						foreach($checkFields as $field)
						{
							echo '<td>'.$model->getAttribute($field,'html').'</td>';
						}

						echo '</tr>';

						if(!$first){							
							if(!empty($params['delete'])){

								if($model->hasLinks() && $model->countLinks()){
									echo '<tr><td colspan="99">'.GO::t('skippedDeleteHasLinks').'</td></tr>';
								}elseif(($filesFolder = $model->getFilesFolder(false)) && ($filesFolder->hasFileChildren() || $filesFolder->hasFolderChildren())){
									echo '<tr><td colspan="99">'.GO::t('skippedDeleteHasFiles').'</td></tr>';
								}else{									
									$model->delete();
								}
							}

							$count++;
						}

						$first=false;
					}
				}	
					

				echo '</table>';

				echo '<p>'.sprintf(GO::t('foundDuplicates'),$count).'</p>';
				echo '<br /><br /><a href="'.GO::url('addressbook/addressbook/removeDuplicates', array('delete'=>true, 'addressbook_id'=>$addressbook->id)).'">'.GO::t('clickToDeleteDuplicates').'</a>';
				
			}
		}
		
		$this->render('externalFooter');
		
		
	}
}
