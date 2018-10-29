#!/usr/bin/env bash

cd ../../

# Run just one test
#docker-compose run --rm codeception run -c codeception-browserstack.yml tests/acceptance-tests/acceptance/0038CurrentStepMergeTagCept.php -vvv --html --env mac-safari

# Run tests in a group.
# Create groups by adding the following comment to the top of the tests
# // @group myGroup
#docker-compose run --rm codeception run -g myGroup -vvv --html

# Run all tests
docker-compose run --rm codeception run -c codeception-browserstack.yml -vvv --html --env win-chrome
