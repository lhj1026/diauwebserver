<?php
/**
 * MySQL示例，通过该示例可熟悉BAE平台MySQL的使用（CRUD）
 */
    require_once("../configure.php");
    $dbname = MYSQLNAME;
    $host = HTTP_BAE_ENV_ADDR_SQL_IP;
    $user = HTTP_BAE_ENV_AK;
    $pwd =  HTTP_BAE_ENV_SK;
   
    //creating a new connection object using mysqli
    $conn = new mysqli($host, $user, $pwd, $dbname);

    //if there is some error connecting to the database
    //with die we will stop the further execution by displaying a message causing the error
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    //创建表
    $create_sql = "CREATE TABLE IF NOT EXISTS userMsg(
         		id int(11) NOT NULL AUTO_INCREMENT,
			from_user varchar(40) DEFAULT NULL,
			account varchar(40) DEFAULT NULL,
			password varchar(40) DEFAULT NULL,
			update_time datetime DEFAULT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY from_user (from_user)
		   )";

    mysqli_query($conn,$create_sql) or die (mysqli_error($conn));
    mysqli_close($connect); 
?>
