<?php

session_start();

require_once('config/config.inc.php');
require_once(INSTALL_ROOT . 'shared/core/paths.inc.php');
require_once(CORE_PATH . 'functions.inc.php');
require_once(CORE_PATH . 'session.class.php');
require_once(CORE_PATH . 'db.class.php');
require_once(CORE_PATH . 'userrequest.class.php');

require_once(MODEL_PATH . 'countrycodes.class.php');

require_once(SHARED_PATH . 'controller/controller.class.php');
require_once(CONFIG_PATH . 'fileconfig.class.php');
require_once(MODEL_PATH . 'list.class.php');
require_once(MODEL_PATH . 'file.class.php');

$userrequest = new UserRequest();
$userrequest->handleRequest();

?>