## Project Folder Structure

The application follows Clean Architecture principles with the following folder structure:

### Domain Layer (Core Business Logic)

```
app/Domain/
├── Entities/                               # Business models/entities
│   ├── User.php                            # User entity with core business rules
│   └── Winner.php                          # Winner entity
├── Enums/                                  # Enumeration types
│   └── ScoreOperationEnum.php              # Enum for score operations (add/subtract)
├── Jobs/                                   # Domain job interfaces
│   └── Interfaces/                         # Job contracts
├── Repositories/                           # Repository interfaces
│   └── Interfaces/                         # Repository contracts
│       ├── UserRepositoryInterface.php     # User repository contract
│       └── WinnerRepositoryInterface.php   # Winner repository contract
└── Services/                               # Domain services
    ├── Interfaces/                         # Service interfaces
    └── QrCodeService.php                   # QR code generation service
```

### Application Layer (Use Cases)

```
app/Application/
├── DTOs/                               # Data Transfer Objects
│   └── User/                           # User-related DTOs
│       ├── CreateUserDTO.php           # DTO for user creation
│       ├── UpdateUserDTO.php           # DTO for user updates
│       └── UserDTO.php                 # DTO for user representation
├── Exceptions/                         # Application-specific exceptions
│   ├── InvalidScoreException.php       # Exception for invalid score operations
│   └── UserNotFoundException.php       # Exception for user not found
└── UseCases/                           # Application use cases
    ├── User/                           # User-related use cases
    │   ├── AddPointsUseCase.php        # Add points to user
    │   ├── CreateUserUseCase.php       # Create new user
    │   ├── DeleteUserUseCase.php       # Delete user
    │   ├── GetUserUseCase.php          # Get user details
    │   ├── ListUsersUseCase.php        # List all users
    │   └── SubtractPointsUseCase.php   # Subtract points from user
    └── Winner/                         # Winner-related use cases
        └── DeclareWinnerUseCase.php    # Declare a winner
```

### Infrastructure Layer (External Interfaces)

```
app/Infrastructure/
├── Adapters/                         # External service adapters
│   └── GoQrAdapter.php               # Adapter for GoQR.me API
├── Jobs/                             # Job implementations
│   ├── DeclareWinnerJob.php          # Job to declare a winner
│   └── GenerateUserQrCode.php        # Job to generate QR code for user
└── Repositories/                     # Repository implementations
    ├── UserEloquentRepository.php    # Eloquent implementation of user repository
    └── WinnerEloquentRepository.php  # Eloquent implementation of winner repository
```

### Presentation Layer (Controllers & Views)

```
app/Http/
├── Controllers/                      # API Controllers
│   ├── Api/                          # API-specific controllers
│   │   ├── UserController.php        # User API endpoints
│   │   └── UserQrCodeController.php  # QR code generation endpoint
│   └── LeaderboardController.php     # Web interface controller
├── Livewire/                         # Livewire components
│   └── Leaderboard.php               # Real-time leaderboard component
└── Resources/                        # View resources
    └── Views/                        # Blade templates
        ├── components/               # UI components
        ├── layouts/                  # Page layouts
        └── leaderboard.blade.php     # Leaderboard view
```

This structure follows the principles of Clean Architecture, ensuring:

1. **Independence of frameworks**: Core business logic doesn't depend on external frameworks
2. **Testability**: Each layer can be tested independently
3. **Independence of UI**: The UI can change without affecting business rules
4. **Independence of database**: The database can be changed without affecting business rules
5. **Independence of external agencies**: Business rules don't know about external interfaces 