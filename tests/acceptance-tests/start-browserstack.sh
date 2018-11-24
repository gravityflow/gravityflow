#!/usr/bin/env bash

cd ../../

# Run just one test
#docker-compose run --service-ports --rm codeception run -c codeception-browserstack.yml tests/acceptance-tests/acceptance/0033InboxLinkMergeTagCept.php -vvv --html --env ios

# Run tests in a group.
# Create groups by adding the following comment to the top of the tests
# // @group myGroup
#docker-compose run --rm codeception run -g myGroup -vvv --html

# Run all tests
docker-compose run --service-ports --rm codeception run -c codeception-browserstack.yml -vvv --html --env win-chrome
