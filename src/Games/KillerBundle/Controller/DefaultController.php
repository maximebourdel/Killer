<?php

namespace Games\KillerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('GamesKillerBundle:Default:index.html.twig', array('name' => $name));
    }
}
