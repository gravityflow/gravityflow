#!/usr/bin/env bash

cd ../../

# Run just one test
#docker-compose run --service-ports --rm codeception run tests/acceptance-tests/acceptance/0026RoleAssigneeTokenCept.php -vvv --html

# Run tests in a group.
# Create groups by adding the following comment to the top of the tests
# // @group myGroup
#docker-compose run --rm codeception run -g myGroup -vvv --html

# Run all tests
docker-compose run --service-ports --rm codeception run -vvv --html
