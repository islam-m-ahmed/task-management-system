<?php

namespace App\Architecture\Services\Interfaces;

interface IAuthService
{
    public function login(array $credentials);

    public function logout();
}
