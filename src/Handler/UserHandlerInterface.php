<?php

namespace App\Handler;

use App\Entity\Image;
use App\Entity\User;

interface UserHandlerInterface
{
    public function changeProfilePicture(User $user, Image $image): void;

    public function register(User $user, $password): void;

    public function verifyEmail(User $user): void;

    public function forgotPassword(User $user): void;

    public function resetPassword(User $user, $password): void;
}
