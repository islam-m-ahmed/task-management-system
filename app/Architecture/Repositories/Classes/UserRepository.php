<?php

namespace App\Architecture\Repositories\Classes;

use App\Architecture\Repositories\Interfaces\IUserRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends AbstractRepository implements IUserRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Find a user by email
     */
    public function findByEmail(string $email): ?Model
    {
        return $this->prepareQuery()
            ->where('email', $email)
            ->first();
    }
}
