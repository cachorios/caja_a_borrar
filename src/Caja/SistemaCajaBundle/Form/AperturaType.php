<?php

namespace Caja\SistemaCajaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class AperturaType extends AbstractType
{
    private $usuario_id;

    public function __construct($usuario_id = null)
    {
        $this->usuario_id = $usuario_id;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(null == $options['data']->getId()) {
            $builder->add('fecha');
        }else{
            $builder->add('fecha',null,array("disabled" =>true));
        }
        $builder->add('habilitacion', 'entity', array(
            'empty_value' => "Seleccione el Puesto",
            'class' => 'SistemaCajaBundle:Habilitacion',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('h')
                    ->where('h.hasta is null')
                    ->andWhere('h.usuario = ' . $this->usuario_id)
                    ->orderBy('h.puesto', 'ASC');
            },
        ));

        $builder->add('importe_inicial','number');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SistemaCajaBundle\Entity\Apertura'
        ));
    }

    public function getName()
    {
        return 'caja_sistemacajabundle_aperturatype';
    }
}
