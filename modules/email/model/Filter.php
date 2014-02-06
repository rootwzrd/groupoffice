<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: GO_Email_Model_LinkedEmail.php 7607 2011-09-01 15:38:01Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

/**
 * The GO_Email_Model_Filter model
 * 
 * @property string $name
 * @property string $email
 * @property boolean $default
 * @property string $signature
 * @property int $account_id
 * @property int $id
 * @property string $field
 * @property string $keyword
 * @property string $folder
 * @property int $priority
 * @property boolean $mark_as_read
 */
class GO_Email_Model_Filter extends GO_Base_Db_ActiveRecord {

	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Email_Model_Filter
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}


	/**
	 * Returns the table name
	 */
	public function tableName() {
		return 'em_filters';
	}

	/**
	 * Here you can define the relations of this model with other models.
	 * See the parent class for a more detailed description of the relations.
	 */
	public function relations() {
		return array(
				'account' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Email_Model_Account', 'field'=>'account_id')
		);
	}
}