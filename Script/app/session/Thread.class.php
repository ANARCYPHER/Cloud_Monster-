<?php

namespace CloudMonster\Session;


class Thread extends System {

    protected static Thread|null $instance;

    public function __construct()
    {

        $this->expiresAfter = 60 * 60; //1 hour
        $this->recordMemoryUsage = true;
        parent::__construct('thread');
    }


}