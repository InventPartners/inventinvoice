<?php
define('SQL_DUMP_FILE', 'shared/install/db.sql');
define('CONFIG_FILE_PATH', 'config/config.inc.php');

function get_install_root() {
    $abs_path = __FILE__;
    $parts = explode('/', $abs_path);
    array_pop($parts);
    return join('/', $parts) . '/';
}

function get_site_domain() {
    return 'http://' . $_SERVER['HTTP_HOST'];
}

function input_post($name) {
    return isset($_POST[$name]) ? $_POST[$name] : null;
}

function form_group($name, $label, $type = 'text') {
    
    global $errors, $input_vals;
    
?>

<div class="form-group <?php if(isset($errors[$name])): ?> has-error<?php endif; ?>">
    
    <label for="<?php echo $name; ?>">
        <?php echo $label; ?>
    </label>
    
    <input 
           type="<?php echo $type; ?>" 
           name="<?php echo $name; ?>" class="form-control" 
           id="<?php echo $name; ?>" 
           placeholder="<?php echo $label; ?>" 
           value="<?php if(isset($input_vals[$name])) echo $input_vals[$name]; ?>" 
    />
    
    <?php if(isset($errors[$name])): ?>
    <span class="help-block"><?php echo $errors[$name]; ?></span>
    <?php endif; ?>
    
</div>

<?php
} // form_group()


$view = 'form';
$errors = array();
$input_vals = array();

if(is_file(CONFIG_FILE_PATH)){
    $view = 'already_installed';
} else if(!empty($_POST)) {
    
    // get inputs - database information
    $input_vals['database_host'] = input_post('database_host');
    $input_vals['database_name'] = input_post('database_name');
    $input_vals['database_user'] = input_post('database_user');
    $input_vals['database_pwd'] = input_post('database_pwd');
    
    // get inputs - admin user
    $input_vals['admin_user'] = input_post('admin_user');
    $input_vals['admin_pwd'] = input_post('admin_pwd');
    $input_vals['admin_pwd_confirm'] = input_post('admin_pwd_confirm');
    
    // validate inputs - database information
    if(empty($input_vals['database_host'])) $errors['database_host'] = 'Enter your database host.';
    if(empty($input_vals['database_name'])) $errors['database_name'] = 'Enter your database name.';
    if(empty($input_vals['database_user'])) $errors['database_user'] = 'Enter your database user.';
    if(empty($input_vals['database_pwd'])) $errors['database_pwd'] = 'Enter your database password.';
    
    // validate inputs - admin user
    if(empty($input_vals['admin_user'])) $errors['admin_user'] = 'Enter an admin username.';
    if(empty($input_vals['admin_pwd'])) $errors['admin_pwd'] = 'Enter an admin password.';
    if(empty($input_vals['admin_pwd_confirm'])) {
        $errors['admin_pwd_confirm'] = 'Confirm the admin password.';
    } else if($input_vals['admin_pwd'] != $input_vals['admin_pwd_confirm']) {
        $errors['admin_pwd_confirm'] = 'Admin passwords do not match.';
    }
    
    // passed validation
    if(empty($errors)) {
        
        // try connect to DB
        @$conn = new mysqli(
            $input_vals['database_host'],
            $input_vals['database_user'],
            $input_vals['database_pwd'],
            $input_vals['database_name']
        );

        // Check connection
        if ($conn->connect_error) {
            
            $errors['general'] = 'Failed to connect to MySQL: ' . $conn->connect_error;
            
        } else {
            
            // connected to db - run db import
            $commands = file_get_contents(SQL_DUMP_FILE);
            $result = $conn->multi_query($commands);
            
            if($result) {
                
                // consume results from multi query
                while($conn->more_results()){
                    $conn->next_result();
                    $conn->use_result();
                }
                
                // import worked - add admin user
                if($stmt = $conn->prepare("INSERT INTO user (username, password, is_admin) VALUES (?, PASSWORD(?),1)")) {
                    
                    if($stmt->bind_param("ss", $input_vals['admin_user'], $input_vals['admin_pwd'])) {
                        if($stmt->execute()) {
                            
                            // make the config file
                            $config = array();
                            array_push($config, '<?php');
                            array_push($config, 'define(\'INSTALL_ROOT\' , \'' . get_install_root() . '\');');
                            array_push($config, 'define(\'APP_PATH\' , INSTALL_ROOT);');
                            array_push($config, 'define(\'SITE_DOMAIN\' , \'' . get_site_domain() . '\');');
                            array_push($config, 'define(\'SHARED_PATH\' , INSTALL_ROOT . \'shared/\');');
                            array_push($config, 'define(\'DB_HOST\' , \'' . $input_vals['database_host'] . '\');');
                            array_push($config, 'define(\'DB_NAME\' , \'' . $input_vals['database_name'] . '\');');
                            array_push($config, 'define(\'DB_USER\' , \'' . $input_vals['database_user'] . '\');');
                            array_push($config, 'define(\'DB_PASS\' , \'' . $input_vals['database_pwd'] . '\');');
                            
                            $config_str = join(chr(13), $config);
                                
                            if($fp = fopen(CONFIG_FILE_PATH, 'w')){
                            	fwrite($fp, $config_str);
                            	fclose($fp);
                                $view = 'install_complete';
                            } else {
                                $view = 'no_write_permission';
                            }
                            
                            
                            
                            
                        } else {
                            $errors['general'] = 'execute() failed: ' . htmlspecialchars($conn->error);
                        }
                    } else {
                        $errors['general'] = 'bind_param() failed: ' . htmlspecialchars($conn->error);
                    }
                                        
                    $stmt->close();
                    
                } else {
                    $errors['general'] = 'prepare() failed: ' . htmlspecialchars($conn->error);
                }
                



            }
            
       		$conn->close();
            
        }  
        
    }
 
}

