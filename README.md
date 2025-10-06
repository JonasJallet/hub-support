# HubSupport

HubSupport is a support ticket management platform developed as part of a technical interview challenge. It allows users to create, manage, and track support tickets efficiently. The platform is built with a modern tech stack and follows best practices in architecture and software design.

## Run the platform by using `make start`

This command will typically handle necessary steps like compiling code, installing dependencies, and launching the services in separate processes.

## Technologies Used

- **Docker**: For containerization of services (both frontend and backend).
- **Symfony**: A powerful PHP framework used for the backend API.
- **FrankenPHP**: A high-performance PHP server that ensures fast, efficient handling of PHP requests.
- **Vue.js 3**: A progressive JavaScript framework used for building the frontend of the application.
- **CQRS (Command Query Responsibility Segregation)**: An architectural pattern to separate the command and query responsibilities for improved performance and scalability.
- **DDD (Domain-Driven Design)**: A software design approach that focuses on modeling the domain and its logic.
- **SOLID principles**: A set of object-oriented design principles that ensure the application is modular, maintainable, and scalable.

## Available Make Commands

Here is a list of the available `make` commands to interact with the platform:

### 1. `make start`
Builds and starts both the backend and frontend services. This command sets up the entire platform, ensuring both the API and the web app are ready to go.

### 2. `make check-daily-clients`
Checks daily client activities by executing a specific Symfony console command inside the backend container. This is useful for monitoring and managing daily client interactions.

### 3. `make api-tests`
To run the PHPUnit tests for the backend

### Other Useful Commands

- `make api-start` : Build and start the backend API.
- `make api-up` : Start backend containers only.
- `make api-down` : Stop backend containers.
- `make api-build` : Rebuild the backend containers.
- `make api-logs` : View logs for the backend API.
- `make api-sh` : Open a shell inside the backend container.

- `make web-app-start` : Start the frontend web app.
- `make web-app-down` : Stop the frontend web app.
- `make web-app-logs` : View logs for the frontend web app.
- `make web-app-restart` : Restart the frontend web app by stopping and starting it again.

- `make up` : Starts the backend and frontend services together.
- `make down` : Stops both the backend and frontend services.
- `make restart` : Restarts both the backend and frontend services.  
