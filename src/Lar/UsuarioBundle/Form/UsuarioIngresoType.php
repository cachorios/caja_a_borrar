<?php

namespace Lar\UsuarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsuarioIngresoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lunes')
            ->add('martes')
            ->add('miercoles')
            ->add('jueves')
            ->add('viernes')
            ->add('sabado')
            ->add('domingo')
            ->add('lugar_ingreso')
            ->add('usuario')
            ->add('horario')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lar\UsuarioBundle\Entity\UsuarioIngreso'
        ));
    }

    public function getName()
    {
        return 'lar_usuariobundle_usuarioingreso';
    }
}
