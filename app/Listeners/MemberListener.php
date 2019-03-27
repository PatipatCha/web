<?php

namespace App\Listeners;

class MemberListener
{
    public function doSomeThing($event)
    {
        //dd($event->member);
    }

    public function subscribe($events)
    {
        $events->listen(
            'App\Events\MemberWasCreated', 'App\Listeners\MemberListener@doSomeThing'
        );
    }
}
