<?php

namespace App\Http\Controllers;

use App\General;
use App\Role;
use App\User;
use App\Traits\ApiResponser;

class ApiController extends Controller
{
    use ApiResponser;

    protected $repo;

    /**
     * @param General $targetResource
     * @param User $user
     * @return bool
     */
    protected function isOwner(General $targetResource, User $user)
    {
        if ($user->hasRole(Role::ADMIN) || $targetResource->user_id === $user->id) {
            return true;
        }
        return false;
    }
}
