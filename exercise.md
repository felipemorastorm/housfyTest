Housfy Senior Backend Test


As a backend developer you should create a project with an API to list all the Housfy offices.

Each office have the following fields:
- id
- name
- address

All the offices will be stored in a database.
Create the required migrations to populate the database with at least 50 offices.

The API should allow the following operations: create, read, update and delete.


Extra: 

The read operation should retrieve the results from a cached version.
If the result is not cached, you should create a queue job to cache it.

Create a docker-compose file with all the infrastructure requirements.

All your code must be tested as much as possible using unit and functional testing.

Create the required documentation to be sure we could test your application.


Requirements:

- Framework: Laravel >= 8
- Language: PHP 7.4
- Database: MySQL/MariaDB
- Cache: Redis, Memcached, etc.
- Queue: Redis, RabbitMQ, etc.
- Unit test: PHPUnit
- Functional test: Behat, Codeception, etc.
- Infrastructure: Docker and Docker Compose


What will we evaluated:

- Design: We know this is a very simple application but we want to see how you design code. We value having a clear application architecture, that helps maintain it (and make changes requested by the product owner) for years.

- Testing: We love automated testing and we love reliable tests. We like testing for two reasons: First, good tests let us deploy to production without fear (even on a Friday!). Second, tests give a fast feedback cycle so developers in dev phase know if their changes are breaking anything.

- Simplicity: If our product owner asks us for the same application but accessed by command line (instead of the http server) it should be super easy to bring to life!
