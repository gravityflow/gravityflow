Gravity Flow
==============================

[![PHPUnit Tests](https://github.com/gravityflow/gravityflow/workflows/PHPUnit%20Tests/badge.svg?branch=master)](https://github.com/gravityflow/gravityflow/actions?query=branch%3Amaster+workflow%3A%22PHPUnit+Tests%22)  [![CircleCI](https://circleci.com/gh/gravityflow/gravityflow.svg?style=svg)](https://circleci.com/gh/gravityflow/gravityflow)

Gravity Flow is a premium plugin for WordPress which provides Workflow automation for forms created in Gravity Forms.

This repository is a development version of Gravity Flow intended to facilitate communication with developers. It is not stable and should not be used on production sites.

Do not use this repository as a way of evaluating Gravity Flow before purchasing - it's not stable and not everything will work and we will not be able to answer any questions.

Pull requests are welcome.

## Installation Instructions
The only thing you need to do to get this development version working is clone this repository into your plugins directory and activate script debug mode. If you try to use this plugin without script mode on the scripts and styles will not load and it will not work properly.

To enable script debug mode just add the following line to your wp-config.php file:

define( 'SCRIPT_DEBUG', true );

## Local Development
To work with the css and new js files, you will need to get setup to use [Gulp](https://gulpjs.com/) and [PostCSS](https://postcss.org/). Follow these steps:

* Important: if you are on Apple M1 Chips for now you must use a [Rosetta terminal](https://osxdaily.com/2020/11/18/how-run-homebrew-x86-terminal-apple-silicon-mac/)
* If you don't already have it installed, we recommend a Node Version Manager, eg [nvm](https://github.com/nvm-sh/nvm), or its [Windows equivalent](https://github.com/coreybutler/nvm-windows).
* Next check the `.nvmrc` file in the root of the repository and `nvm install X.XX.X` that version.
* Moving forward every time you wish to work with files in the src directory you should `nvm use` in this repo one time to make sure you are on the right version, nvm will use that rc file for that command.
* On your first install of the correct node version, you will need to run this command one time only: `npm install gulp-cli -g`
* Restart your terminal after running that.
* Now run `npm install` at root of the repository. This will install your node modules.
* To develop with [browsersync](https://browsersync.io/) you will want to run the `gulp dev` task. This requires you to rename the `config-sample.json` to `config.json` and adjust the cert path and domain to your local dev domain. Browsersync has a benefit over watch in that it injects your css changes as you dev without reloading the page, helping with speed.
* To develop with watch instead, you can just execute `gulp watch`
* When you have completed your work and are ready to push a pr, you will need to run `gulp dist`. This will build out all the development files and sourcemaps.
* When pulling in new code and wishing to use the build system again, always remember to run `npm install` before starting to make sure you get any new changes to the dependency tree others may have added.

## Icon Kits

This plugin contains an icon kit for the admin UI that is managed in Icomoon. All icons use the class `gflow-icon` and then `gflow-icon--NAME` and can be referenced in their respective demo kits found in the `dev/icons` directory.

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