?>

<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invent Invoice Installer</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>

<body>
    <div class="container" style="max-width: 700px;">
        
        <div class="page-header">
            <h1 class="text-center">Invent Invoice Installer</h1>  
        </div>
        
        <?php if($view == 'form'): ?>
        
        <?php if(isset($errors['general'])): ?>
        <div class="alert alert-danger">
            <?php echo $errors['general']; ?>
        </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Database Information</h2>
                </div>
                <div class="panel-body">
                    
                    <?php
                    form_group('database_host', 'Database Host');
                    form_group('database_name', 'Database Name');
                    form_group('database_user', 'Database User');
                    form_group('database_pwd', 'Database Password', 'password');
                    ?>
                    
                </div>
            </div>


            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Create Admin User</h2>
                </div>
                <div class="panel-body">
                    
                    <?php
                    form_group('admin_user', 'Username');
                    form_group('admin_pwd', 'Password', 'password');
                    form_group('admin_pwd_confirm', 'Confirm Password', 'password');
                    ?>

                </div>
            </div>
            
            <input type="submit" value="Install" class="btn btn-lg btn-block btn-primary" style="margin-bottom:100px;" /> 
            
        </form>
        
        <?php elseif($view == 'install_complete'): ?>
        <div class="alert alert-success">
            <strong>Install complete!</strong> You may now <a href="/admin">log in</a> with the admin account you just created.
        </div>
        <?php elseif($view == 'already_installed'): ?>
        <div class="alert alert-warning">
        	Invent invoice is already installed!
        </div>   
        <?php elseif($view == 'no_write_permission'): ?>
        <div class="alert alert-danger">
        	<strong>Failed to write config file</strong> (permission denied). Please copy the code below and save to <em>config/config.inc.php</em>
        </div>
        <pre>
            <code>
<?php echo htmlentities($config_str); ?>
            </code>
        </pre>        
        <?php endif; ?>

    </div>
    <!-- /.container -->

    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script>
        $(function() {
        });
    </script>
</body>
</html>