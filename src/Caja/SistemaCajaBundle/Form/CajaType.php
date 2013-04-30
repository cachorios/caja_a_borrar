<?php

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class CajaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('numero','integer')
            ->add('nombre')
            ->add('cajero')
            ->add('ubicacion','textarea')
            ->add('puerto','choice',array("choices" =>
                array(
                    'COM1'  =>  'COM1',
                    'COM2'  => 'COM2',
                    'COM3'  => 'COM3',
                    'COM4'  => 'COM4',
                    'COM5'  => 'COM5',
                    'COM6'  => 'COM6',
                    'COM7'  => 'COM7',
                    'COM8'  => 'COM8',
                    'COM9'  => 'COM9',
                    'COM10' => 'COM10'
                )))

        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SistemaCajaBundle\Entity\Caja'
        ));
    }

    public function getName()
    {
        return 'caja_sistemacajabundle_cajatype';
    }
}
