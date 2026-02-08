<?php

namespace App\Architecture\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface IUserRepository extends IAbstractRepository
{
    public function findByEmail(string $email): ?Model;
}
