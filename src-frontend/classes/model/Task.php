<?php
	include_once "SimpleRecord.php";
	
	/**
	 * This is the model class for table "task".
	 *
	 * The followings are the available columns in table 'task':
	 * @property integer $id_task
	 * @property string $nama_task
	 * @property string $status
	 * @property string $deadline
	 * @property integer $id_kategori
	 * @property integer $id_user
	 */
	class Task extends SimpleRecord
	{		
		/**
		 * Returns the static model of the specified SimpleRecord class.
		 * @param string $className active record class name.
		 * @return Task the static model class
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
			return "task";
		}
		
		/**
		 * Check the validity of the record
		 * @return array of errors
		 */
		public function checkValidity()
		{
			$error = array();
			
			if (!preg_match("/^[a-zA-Z0-9 ]{1,25}$/", $this->nama_task))
			{
				$error["nama_task"] = "Nama task maksimal 25 karakter alfanumerik.";
			}
			
			$i = 0;
			$assignees = array();
			$tempuser = explode (",", $this->assignee);
			foreach ($tempuser as $user)
			{
				$tempouser = User::model()->find("username='".$user."'", array("id_user"));
				if ($tempouser->data)
				{
					$assignees[] = new User();
					$assignees[$i]->id_user = $tempouser->id_user;
					$assignees[$i]->username = $user;
					$i++;
				}
				else 
				{
					$error['assignee'][] = "Assignee ".$user." is not valid";
				}
			}
			$this->assignee = $assignees;
			
			$this->tag = explode(",", $this->tag);
		}
		
		/**
		 * Save the current model, insert if new model, update if exists
		 * @return boolean whether record is saved or not
		 */
		public function save()
		{
			// check same task name
			if ($this->id_task==null)
			{
				$user_id = addslashes($_SESSION['user_id']);
				$result = DBConnection::DBquery("INSERT INTO `".self::tableName()."` (`nama_task`, `deadline`, `id_kategori`, `id_user`)".
						" VALUES ('".addslashes($this->nama_task)."', '".addslashes($this->deadline)."', '".addslashes($this->id_kategori)."', '".
						$user_id."')");
				
				$this->id_task = DBConnection::insertID();
				
				if ($result)
				{
					foreach ($this->assignee as $assignee)
					{
						DBConnection::DBquery("INSERT INTO `assign` (`id_user`, `id_task`)".
							" VALUES ('".$assignee->id_user."', '".$this->id_task."')");
					}
					foreach ($this->tag as $tag)
					{
						$temptag = Tag::model()->find("tag_name = ".$tag);
						if (!$temptag)
						{
							$temptag->tag_name = $tag;
							$temptag->save();
						}
						
						DBConnection::DBquery("INSERT INTO `have_tags` (`id_task`, `id_tag`)".
							" VALUES ('".$this->id_task."', '".$temptag->id_tag."')");
					}
					
					foreach ($this->attachments as $attachment)
					{
						$attachment->id_task = $this->id_task;
						print_r($attachment);
						echo "<br>";
						if ($attachment->save())
						{
							move_uploaded_file($attachment->temp_name, $_SESSION['full_path']."upload/attachments/" . $attachment->attachment);
						}
					}
				}
				
				return $result;
			}
			else
			{
				// existing task
				$user_id = addslashes($_SESSION['user_id']);
				$result = DBConnection::DBquery("UPDATE `".self::tableName()."` SET `nama_task`='".addslashes($this->nama_task).
						"', `deadline`='".addslashes($this->deadline)."' , `id_kategori`='".addslashes($this->id_kategori)."', `id_user`='".
						$user_id."' WHERE id_task = '".$this->id_task."'");
				
				if ($result)
				{
					DBConnection::DBquery("DELETE FROM `assign` WHERE `id_task`='".$this->id_task."'");
					foreach ($this->assignee as $assignee)
					{
						DBConnection::DBquery("INSERT INTO `assign` (`id_user`, `id_task`)".
							" VALUES ('".$assignee->id_user."', '".$this->id_task."')");
					}
					
					DBConnection::DBquery("DELETE FROM `have_tags` WHERE `id_task`='".$this->id_task."'");
					foreach ($this->tag as $tag)
					{
						$temptag = Tag::model()->find("tag_name = '".$tag."'");
						if (!$temptag)
						{
							$temptag->tag_name = $tag;
							$temptag->save();
							echo "A";
						}
						
						DBConnection::DBquery("INSERT INTO `have_tags` (`id_task`, `id_tag`)".
							" VALUES ('".$this->id_task."', '".$temptag->id_tag."')");
					}
					
					foreach ($this->attachments as $attachment)
					{
						$attachment->id_task = $this->id_task;
						print_r($attachment);
						echo "<br>";
						if ($attachment->save())
						{
							move_uploaded_file($attachment->temp_name, $_SESSION['full_path']."upload/attachments/" . $attachment->attachment);
						}
					}
				}
				
				return $result;
			}
		}

		/**
		 * Get the tags associated with the task
		 * @return array of Tag that is associated
		 */
		public function getTags() 
		{
			return Tag::model()->findAll("id_tag IN (SELECT id_tag FROM have_tags WHERE id_task='" . $this->id_task . "')");
		}
		
		/**
		 * Get the kategori where the task belongs
		 * @return Category where the task belongs
		 */
		public function getCategory()
		{
			return Category::model()->find("id_kategori = ".$this->id_kategori);
		}
		
		/**
		 * Get the attachments in the task
		 * @return array \of Attachment in the task
		 */
		public function getAttachment()
		{
			//TODO implement Attachment model
			return Attachment::model()->findAll("id_task = ".$this->id_task);
		}
		
		/**
		 * Get the assignee in the task
		 * @return array of User that is asignee of task
		 */
		public function getAssignee()
		{
			return User::model()->findAll("id_user IN (SELECT id_user FROM assign WHERE id_task = '" . $this->id_task . "')", array("id_user", "username"));
		}
		
		/**
		 * Get the comment in the task
		 * @return array of Comment of the task
		 */
		public function getComment()
		{
			return array_reverse(Comment::model()->findAll("id_task = '".$this->id_task."' ORDER BY timestamp DESC LIMIT 10"));
		}
		
		/**
		 * Get total comment in the task
		 * @return int Total Comment in the task
		 */
		public function getTotalComment()
		{
			// TODO Optimized
			$comments = Comment::model()->findAll("id_task = '".$this->id_task."'");
			return count($comments);
		}
		
		
		/**
		 * Check if task editable or not
		 * @return boolean
		 */
		public function getEditable($id_user)
		{
			$id_user = addslashes($id_user);
			return (User::model()->find("id_user IN (SELECT id_user FROM assign WHERE id_task='" . $this->id_task . "' AND id_user ='"+id_user+"')")) ? true : false ;
		}
		
		/**
		 * Check if task deletable or not
		 * @return boolean
		 */
		public function getDeletable($id_user)
		{
			$id_user = addslashes($id_user);
			return ($this->id_user == $id_user) ? true : false ;
		}
	}
?>
