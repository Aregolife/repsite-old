<?php
require dirname(__FILE__). '/../../vendor/autoload.php';
use AregoLife\Repsite\MockData\Customer as MDCustomer;

header('Content-type: application/json');
die((new MDCustomer('UT'))->dump());
