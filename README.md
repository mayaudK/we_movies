# we_movies
this is a cinema website, which looks like Allocine

To run the project, you need to do the following steps:

pre-requisites:
- docker
- docker-compose

1. Clone the project
2. Run the command docker-compose up --build
3. Run the command ```docker exec -it php bash``` to go inside the php container
4. Run the command ```make run``` to install the dependencies and launch the project

You can now access the project at the following address: https://localhost:8000 (Symfony launch the project on port 8000 by default)


All commands except git clone are to be run in the php container.