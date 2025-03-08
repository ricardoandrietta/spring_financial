# Spring Financial
Back End Developer Assignment

### Running

- mkdir data
- Under application folder
  - composer install
  - php artisan migrate
  - php artisan db:seed
  - npm install && npm run build

### Resetting Score

- `php artisan app:reset-score`


### Project Structure (WIP)

```
app/
├── Domain/                      # Domain layer (core business logic)
│   ├── Entities/                # Business models/entities
│   ├── ValueObjects/            # Immutable value objects
│   ├── Repositories/            # Repository interfaces
│   │   └── Interfaces/          # Repository contracts
│   ├── Services/                # Domain services
│   └── Events/                  # Domain events
│
├── Application/                 # Application layer (use cases)
│   ├── UseCases/                # Application use cases/interactors
│   │   ├── User/                # Grouped by entity
│   │   │   ├── CreateUser/
│   │   │   ├── UpdateUser/
│   │   │   └── DeleteUser/
│   │   └── Product/             # Another entity group
│   ├── DTOs/                    # Data Transfer Objects
│   ├── Exceptions/              # Application exceptions
│   └── Interfaces/              # Input/output ports
│
├── Infrastructure/              # Infrastructure layer
│   ├── Repositories/            # Repository implementations
│   ├── Services/                # External service implementations
│   ├── Persistence/             # Database related code
│   │   └── Eloquent/            # Eloquent models and related code
│   └── ExternalAPIs/            # External API integrations
```