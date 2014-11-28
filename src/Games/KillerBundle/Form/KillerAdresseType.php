<?php

namespace Games\KillerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KillerAdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name',     'text')
        ->add('adresse',  'text', array('read_only' => true))
        ->add('latitude',  'hidden')
        ->add('longitude',  'hidden')
        ->add('players', 'collection', array(
                'type'         => new PlayerType(),
                'allow_add'    => true,
                'allow_delete' => true
        ))
        ->add('save',     'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Games\KillerBundle\Entity\Killer'
        ));
    }

    public function getName()
    {
        return 'games_killerbundle_killer';
    }
}