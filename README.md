# FriendFamilyIndonesia Project Management

![image](https://raw.githubusercontent.com/SeptiawanAjiP/dewakoding-project-management/refs/heads/main/image-1.jpeg)
![image](https://raw.githubusercontent.com/SeptiawanAjiP/dewakoding-project-management/refs/heads/main/image-4.jpeg)

A Laravel Filament 3 application for managing projects with ticket management and status tracking.

## Features

- Project management with ticket prefix configuration
- Team member management with role assignments
- Customizable ticket statuses with color coding
- Ticket management with assignees and due dates
- Unique ticket identifiers (PROJECT-XXXXXX format)

## Requirements

- PHP > 8.2+
- Laravel 12
- MySQL 8.0+ / PostgreSQL 12+
- Composer

![image](https://raw.githubusercontent.com/SeptiawanAjiP/dewakoding-project-management/refs/heads/main/image-2.jpeg)

## Installation

1. Clone the repository:
   ```
  https://github.com/muhamad-prasetyo/Project-Management-system.git

2. Install dependencies:
   ```
   composer install
   npm install
   ```

3. Set up environment:
   ```
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure database in `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=dproject_management
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Run migrations:
   ```
   php artisan migrate
   ```

6. Create a Filament admin user:
   ```
   php artisan make:filament-user
   ```
7. Activate Role & Permission
   ```
   php artisan shield:setup
   php artisan shield:install
   php artisan shield:super-admin
   ```

8. Start the development server:
   ```
   php artisan serve
   ```

## Usage

1. Access the Filament admin panel at `http://localhost:8000/admin`
2. Log in with the Filament user credentials you created
3. Create a new project with custom ticket prefix
4. Add team members to the project
5. Create and customize ticket statuses
6. Add tickets and assign to team members

## Project Structure

### Models
- **Project**: Core project entity with ticket prefix and description
- **User**: Extended Laravel user model for team members
- **TicketStatus**: Configurable status columns for tickets
- **Ticket**: Task cards with relationships to status and assignee

### Filament Resources
- **ProjectResource**: CRUD for projects with relation managers
- **UserResource**: User management with secure password handling
- **TicketsRelationManager**: Manages tickets within projects
- **TicketStatusesRelationManager**: Manages ticket status options
- **MembersRelationManager**: Handles team membership

## License

