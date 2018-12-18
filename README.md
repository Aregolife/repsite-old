# repsite

# pre-reqs to the bootstrap-installer.php script
- Before running the bootstrap-install.php script, you probably want to have 'unzip' installed via sudo. I didn't put sudo in the bootstrap-install.php script because that's just insane.
- Before running the bootstrap-install.php script, you'll definitely need to install the java runtime. I installed default-jre via apt-get
` sudo apt-get update && apt-get -y install unzip default-jre `

# Installation
If this is your first time with the codebase, run:
$ php bootstrap-install.php

This will install composer, phpunit, and generate a .bashrc in the pwd that has a PATH variable loaded with the bin directory that it creates.

If you notice a file named something like 'bootstrap.lock' before you run the bootstrap-install.php script, it's because a previous run of the script likely failed. Investigate.



# changes
Dates are formatted in (YYYY-MM-DD)
- 2018-12-18 -> Created bootstrap-install.php script
	- installs composer, phpunit (via composer require), selenium 3.9.1 jar
	- creates .bashrc to include pwd/bin/ in your PATH environment variable


# vendor/autoload.php
- This project is aimed at being purely TDD combined with CI (thanks to TravisCI and github's support for it)
- Remember to source vendor/autoload.php if you run the phpunit tests. It's probably wise to create a macro in your .bashrc. Pretty trivial for loop stuff:
	function run_tests(){ 
	test_list=$(cat test_lists)
	for i in $test_list; do
		php bin/phpunit --bootstrap vendor/autoload.php tests/$i
	done
	}

# what's next
- travis.yml -> I need to create this so that github will automatically run our tests and give us a nice pass/fail badge on the master branch
- selenium powered phpunit tests
- classmap is going to be in src/


# nice to haves
- docker? maybe? I'm not sure if I have the time for configuring a docker instance, but that would be *really* nice to have
