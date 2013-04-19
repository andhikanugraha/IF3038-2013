<?php

	include_once "User.php";
	include_once "Category.php";
	include_once "Task.php";
	include_once "Tag.php";
	include_once "Comment.php";
	include_once "Attachment.php";
	include_once "Connection.php";
	
	/**
	 * This class is to be extended by models.
	 *
	 * The followings are the available columns in table 'user':
	 * @property string $class_name
	 * @property array $models
	 * @property array $data
	 */
	abstract class SimpleRecord
	{
		private static $class_name;
		private static $models = array();
		public $data = array();
		
		public static function tableName() {}
		
		public function __construct()
		{
		}
		
		public static function model($className)
		{
			self::$class_name = $className;
			if (!array_key_exists($className, self::$models))
			{
				$models[$className] = new $className();
			}
			return $models[$className];
		}
		
		public function __get($property) 
		{
			if (array_key_exists($property, $this->data))
			{
				return $this->data[$property];
			}
			else
			{
				return null;
			}
		}

		public function __set($property, $value) 
		{
			$this->data[$property] = $value;
			return $this;
		}
		
		public function find($query="", $selection = array())
		{
			$ret = new self::$class_name();

			// DBConnection::openDBconnection();
			
			if ($query != "")
				$query = " WHERE ".$query;
			
			$select = "";
			if ($selection)
			{
				foreach($selection as $tempselect)
				{
					$select .= $tempselect.", ";
				}
				$select = substr($select, 0, -2);
			}
			else
			{
				$select = "*";
			}
			$result = DBConnection::DBquery("SELECT ".$select." FROM ".$this->tableName().$query." LIMIT 1");

			$count = $result->num_rows;
			
			if ($count > 0)
			{
				$fields = $result->fetch_fields();

				$i = 0;
				$row = $result->fetch_object();

				foreach ($fields as $val) 
				{
					$name = $val->name;
					$ret->$name = $row->$name;
				}
				$result->close();
			}
			
			// DBConnection::closeDBconnection();
						
			return $ret;
		}
		
		public function findAll($query="", $selection = array())
		{
			$ret = array();
		
			// DBConnection::openDBconnection();
			
			if ($query != "")
				$query = " WHERE ".$query;
				
			$select = "";
			if ($selection)
			{
				foreach($selection as $tempselect)
				{
					$select .= $tempselect.", ";
				}
				$select = substr($select, 0, -2);
			}
			else
			{
				$select = "*";
			}
			
			$result = DBConnection::DBquery("SELECT ".$select." FROM ".$this->tableName().$query);
			$count = $result->num_rows;
			
			if ($count > 0)
			{
				$fields = $result->fetch_fields();

				$i = 0;
				while ($row = $result->fetch_object())
				{
					$ret[$i] = new self::$class_name();
					foreach ($fields as $val) 
					{
						$name = $val->name;
						$ret[$i]->$name = $row->$name;
					}
					$i++;
				}
				
				$result->close();
			}			
			
			// DBConnection::closeDBconnection();
			
			return $ret;
		}

		public function findAllLimit($query = "", $selection = array(), $limitStart = 0, $limitEnd = 100) {
			return $this->findAll($query . " LIMIT " . intval($limitStart) . "," . intval($limitEnd));
		}
		
		public function delete($query)
		{
			if ($query != "")
				$query = " WHERE ".$query;
				
			$result = DBConnection::DBquery("DELETE FROM ".$this->tableName().$query);
						
			return DBConnection::affectedRows();
		}
	}

?>
