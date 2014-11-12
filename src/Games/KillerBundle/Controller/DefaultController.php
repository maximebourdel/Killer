<?php

namespace Games\KillerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


use Games\UserBundle\Entity\User;
use Games\UserBundle\Form\UserType;

use Games\KillerBundle\Entity\Killer;
use Games\KillerBundle\Entity\KillerRepository;
use Games\KillerBundle\Form\KillerType;

use Games\KillerBundle\Entity\Player;
use Games\KillerBundle\Entity\PlayerRepository;
use Games\KillerBundle\Form\PlayerType;
use Games\KillerBundle\Form\PlayerEnablingType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $killers = $this->getDoctrine()
        ->getRepository('GamesKillerBundle:Killer')
        ->findAll();
        
        return $this->render('GamesKillerBundle:Default:index.html.twig', array (
                'killers' => $killers,
        ) );
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
    public function consultKillerAction(Request $request, $name)
    {
        // On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Sinon on déclenche une exception « Accès interdit »
            throw new AccessDeniedException('Accès limité aux personnes authentifiées.');
        }
        
        //on récupere les valeurs du killer
        $killer = $this->getDoctrine()
            ->getRepository('GamesKillerBundle:Killer')
            ->findOneByName($name);
        
        // On récupere l'utilisateur actuel
        $user = $this->get('security.context')->getToken()->getUser();
        
        
        
        //On gere la création du formulaire d'un nouveau participant
        $newPlayer = new Player();
        $formParticipation = $this->get('form.factory')->create(new PlayerType, $newPlayer);
        if ($formParticipation->handleRequest($request)->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            
            $newPlayer->setKiller($killer);
            $newPlayer->setUser($user);
        
            $em->persist($newPlayer);
        
            $em->flush();
             
            return $this->redirect($this->generateUrl('games_killer_consultKiller', array('name' => $killer->getName())));
        } 
        
        //si la personne a déja participé, on définit à null le player
        $isPlayerExists = $this->getDoctrine()
            ->getRepository('GamesKillerBundle:Player')
            ->findBy(array(
                    'user' => $user,
                    'killer' => $killer
            ));
        if ( $isPlayerExists != null ){ $newPlayer = null; }
            

       
        $participants = null;
        //on est à l'étape ou le jeu n'est pas commencé (participation)
        if($killer->getDateBegin() == null){ 
            
            if($killer->getUserAdmin()->getId() == $user->getId() ){
                
                //on liste les gens qui veulent participer
                $participants = $this->getDoctrine()
                ->getRepository('GamesKillerBundle:Player')
                ->findBy(array(
                        'killer' => $killer,
                ));
                
                
                foreach ($participants as $i => $participant ){
                    
                    $formType = new PlayerEnablingType($i);
                    
                    
                    $formParticipants[] = $this->get('form.factory')->create($formType, $participant);
                    
                    
                    if ($formParticipants[$i]->handleRequest($request)->isValid()) {
                        
                        $em = $this->getDoctrine()->getManager();
                        
                        $em->persist($participant);
                        
                        $em->flush();
                        
                        return $this->redirect($this->generateUrl('games_killer_consultKiller', array('name' => $killer->getName())));
                    }
                    
                    $formParticipants[$i] = $formParticipants[$i]->createView();
        
                }
            
            } else {
                $formParticipants[] = null;
            }
            
    		// Puis modifiez la ligne du render comme ceci, pour prendre en compte les variables :
    		return $this->render ( 'GamesKillerBundle:Default:consultKiller.html.twig', array (
    				'killer' => $killer,
    		        'formParticipation' => $formParticipation->createView(),
    		        'formParticipants' => $formParticipants,
    		        'user' => $user,
    		        'newPlayer' => $newPlayer,
    		        'participants' => $participants,
    		) );
        
        //étape ou le jeu est commencé!!
        } else {
            
            $allowedPlayers = $this->getDoctrine()
            ->getRepository('GamesKillerBundle:Player')
            ->findAllowedPlayers($killer);
            
            
            
            return $this->render('GamesKillerBundle:Default:consultKillerOn.html.twig', array (
                'allowedPlayers' => $allowedPlayers,
            ) );
            
        }    
    }
    
    
    
    
    
    //cette méthode affiche la liste des Killers
    public function consultListKillersAction()
    {
        // On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Sinon on déclenche une exception « Accès interdit »
            throw new AccessDeniedException('Accès limité aux personnes authentifiées.');
        }
        
        // On récupere l'utilisateur actuel
        $userId = $this->get('security.context')->getToken()->getUser()->getId();
        
        $killers = $this->getDoctrine()
        ->getRepository('GamesKillerBundle:Killer')
        ->findBy(
            array('userAdmin' => $userId)
        );
        // Puis modifiez la ligne du render comme ceci, pour prendre en compte les variables :
        return $this->render ( 'GamesKillerBundle:Default:consultListKillers.html.twig', array (
                'killers' => $killers,
        ) );
    }
    
    
    
    public function setKillerOnAction(Request $request, $id)
    {
        //on récupere les valeurs du killer
        $killer = $this->getDoctrine()
        ->getRepository('GamesKillerBundle:Killer')
        ->findOneById($id);
        
        
        foreach ($killer->getPlayers() as $player){
            
           $player->setPassword("lol");
            
            
           $em = $this->getDoctrine()->getManager();
                    
           $em->persist($player);
                    
          
        }
        
        
        $killer->setDateBegin(new \Datetime());
        
        $em->flush();
        
        
        return $this->redirect($this->generateUrl('games_killer_consultKiller', array('name' => $killer->getName())));
    }
}
