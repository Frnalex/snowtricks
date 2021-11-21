<?php

namespace App\Handler;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;

interface TrickHandlerInterface
{
    public function add(Trick $trick);

    public function edit(Trick $trick);

    public function delete(Trick $trick);

    public function addComment(User $user, Comment $comment, Trick $trick);
}
