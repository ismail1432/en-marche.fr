<?php

namespace AppBundle\Admin;

use AppBundle\Summary\Contribution;
use AppBundle\Summary\JobDuration;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SummaryAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_par_page' => 32,
        '_sort_order' => 'ASC',
        '_sort_by' => 'member',
    ];

//    protected function configureShowFields(ShowMapper $showMapper)
//    {
//        $showMapper
//            ->add('member', null, [
//                'label' => 'Membre',
//            ])
//            ->add('currentProfession', null, [
//                'label' => 'Métier principal',
//            ])
//            ->add('contributionWish', null, [
//                'label' => 'Souhait de contribution',
//                'field_type' => ChoiceType::class,
//                'field_options' => [
//                    'choices' => Contribution::CHOICES,
//                ],
//            ])
//            ->add('availabilities', null, [
//                'label' => 'Disponibilités',
//                'field_type' => ChoiceType::class,
//                'field_options' => [
//                    'choices' => JobDuration::CHOICES,
//                ],
//            ])
//            ->add('contactEmail', null, [
//                'label' => 'Email',
//            ])
//        ;
//    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('member', null, [
                'label' => 'Membre',
                'show_filter' => true,
            ])
            ->add('currentProfession', null, [
                'label' => 'Métier principal',
            ])
            ->add('contributionWish', null, [
                'label' => 'Souhait de contribution',
                'show_filter' => true,
            ], ChoiceType::class, ['choices' => Contribution::CHOICES])
            ->add('availabilities',null, [
                'label' => 'Disponibilités',
                'show_filter' => true,
            ], ChoiceType::class, ['choices' => JobDuration::CHOICES])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('member', null, [
                'label' => 'Membre',
            ])
            ->add('currentProfession', null, [
                'label' => 'Métier principal',
            ])
            ->add('contributionWishLabel', null, [
                'label' => 'Souhait de contribution',
            ])
            ->add('availabilities', null, [
                'label' => 'Disponibilités',
                'template' => 'admin/summary/list_availabilities.html.twig',
            ])
            ->add('contactEmail', null, [
                'label' => 'Email',
            ])
            ->add('public', null, [
                'label' => 'Visible au public',
            ])
        ;
    }
}
