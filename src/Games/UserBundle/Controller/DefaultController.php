<?php

namespace Games\UserBundle\Controller;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Games\UserBundle\Form\Type\UserType;
use Games\UserBundle\Entity\User;

class DefaultController extends Controller
{
    
    public function createAction(Request $request)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = new User();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->get('form.factory')->create(new UserType, $user);
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('games_killer_index');
                $response = new RedirectResponse($url);
            }
            
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
            
            //Modification de la notification
            $this->get('session')->getFlashBag()->set('success', 'Votre compte a bien créé');
            
            return $response;
        }
        
       
        // On passe la méthode createView() du formulaire à la vue
        // afin qu'elle puisse afficher le formulaire toute seule
        return $this->render('GamesUserBundle:Default:create.html.twig', array(
                'form' => $form->createView(),
        ));
    }
    
    
    public function loginAction(Request $request)
    {
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        
        // Dernier Login entré par l'utilisateur
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);
        
        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        // Erreur après soumission du formulaire   
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
           
            //Utilisateur non présent dans la BDD
            if ($userManager->findUserByUsername($lastUsername) === null ) {
                $this->get('session')->getFlashBag()->add('warning', 'Mauvais login');
                //Utilisateur présent, mauvais mot de passe
            } else {
                $this->get('session')->getFlashBag()->add('warning', 'Mot de passe incorrect');
            }
        // Pas d'erreur
        } else {
            $error = null;
        }
    
        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }
    
        if ( $request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            
        }
        
        
        return $this->render('GamesUserBundle:Default:login.html.twig', array(
          // Valeur du précédent nom d'utilisateur entré par l'internaute
          'last_username' => $session->get(SecurityContext::LAST_USERNAME),
          'error'         => $error,
        ));
    }
    
}
