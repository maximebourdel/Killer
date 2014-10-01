<?php

namespace Games\KillerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use Games\UserBundle\Form\User;
use Games\KillerBundle\Entity\Killer;
use Games\KillerBundle\Form\KillerType;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('GamesKillerBundle:Default:index.html.twig', array('name' => $name));
    }
    
    //cette méthode crée une partie de Killer
    public function createKillerAction(Request $request)
    {
        // On récupere l'utilisateur actuel
        $user = $this->get('security.context')->getToken()->getUser();
        
        
        // On crée un objet Killer
        $killer = new Killer();
        
        $form = $this->get('form.factory')->create(new KillerType, $killer);
    
    
        if ($form->handleRequest($request)->isValid()) {
            
            
            $em = $this->getDoctrine()->getManager();
            
            $user->addMyKiller($killer);
            
            $em->persist($user);
            
            $em->persist($killer);
            $em->flush();
    
            //return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));
        }
    
        return $this->render('GamesKillerBundle:Default:createKiller.html.twig', array(
                'form' => $form->createView(),
        ));
    }
    
    
}
