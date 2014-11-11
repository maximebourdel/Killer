<?php

namespace Games\KillerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlayerEnablingType extends AbstractType
{
  
    private static $count = 0;
    private $suffix;
    
    public function __construct() {
        $this->suffix = self::$count++;
    }
    
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
      $builder
            
         ->add('isAllowed', 'choice', array(
            'expanded' => true,
            'choices' => array(true => 'autoriser', false => 'refuser'),
        ))  
        
        ->add('save',     'submit', array('label' => 'Valider'))
      ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Games\KillerBundle\Entity\Player'
    ));
  }

  public function getName()
  {
    return 'games_killerbundle_enablingPlayer'.$this->suffix;
  }
  
  
}