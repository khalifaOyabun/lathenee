<?php

class AutoWebsite
{
    # Database Setting
    var $mysql_username = 'root';
    var $mysql_password = 'NLvrTvyfb6dpjaQx';
    var $mysql_hostname = 'localhost';
    var $mysql_main_database = 'autoinstaller';

    # Apache configuration settings
    #var $template_folder = '/var/www/netprofile2020/functions/apache_templates/';
    #var $backup_folder = '/var/www/netprofile2020/functions/apache_conf_backup/';
    var $template_folder = '/home/var/www/html/com/net-profil/subdomains/autoinstaller/apache_templates/';
    var $backup_folder = '/home/var/www/html/com/net-profil/subdomains/autoinstaller/apache_conf_backup/';
    var $template_file = [];

    # var $apache_conf_dir = '/var/www/netprofile2020/functions/old/';
    var $apache_conf_dir = '/etc/httpd/sites-enabled/auto-sites/';

    # var $apache_log_dir = '/var/log/apache2';
    var $apache_log_dir = '/etc/httpd/logs/';

    # var $system_type = 'debian';  # Or Centos
    var $system_type = 'centos';  # Or Centos

    # Class variables
    var $generated_conf_file = '';
    var $website_domain;
    var $website_domain_alias;
    var $website_directory;
    var $conf_filename;
    var $website_database;

    var $pdo;
    var $error_message;

