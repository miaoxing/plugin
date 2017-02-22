<?php

require 'functions.php';

$wei = init();

$wei->request->setPathInfo('/app/cli/tests/create')
    ->set('plugin', basename(getcwd()));

$wei->app();
