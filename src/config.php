<?php
define('AREGOLIFE_DEBUG_MODE','1');
if(!defined('AREGOLIFE_API_KEY')){
define('AREGOLIFE_API_KEY',trim(file_get_contents(dirname(__FILE__) . '/api.key')));
define('AREGOLIFE_CONTEXT','AREGOLIFE');
}
