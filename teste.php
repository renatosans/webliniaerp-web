<?php

$session_save_path = ini_get('session.save_path');

echo "session.save_path: " . $session_save_path . "<br>";

ini_set('session.save_path', getcwd() . '/tmp');

$session_save_path = ini_get('session.save_path');

echo "session.save_path: " . $session_save_path . "<br>";

?>