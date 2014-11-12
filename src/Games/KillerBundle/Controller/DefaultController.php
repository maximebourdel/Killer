<?php

namespace Games\KillerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Games\KillerBundle\Password\Password;
use Games\KillerBundle\ObjectsAttribution\ObjectsAttribution;

use Games\KillerBundle\Entity\Object;
use Games\KillerBundle\Entity\ObjectRepository;

use Games\KillerBundle\Entity\Image;
use Games\KillerBundle\Entity\ImageRepository;

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
            
            //on n'affiche pas le formulaire de gestion si l'utilisateur n'est le createur du Killer
            if($killer->getUserAdmin()->getId() == $user->getId() ){
                
                //on liste les gens qui veulent participer
                $participants = $this->getDoctrine()
                ->getRepository('GamesKillerBundle:Player')
                ->findBy(array(
                        'killer' => $killer,
                ));
                
                
                //gere le formulaire d'acceptation des participants
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
            
            //on récupere les valeurs du killer
            $allowedPlayers = $this->getDoctrine()
            ->getRepository('GamesKillerBundle:Player')
            ->findAllowedPlayers($killer->getId());
            
            
            return $this->render('GamesKillerBundle:Default:consultKillerOn.html.twig', array (
                    'killer' => $killer,
                    'participants' => $allowedPlayers,
                    'user' => $user,
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
        // On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Sinon on déclenche une exception « Accès interdit »
            throw new AccessDeniedException('Accès limité aux personnes authentifiées.');
        }
        
        $user = $this->get('security.context')->getToken()->getUser(); 
        
        //on récupere les valeurs du killer
        $allowedPlayers = $this->getDoctrine()
        ->getRepository('GamesKillerBundle:Player')
        ->findAllowedPlayers($id);
        
        $killer = $this->getDoctrine()
        ->getRepository('GamesKillerBundle:Killer')
        ->findOneById($id);
        
        
        //on récupere les valeurs du killer
        $objects = $this->getDoctrine()
        ->getRepository('GamesKillerBundle:Object')
        ->findAll();
        
        //on mélange les joueurs
        shuffle($allowedPlayers);
        
        foreach ($allowedPlayers as $allowedPlayer){
           
           /*
            * Attribution des objets
            */
           //génération du password
           $password = new Password();
           $allowedPlayer->setPassword( $password->generateNewPassword() );
           
           /*
            * Attribution des objets
            */
           //on sélectionne un objet au hasard
           $randomIndex = rand(0, sizeOf($objects)-1);
           
           //on l'attribue au joueur
           $allowedPlayer->setObject($objects[$randomIndex]);
           
           //on enleve l'objet de la liste pour qu'il ne soit utilisé qu'une fois
           array_splice($objects, $randomIndex, 1);
           
           $em = $this->getDoctrine()->getManager();
                    
           $em->persist($allowedPlayer);    
           
        }
        $em->flush();
        
        
        //on redéfinit les participants et la date du début
        $killer->setNbParticipants( count($allowedPlayers) );
        $killer->setDateBegin(new \Datetime());
        
        $em->persist($killer);
        
        $em->flush();
        
        
        //on récupere les valeurs du killer
        $allowedPlayers = $this->getDoctrine()
        ->getRepository('GamesKillerBundle:Player')
        ->findAllowedPlayers($id);
        
        //si c'est le créateur du killer
        if($killer->getUserAdmin()->getId() == $user->getId() ){
            
            return $this->render ( 'GamesKillerBundle:Default:consultKillerOn.html.twig', array (
        				'killer' => $killer,
                        'participants' => $allowedPlayers,
        		        'user' => $user,
        	) );    
        
        } else {
            
        }
    }
}
