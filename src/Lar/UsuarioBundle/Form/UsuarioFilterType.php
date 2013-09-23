<?php

namespace Lar\UsuarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Lexik\Bundle\FormFilterBundle\Filter\Extension\Type\FilterTypeSharedableInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\QueryBuilder;
//use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;

class UsuarioFilterType extends AbstractType
{
    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $em = null){
        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('id',             'filter_number_range')
            ->add('nombre',         'filter_text')
            ->add('apellido',       'filter_text')
            ->add('username',       'filter_text')
            ->add('email',          'filter_text')
            ->add('direccion',      'filter_text')
            ->add('permite_email',  'filter_boolean')
            ->add('isActive',       'filter_boolean')
            ->add('isDeleted',      'filter_boolean')
            ->add('dni',            'filter_text')
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
        return 'lar_usuariobundle_usuariofiltertype';
    }

//    public function addShared(FilterBuilderExecuterInterface $qbe)
//    {
//        $closure = function(QueryBuilder $filterBuilder, $alias, $joinAlias, Expr $expr) {
//            // add the join clause to the doctrine query builder
//            // the where clause for the label and color fields will be added automatically with the right alias later by the Lexik\Filter\QueryBuilderUpdater
//
//            $filterBuilder->leftJoin($alias . '.options', 'opt');
//        } ;
//
//        // then use the query builder executor to define the join, the join's alias and things to do on the doctrine query builder.
//        $qbe->addOnce($qbe->getAlias().'.options', 'opt', $closure);
//
//    }
}
