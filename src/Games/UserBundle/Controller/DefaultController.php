<?php

namespace Games\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;


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
}
