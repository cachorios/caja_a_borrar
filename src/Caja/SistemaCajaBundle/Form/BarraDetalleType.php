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
            ->add('posicion','number',array('attr' =>array("style" => "width: 40px;")))
            ->add('longitud','number',array('attr' =>array("style" => "width: 40px;")))
            ->add('descripcion',null,array('attr' =>array("style" => "width: 150px;")))
            ->add('tabla','parametro_choice',array(
                        'tabla' => 0))
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
