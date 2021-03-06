<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\App\Validators\ValidatorFactory;
use App\Timeline\Domain\Services\UserService;
use App\Timeline\Domain\ValueObjects\Email;
use App\Timeline\Domain\ValueObjects\UserId;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request, ValidatorFactory $validatorFactory)
    {
        $validatorFactory->validate($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        $token = $this->userService->register(
            $request->get('name'),
            new Email($request->get('email')),
            $request->get('password')
        );

        return response()->json($token);
    }

    public function getCurrentUser()
    {
        $user = $this->userService->getCurrentUser();

        if ($user === null) {
            return response()->json(null, 404);
        }

        return response()->json($user);
    }

    public function login(Request $request, ValidatorFactory $validatorFactory)
    {
        $validatorFactory->validate($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $token = $this->userService->login(
            new Email($request->get('email')),
            $request->get('password')
        );

        return response()->json($token);
    }

    public function getAllUser()
    {
        $users = $this->userService->getAll();

        return response()->json($users);
    }

    public function update(string $id, Request $request, ValidatorFactory $validatorFactory)
    {
        $params = $request->all();
        $params['id'] = $id;

        $validatorFactory->validate($params, [
            'name' => 'nullable|string',
            'newPassword' => 'nullable|string|min:8',
            'oldPassword' => 'nullable|string|min:8',
            'isAdmin' => 'nullable|boolean',
            'isEditor' => 'nullable|boolean',
            'isActive' => 'nullable|boolean'
        ]);

        $user = $this->userService->update(
            new UserId(intval($id)),
            $request->get('name'),
            $request->get('oldPassword'),
            $request->get('newPassword'),
            $request->get('isAdmin'),
            $request->get('isEditor'),
            $request->get('isActive')
        );

        return response()->json($user);
    }
}

