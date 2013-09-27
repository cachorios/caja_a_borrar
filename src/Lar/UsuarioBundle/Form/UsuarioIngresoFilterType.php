<?php

namespace Lar\UsuarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class UsuarioIngresoFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'filter_number_range')
            ->add('usuario', 'filter_entity', array(
            'class' => 'UsuarioBundle:Usuario',
            'property' => 'username'))
            ->add('lunes', 'filter_boolean')
            ->add('martes', 'filter_boolean')
            ->add('miercoles', 'filter_boolean')
            ->add('jueves', 'filter_boolean')
            ->add('viernes', 'filter_boolean')
            ->add('sabado', 'filter_boolean')
            ->add('domingo', 'filter_boolean')
            ->add('lugar_ingreso', 'filter_boolean')
        ;

        $listener = function(FormEvent $event)
        {
            // Is data empty?
            foreach ($event->getData() as $data) {
                if(is_array($data)) {
                    foreach ($data as $subData) {
                        if(!empty($subData)) return;
                    }
                }
                else {
                    if(!empty($data)) return;
                }
            }

            $event->getForm()->addError(new FormError('Filter empty'));
        };
        $builder->addEventListener(FormEvents::POST_BIND, $listener);
    }

    public function getName()
    {
        return 'lar_usuariobundle_usuarioingresofiltertype';
    }
}
