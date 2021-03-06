<?php

namespace Games\KillerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Games\KillerBundle\Password\Password;

use Games\KillerBundle\Entity\Killer;

use Games\KillerBundle\Form\Type\KillerType;

use Games\KillerBundle\Entity\Player;
use Games\KillerBundle\Form\Type\PlayerType;
use Games\KillerBundle\Form\Type\PlayerEnablingType;
use Games\KillerBundle\Form\Type\PlayerEliminationType;

use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * Page d'index
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $killers = $this->getDoctrine()
        ->getRepository('GamesKillerBundle:Killer')
        ->findAll();
        
        return $this->render('GamesKillerBundle:Default:index.html.twig', array (
                'killers' => $killers,
        ) );
    }
    
    /**
     * Page de présentation du jeu du Killer
     * @param Request $request
     */
    public function presentationAction(Request $request)
    {
        return $this->render('GamesKillerBundle:Default:presentation.html.twig');
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
            
            //Ajout de la notification
            $this->get('session')->getFlashBag()->add('success', 'Votre killer a bien été créé');
            
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
        if ( $isPlayerExists !== null ){ $newPlayer = null; }

       
        $participants = null;
        //on est à l'étape ou le jeu n'est pas commencé (participation)
        if($killer->getDateBegin() === null){ 
            
            //on n'affiche pas le formulaire de gestion si l'utilisateur n'est le createur du Killer
            if($killer->getUserAdmin()->getId() == $user->getId()){
                
                
                //on liste les gens qui veulent participer
                $participants = $this->getDoctrine()
                ->getRepository('GamesKillerBundle:Player')
                ->findBy(array(
                        'killer' => $killer,
                ));
                
                
                foreach ($participants as $i => $participant ){
                    
                    $formType = new PlayerEnablingType($i);                    
                    $formParticipants[] = $this->get('form.factory')->create($formType, $participant);
                    
                    //la vérification de ce formulaire est effectuée dans la vue pour les participants en AJAX
                    $formParticipants[$i] = $formParticipants[$i]->createView();
                }
            
            } else {
                $formParticipants[] = null;
            }
            
            //rien ne sert d'afficher un formulaire si il n'y a personne
            if(sizeOf($participants) == 0){ $formParticipants[] = null; }
            
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
            //on n'affiche pas le formulaire de gestion si l'utilisateur n'est le createur du Killer
            if($killer->getUserAdmin()->getId() == $user->getId() ){
            
                //on récupere les valeurs du killer
                $allowedPlayers = $this->getDoctrine()
                ->getRepository('GamesKillerBundle:Player')
                ->findAllowedPlayers($killer->getId());
                
                
                return $this->render('GamesKillerBundle:Default:consultKillerOn.html.twig', array (
                        'killer' => $killer,
                        'participants' => $allowedPlayers,
                        'user' => $user,
                ) );
            //si ce n'est pas le créateur du killer
            } else {
                
                //on récupere le player du killer si il existe 
                $player = $this->getDoctrine()
                ->getRepository('GamesKillerBundle:Player')
                ->findOneBy(
                        array(
                                'killer' => $killer,
                                'user' => $user
                        )
                );
                                
                //si le joueur est inscrit
                if ($player !== null) {
                    //le joueur a été accepté
                    if ( $player->isAllowed() === true ){
                        
                            $playerEliminationForm = $this->get('form.factory')->create(new PlayerEliminationType, new Player());
                            
                            if ($playerEliminationForm->handleRequest($request)->isValid()) {
                                
                                $playerToKill = $player->getPlayerToKill(); 
                                
                                //on vérifie que le mdp entré est le bon    
                                if ($playerEliminationForm['password']->getData() == $playerToKill->getPassword()){
                                    
                                    $em = $this->getDoctrine()->getManager();
                                    
                                    $playerToKill->setDead(true);
                                    $playerToKill->setDeathDate(new \Datetime());
                                    
                                    
                                    $player->setNumkills($player->getNumkills()+1);
                                    
                                    //cas ou il n'y avait plus que 2 concurrents c'est la FIN
                                    if($playerToKill->getPlayerToKill() == $player){
                                        $player->setPlayerToKill(null);
                                        $killer->setDateEnd(new \Datetime());
                                        $em->persist($killer);
                                    } else {    
                                        $player->setPlayerToKill($playerToKill->getPlayerToKill());
                                    }
                                   
                                    $em->persist($player);
                                    $em->persist($playerToKill);
                                    
                                    $em->flush();
                                    
                                 //cas d'un mauvais mot de passe
                                 } else {
                                    // On définit un message flash
                                    $this->get ( 'session' )->getFlashBag ()->add ( 'warning', 'Mauvais mot de passe' );
                                    return $this->redirect($this->generateUrl('games_killer_consultKiller', array('name' => $killer->getName())));
                                 }
                                      
                        }
                        
                        $playerEliminationForm = $playerEliminationForm->createView();
                        
                        if ($player->getPlayerToKill() === null ){$playerEliminationForm == null;}
                        
                        return $this->render ( 'GamesKillerBundle:Default:consultKillerOnPlayer.html.twig', array (
                                'killer' => $killer,
                                'participants' => $player,
                                'user' => $user,
                                'playerEliminationForm' => $playerEliminationForm,
                                
                        ) );
                        
                    } else {
                        throw new AccessDeniedException('Désolé, ce killer est déja commencé et vous n\'avez pas été accepté :(');
                    }
                } else {
                    throw new AccessDeniedException('Désolé, ce killer est déja commencé et les inscriptions sont closes :/');
                }
            }
        }    
    }
    
    //cette méthode affiche la liste des Killers
    public function consultCreatedListKillersAction()
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
        return $this->render ( 'GamesKillerBundle:Default:consultListCreatedKillers.html.twig', array (
                'killers' => $killers,
        ) );
    }
       
    //cette méthode affiche la liste des Killers
    public function consultParticipationListKillersAction()
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
        ->findMyParticpationListKillers($userId);
        
        // Puis modifiez la ligne du render comme ceci, pour prendre en compte les variables :
        return $this->render ( 'GamesKillerBundle:Default:consultMyParticipationListKillers.html.twig', array (
                'killers' => $killers,
        ) );
    }
    
    //le killer est commencé
    public function setKillerOnAction(Request $request, $id)
    {
        // On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Sinon on déclenche une exception « Accès interdit »
            throw new AccessDeniedException('Accès limité aux personnes authentifiées.');
        }

        $em = $this->getDoctrine()->getManager();
                
        //on récupere les valeurs des joueurs
        $allowedPlayers = $this->getDoctrine()
        ->getRepository('GamesKillerBundle:Player')
        ->findAllowedPlayers($id);
        
        //on récupere les valeurs du killer
        $killer = $this->getDoctrine()
        ->getRepository('GamesKillerBundle:Killer')
        ->findOneById($id);
        
        // Il faut 2 participants autorisés au minimum pour lancer le jeu
        if (sizeof($allowedPlayers) < 2 ) {
            // Sinon on ne déclenche pas le Killer
            $this->get ( 'session' )->getFlashBag ()->add ( 'warning', 'Vous devez autoriser au moins deux participants.' );
            
            return $this->redirect($this->generateUrl('games_killer_consultKiller', array('name' => $killer->getName())));
        }
        
       
        //on récupere les valeurs des objets
        $weapons = $this->getDoctrine()
        ->getRepository('GamesKillerBundle:Weapon')
        ->findAll();
        
        //on mélange les joueurs
        shuffle($allowedPlayers);
        
        foreach ($allowedPlayers as $i=> $allowedPlayer){
           
           /*
            * Attribution de la personne à tuer
            */ 
            if ($i < sizeOf($allowedPlayers)-1){
                $allowedPlayer->setPlayerToKill($allowedPlayers[$i+1]);
            } else {
                $allowedPlayer->setPlayerToKill($allowedPlayers[0]);
            }
            
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
           $randomIndex = rand(0, sizeOf($weapons)-1);
           
           //on l'attribue au joueur
           $allowedPlayer->setWeapon($weapons[$randomIndex]);
           
           //on enleve l'objet de la liste pour qu'il ne soit utilisé qu'une fois
           array_splice($weapons, $randomIndex, 1);
           
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
        
        return $this->redirect($this->generateUrl('games_killer_consultKiller', array('name' => $killer->getName())));
        
    }
   
    public function ajaxrqValidatePlayerAction() {
        
        $request = $this->container->get('request');
    
        if ($this->container->get('request')->isXmlHttpRequest()) {
            
            //Récuperer le choix que vous fait dans la liste déroulante "Pays : "
            $id = $request->request->get('id');
            //Faire la requête pour récurer la liste des ville du pays sélectionné, grâce à leur "id" (fr, ma, es..), insérer ce résultat dans $villes
    
            //on récupere les valeurs du killer
            $participant = $this->getDoctrine()
            ->getRepository('GamesKillerBundle:Player')
            ->find($id);
            
            if($participant->isAllowed() === true ){
                $participant->setAllowed(false);
            } else {
                $participant->setAllowed(true);
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($participant);
            $em->flush();
            
        }
        //Instancier une "réponse" grâce à l'objet "Response"
        $response = new Response(json_encode("ok"));
        
        //Lui indiquer le type de format dans le quelle est envoyé la reponse
        $response->headers->set('Content-Type', 'application/json');
        
        //Retourner la tout à notre liste déroulante
        return $response;
    }
   
    public function ajaxrqGetAdresseCompleteAction() {
    
        $request = $this->container->get('request');
    
        if ($this->container->get('request')->isXmlHttpRequest()) {
    
            //Récuperer le choix que vous fait dans la liste déroulante "Pays : "
            $latLng = $request->request->get('latLng');
            //Faire la requête pour récurer la liste des ville du pays sélectionné, grâce à leur "id" (fr, ma, es..), insérer ce résultat dans $villes
            
            
            \Doctrine\Common\Util\Debug::dump($value);
            
            //Instancier une "réponse" grâce à l'objet "Response"
            $response = new Response(json_encode("http://maps.googleapis.com/maps/api/geocode/json?latlng=".$latLng));
            
        } else {
            //Instancier une "réponse" grâce à l'objet "Response"
            $response = new Response(json_encode("Erreur lors du chargement de la page"));   
        }
        
        //Lui indiquer le type de format dans le quelle est envoyé la reponse
        $response->headers->set('Content-Type', 'application/json');
        
        //Retourner la tout à notre liste déroulante
        return $response;
    }
    
}
