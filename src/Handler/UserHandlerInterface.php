<?php

namespace App\Handler;

use App\Entity\Image;
use App\Entity\User;

interface UserHandlerInterface
{
    public function changeProfilePicture(User $user, Image $image);

    public function register(User $user, $password);

    public function verifyEmail(User $user);

    public function forgotPassword(User $user);

    public function resetPassword(User $user, $password);
}
