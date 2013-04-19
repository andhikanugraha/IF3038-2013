<?php

// REST API Wrapper
// How to use:
// * Every public method of this class will be 

class RestApi 
{
	protected $app;

	public function __construct($app) 
	{
		$this->app = $app;
	}
	
	public function fetch_latest_task($params)
	{
		$ret = array();
		if (isset($params['task_id']))
		{
			if (isset($params['category_id']))
			{
				// TODO check session current user
				// retrieve based on category
				$ret = Task::model()->findAll("task_id > ".$params['task_id']." AND EXISTS (SELECT * FROM ".Categoory::tableName().
					" WHERE category_id = " . $params['category_id']." AND task_id = task.id)");
			}
			else
			{
				// retrieve all
				$ret = Task::model()->findAll("task_id > ".$params['task_id']);
			}
		}
		return $ret;
	}
	
	public function retrieve_tags($params)
	{
		$ret = array();
		if (isset($params['tags']))
		{
			// retrieve based on existing tags
			$condition = "";
			foreach($params['tags'] as $tag)
			{
				$condition .= "tag_name != ".$tag." ";
			}
			$ret = Tag::model()->findAll($condition);
		}
		else
		{
			// retrieve all
			$ret = Tag::model()->findAll();
		}
		return $ret;
	}
	
	/**
	 * Get list of users search by username
	 * @return array of users with likability
	 */
	public function get_tag($params) 
	{
		$return = array();
		if ((isset($params['tag'])) && ($this->app->loggedIn)) 
		{	
			$tags = explode(",", $params['tag']);
			$not_query = array();
			for ($i=0;$i<count($tags)-1;++$i)
			{
				$not_query [] = " tag_name <> '".addslashes($tags[$i])."' ";
			}
			$tag = $tags[count($tags)-1];
			$string = ($not_query) ? " AND ".implode("AND",$not_query) : "";
			$return = Tag::model()->findAll(" tag_name LIKE '".addslashes($tag)."%' ".$string." LIMIT 10");
		}

		return $return;
	}
	
	public function retrieve_users($params)
	{
		$ret = array();
		if (isset($params['users']))
		{
			// retrieve based on existing users
			$condition = "";
			foreach($params['users'] as $user)
			{
				$condition .= "username != ".$user." ";
			}
			$ret = User::model()->findAll($condition, array("username"));
		}
		else
		{
			// retrieve all
			$ret = User::model()->findAll("", array("username"));
		}
		return $ret;
	}
	
	/*** ----- START OF TASK MODULE -----***/
	
	/**
	 * Delete a task
	 * @return string contains whether success or fail
	 */
	public function delete_task() 
	{
		$id_task = addslashes($_POST['task_id']);
		$success = false;

		if ((Task::model()->find("id_task=".$id_task)->getDeletable($this->app->currentUserId))&& ($this->app->loggedIn))
		{
			if (Task::model()->delete("id_task=".$id_task)==1)
			{
				// delete was success
				$success = true;
			}
			else {
				$success = false;
			}
		}

		return array(
			'success' => $success,
			'taskID' => $id_task
		);
	}
	
