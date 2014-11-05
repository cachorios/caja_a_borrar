<?php

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HabilitacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('desde')//, array('required' => true))
            ->add('hasta', null)
            ->add('caja', 'entity', array('required' => true,
                                          //'disabled' => true,
                                          'class' => 'SistemaCajaBundle:Caja',
                                          'empty_value' => "Seleccione la Caja"))
            ->add('usuario', 'entity', array('required' => true,
                                             'class' => 'UsuarioBundle:Usuario',
                                              'empty_value' => "Seleccione el Usuario"))
            ->add('puesto', 'entity', array('required' => true,
                                            'class' => 'SistemaCajaBundle:Puesto',
                                            'empty_value' => "Seleccione el Puesto"));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SistemaCajaBundle\Entity\Habilitacion'
        ));
    }

    public function getName()
    {
        return 'caja_sistemacajabundle_habilitacion';
    }
}
