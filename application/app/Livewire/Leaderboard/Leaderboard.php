<?php

namespace App\Livewire\Leaderboard;

use App\Application\DTOs\User\CreateUserInputDTO;
use App\Application\DTOs\User\UpdateScoreInputDTO;
use App\Application\UseCases\User\CreateUserUseCase;
use App\Application\UseCases\User\DeleteUserUseCase;
use App\Application\UseCases\User\GetAllUsersUseCase;
use App\Application\UseCases\User\GetUserUseCase;
use App\Application\UseCases\User\UpdateScoreUseCase;
use App\Domain\Entities\User;
use App\Domain\Enums\ScoreOperationEnum;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;
use App\Domain\Services\QrCodeService;
use App\Infrastructure\Repositories\UserEloquentRepository;
use Livewire\Component;

class Leaderboard extends Component
{
    public array $users = [];
    protected UserRepositoryInterface $userRepository;

    public ?array $selectedUser = null;

    // For adding new user
    #[Rule('required|string|max:150')]
    public string $name = '';

    #[Rule('required|integer|min:1|max:100')]
    public int $age = 18;

    #[Rule('required|string|max:350')]
    public string $address = '';

    // Points to add/subtract
    public int $points = 1;

    // Modal visibility flags
    public bool $showUserModal = false;
    public bool $showAddUserModal = false;

    public function boot(UserEloquentRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function render()
    {
        return view('livewire.leaderboard.leaderboard');
    }

    public function mount()
    {
        $this->refreshUsers();
    }

    public function refreshUsers()
    {
        $getAllUsersUseCase = new GetAllUsersUseCase($this->userRepository);
        $outputDTO = $getAllUsersUseCase->execute();
        $this->users = $outputDTO->users;
    }

    public function addPoints($userId)
    {
        try {
            $inputDTO = new UpdateScoreInputDTO($userId, ScoreOperationEnum::Add);
            $updateScoreUseCase = new UpdateScoreUseCase($this->userRepository);
            $updateScoreUseCase->execute($inputDTO);

            $this->refreshUsers();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function subtractPoints($userId)
    {
        try {
            $inputDTO = new UpdateScoreInputDTO($userId, ScoreOperationEnum::Subtract);
            $updateScoreUseCase = new UpdateScoreUseCase($this->userRepository);
            $updateScoreUseCase->execute($inputDTO);
            $this->refreshUsers();
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function showUserDetails($userId)
    {
        try {
            $getUserUseCase = new GetUserUseCase($this->userRepository);
            $outputDTO = $getUserUseCase->execute($userId);
            $this->selectedUser = $outputDTO->user;
            $this->showUserModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'User not found!');
        }
    }

    public function closeUserModal()
    {
        $this->showUserModal = false;
        $this->selectedUser = null;
    }

    public function showAddUserForm()
    {
        $this->resetAddUserForm();
        $this->showAddUserModal = true;
    }

    public function closeAddUserModal()
    {
        $this->showAddUserModal = false;
        $this->resetAddUserForm();
    }

    public function resetAddUserForm()
    {
        $this->name = '';
        $this->age = 18;
        $this->address = '';
        $this->resetValidation();
    }

    public function addUser()
    {
        $this->validate(User::rules());

        try {
            $inputDTO = new CreateUserInputDTO(
                $this->name,
                $this->age,
                $this->address
            );

            $qrService = new QrCodeService();
            $createUserUseCase = new CreateUserUseCase($this->userRepository, $qrService);
            $createUserUseCase->execute($inputDTO);

            $this->closeAddUserModal();
            $this->refreshUsers();
            session()->flash('success', 'User added successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add user: ' . $e->getMessage());
        }
    }

    public function deleteUser($userId)
    {
        try {
            $deleteUserUseCase = new DeleteUserUseCase($this->userRepository);
            $deleteUserUseCase->execute($userId);
            $this->refreshUsers();
            session()->flash('success', 'User deleted successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
}
