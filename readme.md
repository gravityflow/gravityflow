Gravity Flow
==============================

[![Build Status](https://travis-ci.org/gravityflow/gravityflow.svg?branch=master)](https://travis-ci.org/gravityflow/gravityflow)  [![CircleCI](https://circleci.com/gh/gravityflow/gravityflow.svg?style=svg)](https://circleci.com/gh/gravityflow/gravityflow)

Gravity Flow is a premium plugin for WordPress which provides Workflow automation for forms created in Gravity Forms.

This repository is a development version of Gravity Flow intended to facilitate communication with developers. It is not stable and should not be used on production sites.

Do not use this repository as a way of evaluating Gravity Flow before purchasing - it's not stable and not everything will work and we will not be able to answer any questions.

Pull requests are welcome.

## Installation Instructions
The only thing you need to do to get this development version working is clone this repository into your plugins directory and activate script debug mode. If you try to use this plugin without script mode on the scripts and styles will not load and it will not work properly.

To enable script debug mode just add the following line to your wp-config.php file:

define( 'SCRIPT_DEBUG', true );

## Support
If you'd like to receive the stable release version, automatic updates and support please purchase a license here: https://gravityflow.io. 

We cannot provide support to anyone without a valid license.

## Test Suites

The integration tests can be installed from the terminal using:

    bash tests/bin/install.sh [DB_NAME] [DB_USER] [DB_PASSWORD] [DB_HOST]


If you're using VVV you can use this command:

	bash tests/bin/install.sh wordpress_unit_tests root root localhost

If you're using Local by Flywheel you can use this (change the IP address and port)

    bash tests/bin/install.sh local root root 192.168.95.100:4010

The acceptance tests are completely separate from the unit tests and do not require the unit tests to be configured. Ensure you have docker installed and run the following script:
 
    bash tests/acceptance-tests/start.sh


## Documentation
User Guides, FAQ, Walkthroughs and Developer Docs: http://docs.gravityflow.io

Class documentation: http://codex.gravityflow.io

## Translations
If you'd like to translate Gravity Flow into your language please create a free account here:

https://www.transifex.com/projects/p/gravityflow/

## Credits
Contributors:

* Steve Henty @stevehenty
* Richard Wawrzyniak @richardW8k
* Jamie Oastler @Idealien
* Yoren Chang @yoren
* Jake Jackson @gravitypdf
* WP Pro Translations (https://wp-translations.org/)

## Legal
Gravity Flow is a legally registered trademark belonging to Steven Henty S.L. Further details: https://gravityflow.io/trademark

Copyright 2015-2018 Steven Henty. All rights reserved.

