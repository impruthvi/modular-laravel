<?php

namespace Modules\User;

use App\Models\User;

class UserDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    )
    {
    }

    public static function fromEloquentModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );
    }
}
