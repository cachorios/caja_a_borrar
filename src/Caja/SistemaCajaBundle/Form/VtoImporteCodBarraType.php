<?php

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VtoImporteCodBarraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vencimiento',null,array('attr' =>array("style" => "width: 40px;")))
            ->add('importe',null,array('attr' =>array("style" => "width: 40px;")))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SistemaCajaBundle\Entity\VtoImporteCodigoBarra'
        ));
    }

    public function getName()
    {
        return 'caja_sistemacajabundle_vtoimportecodbarratype';
    }
}
