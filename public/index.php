<?php

//caricamento del framework
$f3 = require(__DIR__.'/../plugin/fatfree/base.php');

//inizializzazione app
$f3->config(__DIR__.'/../app/config.ini');
$f3->config(__DIR__.'/../app/settings.ini');

//definizione rotte
$f3->config(__DIR__.'/../app/routes.ini');

//esecuzione
$f3->run();
