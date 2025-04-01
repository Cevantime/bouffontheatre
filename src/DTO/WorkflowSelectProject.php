<?php

namespace App\DTO;

use App\Entity\Show;
use App\Entity\User;

class WorkflowSelectProject
{
    public Show $show;
    public User $owner;
}
