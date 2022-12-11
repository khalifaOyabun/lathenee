<?php

class DBcopy
{
    var $pdo;
    var $error_message;
    
    var $mysql_username;
    var $mysql_password;
    
    
    function pdo_connection ($sql_conn_host, $sql_conn_username, $sql_conn_password)
    {
        try {
            $this->mysql_username = $sql_conn_username;
            $this->mysql_password = $sql_conn_password;
            $this->mysql_hostname = $sql_conn_host;
            
            $this->pdo = new PDO("mysql:host=$sql_conn_host;", $sql_conn_username, $sql_conn_password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            $this->error_message .= "DB error: " . $e->getMessage();
        }
    }
    
    function create_from_prototype($sql_original_db, $sql_copy_db)
    {
        try {
            # Check if database exists
            $chdb = $this->pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$sql_copy_db'");
            if ((bool) $chdb->fetchColumn()) {
                # Database already exists
                $this->error_message .= "Database $sql_copy_db already exists";
                
                return false;
            } else {
                $this->pdo->exec("CREATE DATABASE `$sql_copy_db`;
                        GRANT ALL ON `$sql_copy_db`.* TO '{$this->mysql_username}'@'localhost';
                        FLUSH PRIVILEGES;");
            }
        } catch(PDOException $e) {
            $this->error_message .= "DB error: " . $e->getMessage();
            
            return false;
        }
        
        # Generate a tmp file and get it's path
        $sql_filename = stream_get_meta_data(tmpfile())['uri'];
        # Extract database to an sql dump file
        $cmd1 = "mysqldump --user={$this->mysql_username} --password={$this->mysql_password} --host={$this->mysql_hostname} {$sql_original_db} > {$sql_filename}";
        exec($cmd1);
        # Import database dump to correct db
        $cmd2 = "mysql -u {$this->mysql_username} -p{$this->mysql_password} {$sql_copy_db} < {$sql_filename}";
        exec($cmd2);
        
        return true;
    }
}




$sql_conn_username = 'stathis';
$sql_conn_password = 's1234';
$sql_conn_host = 'localhost';

# Required: the prototype database to copy!
$sql_prototype_db = 'dental';  # I use one I had locally for an old project
$sql_copy_db = 'db_test2';



$testObj = new DBcopy;
$testObj->pdo_connection($sql_conn_host, $sql_conn_username, $sql_conn_password);
$testObj->create_from_prototype($sql_prototype_db, $sql_copy_db);

echo $testObj->error_message;

