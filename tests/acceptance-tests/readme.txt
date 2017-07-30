The easiest way to run the acceptance tests is to run them in Docker.

Requirements:
- docker
- docker compose

Run the acceptance tests using start.sh. For example:
bash start.sh

You can watch Chrome perform the tests via VNC on localhost:5900. On OSX open vnc://localhost:5900 in Safari. Password: secret.

Optionally, you can remove the containers when you've finished:
docker-compose down
