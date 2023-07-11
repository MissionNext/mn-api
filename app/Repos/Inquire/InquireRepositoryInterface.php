<?php


namespace App\Repos\Inquire;

use App\Models\Job\Job;
use App\Models\User\User;

interface InquireRepositoryInterface
{
    const KEY = 'inquire';

    public function inquire(User $user, Job $job);
}
