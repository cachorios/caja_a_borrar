<?php

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BarraDetalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('posicion','number')
            ->add('longitud','number')
            ->add('descripcion')
            ->add('tabla','parametro_choice',array(
                        'tabla' => 0,'attr' =>array('data-choice' =>'barra-detalle',)))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SistemaCajaBundle\Entity\BarraDetalle'
        ));
    }

    public function getName()
    {
        return 'caja_sistemacajabundle_barradetalletype';
    }
}
