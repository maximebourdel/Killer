<?php

namespace Games\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GamesUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
