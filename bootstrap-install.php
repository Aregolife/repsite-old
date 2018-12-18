<?php
$lockfile = realpath(dirname(__FILE__)) . '/__bootstrap.lock';
$bin_dir = realpath(dirname(__FILE__)) . '/bin/';
$bashrc = realpath(dirname(__FILE__)) . '/.bashrc';
$git_ignore_example = realpath(dirname(__FILE__)) . '/gitignore-example';
if(file_exists($lockfile)){
	die(json_encode(['status' => 'error','message' => '__bootstrap.lock exists! bootstrap may already be running or a failed run happened. Exiting now...'],1));
}
file_put_contents($lockfile,json_encode(['runtime' => date('YYYY-MM-DD H:i:s')],1));

$composer_script = <<<EOF
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
EOF;

echo shell_exec($composer_script);
$move_script = <<<MV_SCRIPT_EOF
if [ -d $bin_dir ]; then
 	mv composer.phar ${bin_dir}composer;
else
	mkdir $bin_dir
	mv composer.phar ${bin_dir}composer;
fi
MV_SCRIPT_EOF;
file_put_contents($tmp_file = tempnam('/tmp/','bootstrap-composer-script'),$move_script);
chmod($tmp_file,0700);
echo shell_exec($tmp_file);
$path_var = 'PATH=$PATH:' . $bin_dir;
file_put_contents($bashrc,$path_var . PHP_EOL . 'export PATH' . PHP_EOL );
echo 'If you want bin in your PATH, source the .bashrc that I just created.' . PHP_EOL;

#$phpunit_script = <<<__UNIT_SCRIPT
#wget https://phar.phpunit.de/phpunit-nightly.phar
#php phpunit-nightly.phar --version
#mv phpunit-nightly.phar ${bin_dir}phpunit
#__UNIT_SCRIPT;
#
echo 'Grabbing phpunit via composer require. This may take awhile...' . PHP_EOL;
echo shell_exec("${bin_dir}composer require --dev phpunit/phpunit");

echo 'Grabbing facebook/webdrive via composer require. This may take awhile...' . PHP_EOL;
echo shell_exec('php ' . $bin_dir . 'composer require facebook/webdriver');

echo 'Grabbing the selenium jar' . PHP_EOL;
echo shell_exec('wget http://selenium-release.storage.googleapis.com/3.9/selenium-server-standalone-3.9.1.jar');
echo shell_exec('mv selenium-server-standalone-3.9.1.jar ' . $bin_dir . 'selenium.jar');

file_put_contents($git_ignore_example,'vendor/
composer.lock
bin
gitignore-example
');

echo 'Check out ' . $git_ignore_example . ' for a basic .gitignore file example' . PHP_EOL;
if(file_exists($lockfile)){
	unlink(realpath(dirname(__FILE__)) . '/__bootstrap.lock');
}
