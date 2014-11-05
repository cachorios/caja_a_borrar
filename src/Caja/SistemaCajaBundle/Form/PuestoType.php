<?php

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PuestoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcion')
            ->add('nroBox')
            ->add('puerto', 'choice', array("choices" =>
                    array(
                        'COM1' => 'COM1',
                        'COM2' => 'COM2',
                        'COM3' => 'COM3',
                        'COM4' => 'COM4',
                        'COM5' => 'COM5',
                        'COM6' => 'COM6',
                        'COM7' => 'COM7',
                        'COM8' => 'COM8',
                        'COM9' => 'COM9',
                        'COM10' => 'COM10'
                    )))
            ->add('delegacion');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SistemaCajaBundle\Entity\Puesto'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'caja_sistemacajabundle_puesto';
    }
}
