<?php
	
	class DBConnection
	{
		private static $host = "localhost";
		private static $username = "progin";
		private static $password = "progin";
		private static $dbname = "progin_439_13510007";
		// private static $username = "root";
		// private static $password = "";
		// private static $dbname = "kmb";
		private static $link;
		
		public static function openDBconnection() 
		{
			if (mysqli_ping(self::$link))
				return;

            // Create connection
            self::$link = new mysqli(self::$host, self::$username, self::$password, self::$dbname);
            // Check connection
            if (mysqli_connect_errno()) 
            {
                echo 'Failed to connect to MySQL:' . mysqli_connect_error();
                unset(self::$link);
            }
        }
        
		public static function DBquery($query)
		{
			self::openDBconnection();

			if (mysqli_ping(self::$link))
			{
				return self::$link->query($query);
			}
			else
			{
				return "Fail";
			}
		}

		public static function insertID() 
		{
			if (mysqli_ping(self::$link))
				return self::$link->insert_id;
		}
		
		public static function affectedRows() 
		{
			if (mysqli_ping(self::$link))
				return self::$link->affected_rows;
		}

        public static function closeDBconnection()
		{
			if (mysqli_ping(self::$link)) 
			{
				self::$link->close();
			}
			else 
			{
				printf ("Error: %s\n", mysqli_error($link));
			}
        } 
	}
?>
