<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User\Auth\AuthService;
use App\Services\User\Auth\LogoutService;
use App\Services\User\Auth\RefreshService;

class AuthController extends Controller
{
    private $authService;
    private $logoutService;
    private $refreshService;

    public function __construct(
        AuthService $authService, 
        LogoutService $logoutService, 
        RefreshService $refreshService)
    {
        $this->authService = $authService;
        $this->logoutService = $logoutService;
        $this->refreshService = $refreshService;
    }

    public function authenticate(Request $request)
    {
        return $this->authService->authenticate($request);
    }

    public function logout()
    {
        return $this->logoutService->logout();
    }

    public function refresh()
    {
        return $this->refreshService->refresh();
    }

    public function me()
    {
        return $this->authService->me();
    }
}
