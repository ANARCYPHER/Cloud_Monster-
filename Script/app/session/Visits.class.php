<?php

namespace CloudMonster\Session;


class Visits extends System {

    protected static Visits|null $instance;

    public function __construct()
    {
        $this->expiresAfter = 3;
        parent::__construct('visits');
    }

}