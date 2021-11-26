<?php

namespace App\Handler;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;

interface TrickHandlerInterface
{
    public function add(Trick $trick): Trick;

    public function edit(Trick $trick): Trick;

    public function delete(Trick $trick): void;

    public function addComment(User $user, Comment $comment, Trick $trick): void;
}
