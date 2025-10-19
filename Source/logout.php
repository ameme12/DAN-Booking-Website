<?php
session_start();
session_unset();
session_destroy();

// Author: Neelab Wafasharefe

header('Location: landing.html');
exit();
?>
