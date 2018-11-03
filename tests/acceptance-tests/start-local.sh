#!/usr/bin/env bash

cd ../../

# Run just one test
#docker-compose run --rm codeception run -c codeception-browserstack.yml tests/acceptance-tests/acceptance/0038CurrentStepMergeTagCept.php -vvv --html --env win-chrome

# Run tests in a group.
# Create groups by adding the following comment to the top of the tests
# // @group myGroup
#docker-compose run --rm codeception run -g myGroup -vvv --html

docker run -d -p 4444:4444 -p 5900:5900 -v /dev/shm:/dev/shm --name chrome selenium/standalone-chrome-debug:3.141.0-actinium
# Run all tests
./vendor/bin/codecept run -c codeception-local.yml -vvv --html

docker stop chrome
docker rm chrome