    function __construct()
    {
        $this->template_file = [
            'conf_file' => $this->template_folder . 'http_conf',
            'conf_alias_file' => $this->template_folder . 'http_alias_conf',
            'conf_ssl_file' => $this->template_folder . 'https_conf1',
            'conf_ssl_alias_file' => $this->template_folder . 'https_conf2',
        ];
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    function pdo_connection ()
    {
        try {

            #$this->mysql_username = $sql_conn_username;
            #$this->mysql_password = $sql_conn_password;
            #$this->mysql_hostname = $sql_conn_host;


            $this->pdo = new PDO("mysql:host={$this->mysql_hostname};dbname={$this->mysql_main_database}",
                $this->mysql_username,
                $this->mysql_password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            $this->error_message .= "DB error: " . $e->getMessage();
        }
    }

    function check_websites ()
    {
        try {
            $stm = $this->pdo->query("SELECT * from website");
            $websites = $stm->fetchAll();
            # print_r($websites);
        } catch(PDOException $e) {
            $this->error_message .= "DB error: " . $e->getMessage();

            exit();
        }

        foreach ($websites as $website) {
            $website['status'] = strtoupper($website['status']);

            # Setup the correct a2 conf file FILENAMES
            if ($this->system_type == 'centos') {
                $website['conf_file'] = $this->apache_conf_dir . $website['domain'];  # conf file in form /etc/httpd/sites-enabled/test-site.com
                if (!empty($website['alias'])) {
                    $website['conf_alias_file'] = $this->apache_conf_dir . $website['domain'] . '-alias';
                }

                if ($website['ssl']) {
                    $website['conf_ssl_file'] = $this->apache_conf_dir . $website['domain'] . '-ssl';
                    if (!empty($website['alias'])) {
                        $website['conf_ssl_alias_file'] = $this->apache_conf_dir . $website['domain'] . '-ssl-alias';
                    }
                }
            } elseif ($this->system_type == 'debian') {
                $website['conf_file'] = $this->apache_conf_dir . $website['domain'] . '.conf';   # Create conf file in /etc/apache2/sites-available/testsite.conf
                if (!empty($website['alias'])) {
                    $website['conf_alias_file'] = $this->apache_conf_dir . $website['domain'] . '-alias.conf';   # Create conf file in /etc/apache2/sites-available/testsite.conf
                }

                if ($website['ssl']) {
                    $website['conf_ssl_file'] = $this->apache_conf_dir . $website['domain'] . '-ssl.conf';
                    if (!empty($website['alias'])) {
                        $website['conf_ssl_alias_file'] = $this->apache_conf_dir . $website['domain'] . '-ssl-alias.conf';
                    }
                }
            }
            # print_R($website);

            if ($website['status'] == 'EDIT') {
                # First delete, then create again!
                $this->delete_site($website);
                echo "Website deleted, now recreate \n";
                $website['status'] = 'CREATE';
            }

            # Check all websites for actions
            if ($website['status'] == 'CREATE') {
                # First install http
                $conf_files_arr = ['conf_file'];
                if ($website['alias']) $conf_files_arr[] = ['conf_alias_file'];

                $this->create_site($conf_files_arr, $website, 0);

                if ($website['ssl']) {
                    # HTTPS:// is enabled
                    $conf_files_arr = ['conf_ssl_file'];

                    if ($website['alias']) $conf_files_arr[] = ['conf_ssl_alias_file'];
                    $this->create_site($conf_files_arr, $website, 1);

                    # If it works uncomment redirects
                    $conf_files_arr = ['conf_file', 'conf_alias_file', 'conf_ssl_file', 'conf_ssl_alias_file'];
                    foreach ($conf_files_arr as $conf_file) {
                        if (!empty($website[$conf_file]) && file_exists($website[$conf_file])) {
                            $confFile = @file_get_contents($website[$conf_file]);
                            $confFile = str_replace("# Redirect permanent", "Redirect permanent", $confFile);

                            @file_put_contents($website[$conf_file], $confFile);
                        }
                    }

                    if ($this->system_type == 'centos') {
                        $command_status = $this->exec_command('service httpd reload');
                    } elseif ($this->system_type == 'debian') {
                        # $this->exec_command('a2ensite configuration file'); # TODO: apache2 in debian also requires a2ensite command.
                        $command_status = $this->exec_command('service apache2 reload');
                    } else {
                        $command_status = false;
                        $this->error_message = "System type is not defined (centos/debian)!";
                    }
                    if (!$command_status) {
                        $this->website_error($website['id']);
                        return false;
                    }
                }
            } elseif ($website['status'] == 'DELETE') {
                # $this->make_backups();
                $this->delete_site($website);
            } else {
                # NOTHING TO DO
            }
        }
    }

    function delete_site ($website)
    {
        # Delete certificates
        # /opt/certbot-auto --noninteractive delete --cert-name demo18.net-profil.com
        $cert_bot_command = "/opt/certbot-auto --noninteractive delete --cert-name {$website['domain']} ";
        $command_status = $this->exec_command($cert_bot_command);
        # Ignore command status here

        # Remove apache conf files
        $conf_files_arr = ['conf_file', 'conf_alias_file', 'conf_ssl_file', 'conf_ssl_alias_file'];
        foreach ($conf_files_arr as $conf_file) {
            if (!empty($website[$conf_file]) && file_exists($website[$conf_file])) {
                echo "DELETING FILE: {$website[$conf_file]} \n";
                unlink($website[$conf_file]);
            }
        }

        # Reload server
        echo "Trying apache reload \n";
        if ($this->system_type == 'centos') {
            $command_status = $this->exec_command('service httpd reload');
        } elseif ($this->system_type == 'debian') {
            # $this->exec_command('a2ensite configuration file'); # TODO: apache2 in debian also requires a2ensite command.
            $command_status = $this->exec_command('service apache2 reload');
        } else {
            $command_status = false;
            $this->error_message = "System type is not defined (centos/debian)!";
        }
        if (!$command_status) {
            $this->website_error($website['id']);
            return false;
        }

        $this->website_deleted($website['id']);
        return false;
    }

    function create_site ($conf_files_arr, $website, $ssl_check=0)
    {
        # $this->make_backups();
        # Check apache2 settings file not exist! In case of create, it should be a new installation
        foreach ($conf_files_arr as $conf_file) {
            if (!empty($website[$conf_file]) && file_exists($website[$conf_file])) {
                $this->error_message .= "Apache2 settings file ({$website[$conf_file]}) already exists!";
                $this->website_error($website['id']);
                return false;
            }
        }

        # Check parent directory
        if (!realpath($website['directory'])) {
            $this->error_message .= "Website Directory ({$website['directory']}) does NOT exist!";
            $this->website_error($website['id']);
            return false;
        }

        if (
            !(
                substr(realpath($website['directory']), 0, 13 ) === "/var/www/html" or
                substr(realpath($website['directory']), 0, 18 ) === "/home/var/www/html"
            )
        ) {
            $real_dir = realpath($website['directory']);
            $this->error_message .= "Website Directory ({$real_dir}) not under /var/www/html/!";
            $this->website_error($website['id']);
            return false;
        }

        $generated_conf_file = [];
        # Generate the conf file contents
        foreach ($conf_files_arr as $conf_file) {
            # Read configuration file
            $confFile = @file_get_contents($this->template_file[$conf_file]);
            if (!$confFile && !empty($website[$conf_file])) {
                $this->error_message .= 'Unable to open apache2 configuration file. ';
            } elseif ($conf_file == 'conf_ssl_alias_file') {
                # In the case of HTTPS serveralias doesn't work. We need a vhost for each alias
                $generated_conf_file[$conf_file] = '';
                foreach (explode(' ', $website['alias']) as $alias) {
                    $templateConf  = $confFile;

                    $templateConf = str_replace("__DOMAIN__", $website['domain'], $templateConf);
                    $templateConf = str_replace("__DOMAIN_ALIAS__", $alias, $templateConf);
                    $templateConf = str_replace("__LOG_DIR__", $this->apache_log_dir, $templateConf);
                    $templateConf = str_replace("__DIRECTORY__", $website['directory'], $templateConf);

                    $generated_conf_file[$conf_file] .= $templateConf;
                }
            } else {
                # Replace file placeholders with correct data
                $confFile = str_replace("__DOMAIN__", $website['domain'], $confFile);
                $confFile = str_replace("__DOMAIN_ALIAS__", $website['alias'], $confFile);
                $confFile = str_replace("__LOG_DIR__", $this->apache_log_dir, $confFile);
                $confFile = str_replace("__DIRECTORY__", $website['directory'], $confFile);

                $generated_conf_file[$conf_file] = $confFile;
            }
        }

        # install http
        if (!$ssl_check) {
            # Install simple http configuratin files
            foreach ($generated_conf_file as $key => $content) {
                $filename = $website[$key];
                # Variables are set, go ahead and create a2 configuration file
                if (!$this->generate_a2conf($website, $filename, $content)) {
                    $this->website_error($website['id']);
                    return false;
                }
            }

            # Graceful apache2 restart / reload
            echo "Trying apache reload \n";
            if ($this->system_type == 'centos') {
                $command_status = $this->exec_command('service httpd reload');
                #$command_status = $this->exec_command('service apache2 reload');
            } elseif ($this->system_type == 'debian') {
                # $this->exec_command('a2ensite configuration file'); # TODO: apache2 in debian also requires a2ensite command.
                $command_status = $this->exec_command('service apache2 reload');
            } else {
                $command_status = false;
                $this->error_message = "System type is not defined (centos/debian)!";
            }
            if (!$command_status) {
                $this->website_error($website['id']);
                return false;
            }


            # simple http webdomains test (no ssl installed yet)
            $ch_domains = array_merge([$website['domain']], explode(' ', $website['alias']));
            # print_R($ch_domains);
            foreach ($ch_domains as $domain) {
                if ($this->is_valid_domain_name($domain) && $domain != "") {
                    echo "Checking domain: http://" . $domain . "\n";
                    if (!$this->curl_get('http://' . $domain)) {
                        # Error while checking domain
                        $this->website_error($website['id']);
                        return false;
                    }
                }
            }
        } else {
            # Try to install https
            # /opt/certbot-auto certonly --webroot -w /var/www/html/fr/carat-immobilier/this/ -d carat-immobilier.fr -d www.carat-immobilier.fr --agree-tos
            $cert_bot_command = "/opt/certbot-auto certonly --noninteractive --webroot -w {$website['directory']} -d {$website['domain']} ";
            foreach (explode(' ', $website['alias']) as $alias) {
                if ($alias) {
                    $cert_bot_command .= " -d $alias ";
                }
            }
            $cert_bot_command .= " --agree-tos";

            echo "Trying command $cert_bot_command \n";

            $command_status = $this->exec_command($cert_bot_command);
            echo $command_status;
            if (!$command_status) {
                $this->website_error($website['id']);
                return false;
            }

            # After getting certbot auth, then install the apache conf!
            foreach ($generated_conf_file as $key => $content) {
                $filename = $website[$key];
                # Variables are set, go ahead and create a2 configuration file
                if (!$this->generate_a2conf($website, $filename, $content)) {
                    $this->website_error($website['id']);
                    return false;
                }
            }

            # Graceful apache2 restart / reload
            if ($this->system_type == 'centos') {
                $command_status = $this->exec_command('service httpd reload');
                #$command_status = $this->exec_command('service apache2 reload');
            } elseif ($this->system_type == 'debian') {
                # $this->exec_command('a2ensite configuration file'); # TODO: apache2 in debian also requires a2ensite command.
                $command_status = $this->exec_command('service apache2 reload');
            } else {
                $command_status = false;
                $this->error_message = "System type is not defined (centos/debian)!";
            }
            if (!$command_status) {
                $this->website_error($website['id']);
                return false;
            }

            # make sure https was installed correctly
            $ch_domains = array_merge([$website['domain']], explode(' ', $website['alias']));
            foreach ($ch_domains as $domain) {
                if ($this->is_valid_domain_name($domain) && $domain != "") {
                    echo "Checking domain: https://" . $domain . "\n";
                    if (!$this->curl_get('https://' . $domain)) {
                        # Error while checking domain
                        $this->website_error($website['id']);
                        return false;
                    }
                }
            }
        }

        $this->website_done($website['id']);
        return true;
    }

    function make_backups ()
    {
        # $this->recurseCopy();
    }

    function generate_a2conf($website, $filename, $content)
    {
        # Must be run as root for those commands!
        $user = exec('whoami');
        if ($user != 'root') {
            $this->error_message .= 'This functionality must be run from root user!';
            return false;
        }

        # Validate correct domain format! This could cause hard problems
        if (!$this->is_valid_domain_name($website['domain'])) {
            $this->error_message .= 'This domain is invalid: ' . $this->website_domain;
            return false;
        }


        if ($this->system_type == 'centos') {
            if ($filename){
                file_put_contents($filename, $content);
            } else {
                return false;
            }
        } elseif ($this->system_type == 'debian') {
            if ($filename){
                file_put_contents($filename, $content);
            } else {
                return false;
            }
        }

        return true;
    }

    function edit_website_conf($filename)
    {
        # NOT USED! But Could be useful in editing constants.php file
        $confFile = @file_get_contents($filename);
        if (!$confFile) {
            $this->error_message .= 'Unable to open configuration file: ' . $filename;
        } else {
            # Replace file placeholders with correct data
            $confFile = str_replace("__DATABASE__", $this->website_domain, $confFile);
            $confFile = str_replace("__HOST__", $this->website_domain_alias, $confFile);
            $confFile = str_replace("__USER__", $this->apache_log_dir, $confFile);
            $confFile = str_replace("__PASSWORD__", $this->website_directory, $confFile);

            file_put_contents($this->conf_filename, $confFile);
        }
    }

    function check_database_exists($dbname)
    {
        $chdb = $this->pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
        return (bool) $chdb->fetchColumn();
    }

    function create_from_prototype($sql_original_db, $sql_copy_db)
    {
        try {
            # Check if database exists
            if ($this->check_database_exists($sql_copy_db)) {
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
        $this->exec_command($cmd1);
        # Import database dump to correct db
        $cmd2 = "mysql -u {$this->mysql_username} -p{$this->mysql_password} {$sql_copy_db} < {$sql_filename}";
        $this->exec_command($cmd2);

        return true;
    }

    function recurseCopy($src, $dst)
    {
        if (file_exists($dst)) {
            $this->error_message .= "Folder already exists: " . $dst;
            return False;
        }

        $dir = opendir($src);
        @mkdir($dst);

        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $src_filename = $src . '/' . $file;
                $dst_filename = $dst . '/' . $file;

                if ( is_dir($src_filename) ) {
                    $this->recurseCopy($src_filename, $dst_filename);
                } else {
                    copy($src_filename, $dst_filename);
                }
            }
        }
        closedir($dir);
        return True;
    }

    function prepare_conf_file()
    {
        # Read configuration file
        $confFile = @file_get_contents($this->template_file);
        if (!$confFile) {
            $this->error_message .= 'Unable to open apache2 configuration file.';
        } else {
            # Replace file placeholders with correct data
            $confFile = str_replace("__DOMAIN__", $this->website_domain, $confFile);
            $confFile = str_replace("__DOMAIN_ALIAS__", $this->website_domain_alias, $confFile);
            $confFile = str_replace("__LOG_DIR__", $this->apache_log_dir, $confFile);
            $confFile = str_replace("__DIRECTORY__", $this->website_directory, $confFile);

            $this->generated_conf_file = $confFile;
        }
    }

    function exec_command($cmd)
    {
        # Redirect stderr to out
        # $cmd .= ' 2>&1';
        exec($cmd, $output, $return_status);
        print_r($output);
        if ($return_status != 0) {
            $this->error_message .= implode(" ", $output);
            return false;
        }
        return true;
    }

    function is_valid_domain_name($domain_name)
    {
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
    }

    # Those helper functions edit the database website entry, in order for backend software to display status/errors
    function website_error($website_id)
    {
        # Update status to error and error to current error_message
        $query = "UPDATE website SET status=?, errors=? WHERE id=?";
        $this->pdo->prepare($query)->execute(['error', $this->error_message, $website_id]);
    }

    function website_done($website_id)
    {
        $query = "UPDATE website SET status=?, errors=? WHERE id=?";
        $this->pdo->prepare($query)->execute(['OK', '', $website_id]);
    }

    function website_deleted($website_id)
    {
        $query = "UPDATE website SET status=?, errors=? WHERE id=?";
        $this->pdo->prepare($query)->execute(['DELETED', '', $website_id]);
    }

    function website_reset_status($website_id, $status='CREATE')
    {
        # Reset website status to create (default)
        $query = "UPDATE website SET status=?, errors=? WHERE id=?";
        $this->pdo->prepare($query)->execute([$status, '', $website_id]);
    }

    function curl_get($page_url, $charset='ISO-8859-1', $username='', $password='')
    {
        # Inside page scan
        $ch = curl_init($page_url);
        //Tell cURL to return the output as a string.
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        if ($username) {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        }

        $response_raw = curl_exec($ch);

        $retry = 0;
        while ($response_raw === false and $retry < 3) {
            echo "Curl error happened - retry $retry: " . curl_error($ch);
            echo "\n$page_url\n";
            $response_raw = curl_exec($ch);
            $retry++;
        }

        if ($retry >= 3) {
            echo "Connection error, exit! \n";
            $this->error_message = "Access website error <$page_url> : " . curl_error($ch);
            return false;
        }

        # TODO: Make sure the charset is setup correctly
        $response_u = iconv($charset, 'UTF-8', $response_raw);
        $response_ud = html_entity_decode($response_u);  # utf-8 and decoded

        # Return response in UTF-8 (hopefully)
        return $response_ud;
    }

}
/*--------------------------------------------------------------------------------------------------------------------*/

#$sql_conn_username = 'stathis';
#$sql_conn_password = 's1234';
#$sql_conn_host = 'localhost';
#$testFile = '/var/www/netprofile2020/docs/constants.php';
#$testObj->edit_website_conf($testFile);







