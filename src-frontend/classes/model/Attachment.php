<?php
	include_once "SimpleRecord.php";
	
	/**
	 * This is the model class for table "task_attachment".
	 *
	 * The followings are the available columns in table 'task_attachment':
	 * @property integer $id_tag
	 * @property string $tag_name
	 */
	class Attachment extends SimpleRecord
	{		
		/**
		 * Returns the static model of the specified SimpleRecord class.
		 * @param string $className active record class name.
		 * @return Attachment the static model class
		 */
		public static function model($className=__CLASS__)
		{
			return parent::model($className);
		}
	
		/**
		 * @return string the associated database table name
		 */
		public static function tableName()
		{
			return "task_attachment";
		}
		
		/**
		 * Check the validity of the record
		 * @return array of errors
		 */
		public function checkValidity()
		{
			$error = array();
			// check jpeg dll
			if (!preg_match("/^.{5,}$/", $this->data['username']))
			{
				$error["username"] = "Username harus minimal 5 karakter.";
			}
			return $error;
		}
		
		/**
		 * Save the current model, insert if new model, update if exists
		 * @return boolean whether record is saved or not
		 */
		public function save()
		{
			if (Attachment::model()->find("id_task='".$this->id_task."' AND attachment='".$this->attachment."'")->data)
			{
				return false;
			}
			else
			{
				$result = DBConnection::DBquery("INSERT INTO ".self::tableName().
						" (id_task,attachment) VALUES ('".$this->id_task."','".$this->attachment."')");
			}
			return $result;
		}
	}
?>
