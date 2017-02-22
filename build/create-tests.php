<?php

require 'functions.php';

$wei = init();

$composerJson = json_decode(file_get_contents('composer.json'), true);
$plugin = end(explode('/', $composerJson['name']));

$wei->request->setPathInfo('/app/cli/tests/create')
    ->set('plugin', $plugin);

$wei->app();
