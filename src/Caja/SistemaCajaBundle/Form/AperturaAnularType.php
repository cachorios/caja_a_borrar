<?php

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

//use Doctrine\ORM\EntityRepository;

class AperturaAnularType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('detalle','collection',array(
            'label' => " Vencimientos e Importes ",
            'type' => new \Caja\SistemaCajaBundle\Form\LoteDetalleType(),
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,))
            ->add('pagos','collection',array(
            'label' => " Detalle de pago",
            'type' => new \Caja\SistemaCajaBundle\Form\LotePagoType(),
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,))

        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SistemaCajaBundle\Entity\Lote'
        ));
    }

    public function getName()
    {
        return 'caja_sistemacajabundle_aperturaanulartype';
    }
}
