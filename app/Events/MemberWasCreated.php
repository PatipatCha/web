<?php

namespace App\Events;

class MemberWasCreated
{
    public $member;

    public function __construct($member)
    {
        $this->member = $member;
    }
}
