<?php
	include_once "SimpleRecord.php";
	
	/**
	 * This is the model class for table "tag".
	 *
	 * The followings are the available columns in table 'tag':
	 * @property integer $id_tag
	 * @property string $tag_name
	 */
	class Tag extends SimpleRecord
	{		
		/**
		 * Returns the static model of the specified SimpleRecord class.
		 * @param string $className active record class name.
		 * @return Tag the static model class
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
			return "tag";
		}
		
		/**
		 * Check the validity of the record
		 * @return array of errors
		 */
		public function checkValidity()
		{
			$error = array();
			return $error;
		}
		
		/**
		 * Save the current model, insert if new model, update if exists
		 * @return boolean whether record is saved or not
		 */
		public function save()
		{
			// check same tag name
			if ($this->id_tag==null)
			{
				// new tag
				$temp = Tag::model()->find("tag_name=`".$this->tag_name."`");
				if ($temp->data)
				{
					// tag_name already used
					$this->id_tag = $temp->id_tag;
					return false;
				}
				else 
				{
					$result = DBConnection::DBquery("INSERT INTO `".self::tableName()."` (`tag_name`) VALUES ('".$this->tag_name."')");
					$this->id_tag = DBConnection::insertID();
					return $result;
				}
			}
			else
			{
				// existing tag
			}
		}
	}
?>
