Gravity Flow
==============================

[![Build Status](https://travis-ci.org/gravityflow/gravityflow.svg?branch=master)](https://travis-ci.org/gravityflow/gravityflow)  [![CircleCI](https://circleci.com/gh/gravityflow/gravityflow.svg?style=svg)](https://circleci.com/gh/gravityflow/gravityflow)

Gravity Flow is a premium plugin for WordPress which provides Workflow automation for forms created in Gravity Forms.

This repository is a development version of Gravity Flow intended to facilitate communication with developers. It is not stable and not intended for installation on production sites.

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

The acceptance tests are completely separate from the unit tests and do not require the unit tests to be configured. Steps to install and configure the acceptance tests:
 
1. Install the dependencies: `composer install`
2. Download and start either PhantomJS or Selenium.
3. Copy codeception-sample-vvv.yml or codeception-sample-pressmatic.yml to codeception.yml and adjust it to point to your test site. Warning: the database will cleaned before each run.
4. Run the tests: `./vendor/bin/codecept run`


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
* Jake Jackson @gravitypdf

Thank you also to all the translators:

* The team at WP Pro Translations (https://wp-translations.org/)
* Samuel Aguilera @samuelaguilera (samuelaguilera.com)
* FX Bénard @fxbenard (fxbenard.com)
* Erik van Beek @ErikvanBeek (erikvanbeek.nl)
* Alexander (Anticisco) Gladkov
* Edi Weigh (makeii.com)

Thanks to [BrowserStack](https://www.browserstack.com) for automated browser testing

Thank you to all my colleagues at Rocketgenius, Zack Katz (GravityView), Naomi C Bush (Gravity Plus) and Jake Jackson (Gravity PDF) for all their collaboration, advice, support and encouragement.

Copyright 2015-2018 Steven Henty, S.L.