	/**
	 * Retrieve list of tasks for dashboard
	 * @return array of tasks
	 */
	public function retrieve_tasks($params)
	{
		$ret = array();
		if (isset($params['category_id']))
		{
			// TODO check if user session is in some category
			// retrieve based on category
			$cat = Category::model()->find("id_kategori = '" . addslashes($params['category_id']) . "'");

			if ($cat) 
			{
				// found
				$success = true;

				$categoryID = $cat->id_kategori;
				$categoryName = $cat->nama_kategori;
				$canDeleteCategory = $cat->id_user == $this->app->currentUserId;
				$canEditCategory = $cat->getEditable($this->app->currentUserId);

				$ret = Task::model()->findAll("id_kategori = '" . (int) $params['category_id'] . "'");
				//$canAddTask = Category::model()->find("id_kategori = '". $cat->id_kategori."' AND "$this->app->currentUserId;
			}
			else {
				// not found
				$success = false;
				$ret = array();
			}
		}
		else {
			// retrieve all
			$success = true;
			$id = $this->app->currentUserId;
			$ret = Task::model()->findAll("id_kategori IN ( SELECT id_kategori FROM ".Category::tableName()." WHERE id_user='$id' ".
											"OR id_kategori IN (SELECT id_kategori FROM edit_kategori WHERE id_user='$id') ".
											"OR id_kategori IN (SELECT id_kategori FROM ". Task::tableName() ." AS t LEFT OUTER JOIN assign AS a ".
											"ON t.id_task=a.id_task WHERE t.id_user = '". $id ."' OR a.id_user = '". $id ."' ))");
		}

		$tasks = array();
		foreach ($ret as $task) 
		{
			$dummy = new StdClass;
			$dummy->name = $task->nama_task;
			$dummy->id = $task->id_task;
			$dummy->done = (bool) $task->status;
			$dl = new DateTime($task->deadline);
			$dummy->deadline = $dl->format('j F Y');

			$dummy->deletable = $task->getDeletable($this->app->currentUserId);

			$tags = $task->getTags();
			$dummy->tags = array();
			foreach ($tags as $tag) {
				$dummy->tags[] = $tag->tag_name;
			}

			$tasks[] = $dummy;
		}

		return compact('success', 'tasks', 'categoryID', 'categoryName', 'canDeleteCategory', 'canEditCategory');
	}

	public function get_task($params)
	{
		$id_task = addslashes($_GET['id_task']);
		$task = array();

		if ($this->app->loggedIn)
		{
			$task = Task::model()->find("id_task=".$_GET['id_task'], array("id_task","nama_task","status","deadline"));

			$users = $task->getAssignee();
			$temp = array();
			$i = 0;
			foreach ($users as $user)
			{
				$temp[]['username'] = $user->username;
				$temp[$i]['id_user'] = $user->id_user;
				$i++;
			}
			$task->asignee = $temp;
			
			$tags = $task->getTags();
			$temp = array();
			foreach ($tags as $tag)
			{
				$temp[] = $tag->tag_name;
			}
			$task->tag = $temp;
		}

		return $task->data;
	}

	public function mark_task($params) 
	{
		$start = microtime();
		$id_task = addslashes($_POST['taskID']);
		$completed = $_POST['completed'] == 'true' ? 1 : 0;

		// TODO permissions

		$update = "UPDATE task SET status=$completed WHERE id_task='$id_task'";

		$q = DBConnection::DBquery($update);
		if (DBConnection::affectedRows()) {
			return array('success' => true, 'taskId' => $id_task, 'done' => $completed);
		}
		else {
			return array('success' => false, $update);
		}
	}
	
	/*** ----- END OF TASK MODULE -----***/
	
	/*** ----- START OF CATEGORY MODULE -----***/

	/**
	 * Retrieve list of category
	 * @return array of categories
	 */
	public function retrieve_categories($params) 
	{
		// TODO use categories by user
		$cats = array();
		if ($this->app->loggedIn)
		{
			$raw = $this->app->currentUser->getCategories();

			foreach ($raw as $cat) {
				$dummy = new StdClass;
				$dummy->name = $cat->nama_kategori;
				$dummy->id = $cat->id_kategori;
				$dummy->canDeleteCategory = $cat->getDeletable($this->app->currentUserId);
				$dummy->canEditCategory = $cat->getEditable($this->app->currentUserId);

				$cats[] = $dummy;
			}
		}

		return $cats;
	}
	
