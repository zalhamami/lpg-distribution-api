<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Jobs\ProcessUsersExport;
use App\Mail\ExportCompleteMail;
use App\Repositories\UserRepository;
use App\Role;
use App\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Propaganistas\LaravelPhone\PhoneNumber;

class UserController extends ApiController
{
    /**
     * UserController constructor.
     * @param UserRepository $repo
     */
    public function __construct(UserRepository $repo) {
        $this->repo = $repo;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->repo->getAll();
        return $this->collectionData($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyData(Request $request)
    {
        $user = $request->user();
        $response = $user;
        $response['roles'] = $user->roles;
        return $this->singleData($response);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        $user = $this->repo->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();
        $user->assignRole(Role::USER);
        $user->sendEmailVerificationNotification();

        return $this->successResponse([
            'message' => 'Successfully created user!'
        ], 201);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMyData(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
        ]);
        $data = [
            'name' => $request['name'],
        ];
        $user = $this->repo->update($request->user()->id, $data);
        return $this->singleData($user);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'confirmed', 'min:8']
        ]);
        $user = $request->user();
        if ($user->password) {
            return $this->errorResponse('Your password has been set before. Try to change it if you want', 400);
        }
        $user->password = bcrypt($request['password']);
        $user->save();
        return $this->singleData($user);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user = $request->user();
        if (!Hash::check($request['current_password'], $user->password)) {
            return $this->errorResponse('Current password is incorrect.', 400);
        }
        $user->password = bcrypt($request['new_password']);
        $user->save();
        return $this->singleData($user);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $response = $this->repo->delete($id);
        return $this->deleteMessage($response);
    }
}
