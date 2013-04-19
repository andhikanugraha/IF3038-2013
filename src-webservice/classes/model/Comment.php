<?php
	include_once "SimpleRecord.php";
	
	/**
	 * This is the model class for table "comment".
	 *
	 * The followings are the available columns in table 'comment':
	 * @property integer $id_komentar
	 * @property string $timestamp
	 * @property string $komentar
	 * @property string $id_user
	 * @property string $id_task
	 */
	class Comment extends SimpleRecord
	{		
		/**
		 * Returns the static model of the specified SimpleRecord class.
		 * @param string $className active record class name.
		 * @return Comment the static model class
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
			return "comment";
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
			// check same comment name
			if ($this->id==null)
			{
				$result = DBConnection::DBquery("INSERT into ".$this->tableName()." (id_komentar, komentar, id_user, id_task) ".
											"VALUES (0, '".addslashes($this->komentar)."', '".addslashes($this->id_user)."', '".addslashes($this->id_task)."')");
				
				return $result;
			}
			else
			{
				// existing comment
			}
		}
		
		/**
		 * Get the user who comment
		 * @return User who comment
		 */
		public function getUser()
		{
			return User::model()->find("id_user = ".$this->id_user, array("id_user", "username", "fullname", "avatar"));
		}
		
		/**
		 * Get the task where the comment exist
		 * @return Task where the comment exist
		 */
		public function getTask()
		{
			return Task::model()->find("id_task = ".$this->id_task);
		}
		
		/**
		 * Get latest comment with users
		 * @return array of comment and user
		 */
		public static function getLatest($id_task, $timestamp)
		{
			$id_task = addslashes($id_task);
			$timestamp = addslashes($timestamp);
			$result = DBConnection::DBquery("SELECT id_komentar, timestamp, komentar, c.id_user, username, fullname, avatar".
											" FROM ".Comment::tableName()." as c INNER JOIN ".User::tableName()." as u ".
											" ON c.id_user=u.id_user WHERE id_task = '".$id_task."' AND timestamp > '" . $timestamp . "' ORDER BY timestamp");

			$ret = array();
			$count = $result->num_rows;
			
			if ($count > 0)
			{
				$fields = $result->fetch_fields();

				$i = 0;
				$ret[] = array();
				while ($row = $result->fetch_object())
				{
					$ret[$i] = array();
					foreach ($fields as $val) 
					{
						$name = $val->name;
						$ret[$i][$name] = $row->$name;
					}
					$i++;
				}				
				
				$result->close();
			}
			return $ret;
		}
		
		/**
		 * Get older comment with users
		 * @return array of comment and user
		 */
		public static function getOlder($id_task, $timestamp)
		{
			$id_task = addslashes($id_task);
			$timestamp = addslashes($timestamp);
			$result = DBConnection::DBquery("SELECT id_komentar, timestamp, komentar, c.id_user, username, fullname, avatar".
											" FROM ".Comment::tableName()." as c INNER JOIN ".User::tableName()." as u ".
											" ON c.id_user=u.id_user WHERE id_task = '".$id_task."' AND timestamp < '" . $timestamp . "' ORDER BY timestamp DESC LIMIT 10");

			$ret = array();
			$count = $result->num_rows;
			
			if ($count > 0)
			{
				$fields = $result->fetch_fields();

				$i = 0;
				$ret[] = array();
				while ($row = $result->fetch_object())
				{
					$ret[$i] = array();
					foreach ($fields as $val) 
					{
						$name = $val->name;
						$ret[$i][$name] = $row->$name;
					}
					$i++;
				}				
				
				$result->close();
			}
			return $ret;
		}
	}
?>
