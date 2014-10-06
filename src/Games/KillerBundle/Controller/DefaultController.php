<?php

namespace Games\KillerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


use Games\UserBundle\Form\User;
use Games\KillerBundle\Entity\Killer;
use Games\KillerBundle\Entity\KillerRepository;
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
        // On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Sinon on déclenche une exception « Accès interdit »
            throw new AccessDeniedException('Accès limité aux personnes authentifiées.');
        }
        
        
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
               
            return $this->redirect($this->generateUrl('games_killer_consultKiller', array('name' => $killer->getName())));
       }
    
        return $this->render('GamesKillerBundle:Default:createKiller.html.twig', array(
                'form' => $form->createView(),
        ));
    }
    
    //cette méthode affiche le contenu d'un Killer
    public function consultKillerAction($name)
    {
        $killer = $this->getDoctrine()
        ->getRepository('GamesKillerBundle:Killer')
        ->findOneByName($name);
        
		// Puis modifiez la ligne du render comme ceci, pour prendre en compte les variables :
		return $this->render ( 'GamesKillerBundle:Default:consultKiller.html.twig', array (
				'killer' => $killer,
		) );
    }
    
    //cette méthode affiche la liste des Killers
    public function consultListKillersAction()
    {
        $killers = $this->getDoctrine()
        ->getRepository('GamesKillerBundle:Killer')
        ->findAll();
    
        // Puis modifiez la ligne du render comme ceci, pour prendre en compte les variables :
        return $this->render ( 'GamesKillerBundle:Default:consultListKillers.html.twig', array (
                'killers' => $killers,
        ) );
    }
   
}