	/**
	 * Add a new category
	 * @return array of new category data
	 */
	public function add_category() 
	{
		if ($this->app->loggedIn)
		{
			if (!$_POST)
				return;

			$nama_kategori = $_POST['nama_kategori'];
			$id_user = $this->app->currentUserId; // the creator of the category

			$category = Category::model();
			$category->nama_kategori = $nama_kategori;
			$category->id_user = $id_user;
			$category->save();

			$usernames = $_POST['usernames']; // an array of usernames, if using facebook-style
			$usernames_list = $_POST['usernames_list'];
			if (!$usernames && $usernames_list) {
				$usernames = explode(';', $usernames_list);
			}
			foreach ($usernames as $k => $v) {
				$usernames[$k] = trim($v);
			}
			if ($usernames) {
				// Find the IDs of the users
				$escapedUsernames = array();
				foreach ($usernames as $k => $v) {
					$escapedUsernames[] = "'" . addslashes($v) . "'";
				}
				$escapedUsernames = implode(',', $escapedUsernames);
				$escapedUsernames = '(' . $escapedUsernames . ')';

				$q = "username IN $escapedUsernames";
				$users = User::model()->findAll($q);

				// Insert into relationship table
				foreach ($users as $user) {
					$insert = "INSERT INTO edit_kategori (id_user, id_katego) VALUES ({$user->id_user}, {$category->id_kategori})";
					DBConnection::DBQuery($insert);
				}
			}

			return array('categoryID' => $category->id_kategori, 'categoryName' => $category->nama_kategori, 'categories' => $this->retrieve_categories());
		}
		return array();
	}

	/**
	 * Delete a category
	 * @return string contains whether success or fail
	 */
	public function delete_category() 
	{
		$id_kategori = addslashes($_POST['category_id']);
		$success = false;

		if ((Category::model()->find("id_kategori=".$id_kategori)->getDeletable($this->app->currentUserId))&& ($this->app->loggedIn))
		{
			if (Category::model()->delete("id_kategori=".$id_kategori)==1)
			{
				// delete was success
				$success = true;
			}
			else {
				$success = false;
			}
		}

		return array(
			'success' => $success,
			'categoryID' => $id_kategori
		);
	}

	/*** ----- END OF CATEGORY MODULE -----***/
	
	/*** ----- START OF COMMENT MODULE -----***/
	
	/**
	 * Get the previous comment from before timestamp
	 * @return array of comments before timestamp
	 */
	public function get_previous_comments($params)
	{
		$return = array();
		if ((isset($params['id_task'])) && (isset($params['timestamp'])) && ($this->app->loggedIn))
		{
			$return = Comment::getOlder($params['id_task'], $params['timestamp']);
		}
		return $return;
	}
	
	/**
	 * Retrieve comment after timestamp
	 * @return array of comments after timestamp
	 */
	public function retrieve_comments($params)
	{
		$return = array();
		if ((isset($params['id_task'])) && (isset($params['timestamp'])) && ($this->app->loggedIn))
		{
			$return = Comment::getLatest($params['id_task'], $params['timestamp']);
		}
		return $return;
	}
	
