<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    #[Assert\Email]
    public $email;
    public $lastname;
    public $firstname;
    public $message;
}
