<?php

namespace Games\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Games\UserBundle\Form\UserType;
use Games\UserBundle\Entity\User;

class DefaultController extends Controller
{
    
    public function createAction(Request $request)
    {
        // On crée un objet Advert
        $user = new User();

        $form = $this->get('form.factory')->create(new UserType, $user);
    
        
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->setEnabled(true);
            $em->persist($user);
            $em->flush();
            
            //return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));
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
    
        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = null;
        }
    
        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }
    
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);
    
        $csrfToken = $this->has('form.csrf_provider')
        ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
        : null;
    
        return $this->render('GamesUserBundle:Default:login.html.twig', array(
          // Valeur du précédent nom d'utilisateur entré par l'internaute
          'last_username' => $session->get(SecurityContext::LAST_USERNAME),
          'error'         => $error,
        ));
    }
    
}