	/**
	 * Post a new comment
	 * @return string contains whether success or fail
	 */
	public function comment($params)
	{
		$return = "fail";
		if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($params['komentar'])) && ($this->app->loggedIn))
		{
			$comment = new Comment();
			$comment->data = $params;
			$comment->id_user = $this->app->currentUserId;
			if ($comment->save())
			{
				$return = "success";
			}
		}
		return $return;
	}
	
	/**
	 * Remove a comment
	 * @return string contains whether success or fail
	 */
	public function remove_comment ($params)
	{
		$return = "fail";
		if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($params['id'])) && ($this->app->loggedIn))
		{
			if (Comment::model()->delete("id_komentar = ".addslashes($params['id'])." AND id_user = ".addslashes($this->app->currentUserId))==1)
			{
				$return = "success";
			}
		}
		return $return;
	}
	
	/*** ----- END OF COMMENT MODULE -----***/
	
	/*** ----- START OF USER MODULE -----***/
	
	/**
	 * Get list of users search by username
	 * @return array of users with likability
	 */
	public function get_username($params) 
	{
		$return = array();
		if ((isset($params['username'])) && ($this->app->loggedIn)) 
		{	
			$users = explode(",", $params['username']);
			$not_query = array();
			for ($i=0;$i<count($users)-1;++$i)
			{
				$not_query [] = " username <> '".addslashes($users[$i])."' ";
			}
			$user = $users[count($users)-1];
			$string = ($not_query) ? " AND ".implode("AND",$not_query) : "";
			$return = User::model()->findAll(" username LIKE '".addslashes($user)."%' ".$string." LIMIT 10", array("id_user", "username"));
		}

		return $return;
	}
	
	/**
	 * Login using username and password through Rest API
	 * @return string contains whether success or fail
	 */
	public function login($params) 
	{
		$return = array();
		if (($_SERVER['REQUEST_METHOD'] === 'POST') &&
			(isset($params['username'])) && (isset($params['password']))) 
		{	
			$user = User::model()->find("username='".addslashes($params['username'])."' AND password='".md5($params['password'])."'");
			if ($user->data)
			{
				$_SESSION['user_id'] = $user->id_user;
				$u = new User;
				$u->id_user = $user->id_user;
				$u->fullname = $user->fullname;
				$u->username = $user->username;
				$u->email = $user->email;

				$_SESSION['current_user'] = $u;

				$return["status"] = "success";
			}
			else {
				$return["status"] = "fail";
			}
			
		}
		else {
			$return["status"] = "fail";
		}

		return $return;
	}

	/**
	 * Login using username and password through Rest API
	 * @return string contains whether success or fail
	 * @deprecated
	 */
	public function logout($params) 
	{
		$full_url = $_SESSION['full_url'];
		$this->app->destroySession();
		header("Location:".$full_url."index.php");
	}

	/**
	 * Check register parameter through Rest API
	 * @return array of status and possible errors
	 */
	public function register_check($params) 
	{
		$return = array();
		if (($_SERVER['REQUEST_METHOD'] === 'POST') && (ISSET($params['username'])) && (ISSET($params['email']))
			&& (ISSET($params['password'])) && (ISSET($params['confirm_password'])) 
			&& (ISSET($params['fullname'])) && (ISSET($params['birthdate']))
			&& (ISSET($params['avatar']))
			)
		{
			$return["status"] = "success";
			$return["error"] = array();
			
			$user = new User();
			$user->data = $params;
			$temperror = $user->checkValidity();
			
			if ($temperror)
			{
				$return["status"] = "fail";
				$return["error"] = array_merge($return["error"], $temperror);
			}
			
			if (User::model()->find("username='".addslashes($params['username'])."' OR email='".addslashes($params['email'])."'")->data)
			{
				$return["status"] = "fail";
				$return["error"]["duplicate"] .= "Username/email sudah digunakan.";
			}
		}
		else
		{
			$return["status"] = "fail";
		}

		return $return;
	}

	/*** ----- END OF USER MODULE -----***/	

	/*** ----- START OF SEARCH MODULE -----***/
	public function search_suggestions($params = array()) {
		$type = $params['type'];
		$q = $params['q'];

		if (!$q)
			return array();

		$all = $type == 'all';

		$suggestions = array();

		if ($type == 'task' || $all) {
			$tasks = $this->app->currentUser->getTasksLike($q);
			foreach ($tasks as $task) {
				if (!in_array($task->nama_task, $suggestions)) {
					$suggestions[] = $task->nama_task;
				}
			}
		}
		if ($type == 'category' || $all) {
			$cats = $this->app->currentUser->getCategoriesLike($q);
			foreach ($cats as $cat) {
				if (!in_array($cat->nama_kategori, $suggestions)) {
					$suggestions[] = $cat->nama_kategori;
				}
			}
		}
		if ($type == 'user' || $all) {
			$users = User::model()->findAllLike($q);
			foreach ($users as $u) {
				if (!in_array($u->username, $suggestions)) {
					$suggestions[] = $u->username;
				}
			}
		}

		return $suggestions;
	}
}

?>
