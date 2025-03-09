<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Application\DTOs\User\CreateUserInputDTO;
use App\Application\DTOs\User\UpdateScoreInputDTO;
use App\Application\Exceptions\UserNotFoundException;
use App\Application\UseCases\User\CreateUserUseCase;
use App\Application\UseCases\User\DeleteUserUseCase;
use App\Application\UseCases\User\GetAllUsersUseCase;
use App\Application\UseCases\User\GetUserUseCase;
use App\Application\UseCases\User\UpdateScoreUseCase;
use App\Domain\Entities\User;
use App\Domain\Enums\ScoreOperationEnum;
use App\Domain\Services\QrCodeService;
use App\Http\Controllers\Controller;
use App\Infrastructure\Repositories\UserEloquentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    protected UserEloquentRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserEloquentRepository();
    }

    /**
     * Get all users ordered by score
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $getAllUsersUseCase = new GetAllUsersUseCase($this->userRepository);
        $outputDTO = $getAllUsersUseCase->execute();

        return response()->json(['users' => $outputDTO->users]);
    }

    /**
     * Get a user by ID
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function show(int $userId): JsonResponse
    {
        try {
            $getUserUseCase = new GetUserUseCase($this->userRepository);
            $outputDTO = $getUserUseCase->execute($userId);

            return response()->json(['user' => $outputDTO->user]);
        } catch (UserNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate(User::rules());

        $inputDTO = new CreateUserInputDTO(
            name: $validatedData['name'],
            age: $validatedData['age'],
            address: $validatedData['address']
        );

        $qrService = new QrCodeService();
        $createUserUseCase = new CreateUserUseCase($this->userRepository,  $qrService);
        $outputDTO = $createUserUseCase->execute($inputDTO);

        return response()->json(['user' => $outputDTO->user], Response::HTTP_CREATED);
    }

    /**
     * Delete a user
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function destroy(int $userId): JsonResponse
    {
        $deleteUserUseCase = new DeleteUserUseCase($this->userRepository);
        $deleteUserUseCase->execute($userId);

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Add points to a user
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function addPoints(int $userId): JsonResponse
    {
        try {
            $inputDTO = new UpdateScoreInputDTO($userId, ScoreOperationEnum::Add);
            $updateScoreUseCase = new UpdateScoreUseCase($this->userRepository);
            $outputDTO = $updateScoreUseCase->execute($inputDTO);

            return response()->json(['user' => $outputDTO->user]);
        } catch (UserNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Subtract points from a user
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function subtractPoints(int $userId): JsonResponse
    {
        try {
            $inputDTO = new UpdateScoreInputDTO($userId, ScoreOperationEnum::Subtract);
            $updateScoreUseCase = new UpdateScoreUseCase($this->userRepository);
            $outputDTO = $updateScoreUseCase->execute($inputDTO);

            return response()->json(['user' => $outputDTO->user]);
        } catch (UserNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function listUsersByScore()
    {
        $users = $this->userRepository->getUsersGroupedByScore();
        return response()->json($users);
    }
}
