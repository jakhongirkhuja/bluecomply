<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginConfirmRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
    }

    public function authLogin(LoginRequest $request){
        return $this->authService->authLogin($request->validated());
    }
    public function authLoginConfirm(LoginConfirmRequest $request){
        return $this->authService->authLoginConfirm($request->validated());
    }
}
