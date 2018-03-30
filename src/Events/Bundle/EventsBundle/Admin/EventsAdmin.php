<?php

namespace Events\Bundle\EventsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Events\Bundle\EventsBundle\Entity\Subscribed;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EventsAdmin extends Admin { 
    
    
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        
        $formMapper  
            ->add('eventtype1', 'text', array('label' => 'Event Type1'))
            ->add('eventtype2', 'text', array('label' => 'Event Type2'))
            ->add('eventtype3', 'text', array('label' => 'Event Type3'))
            ->add('user','text', array('label' => 'User'))    
//            ->add('user.email','text', array('label' => 'User'))        
            ->add('eventtype4', 'text', array('label' => 'Event Type4'))     
            ->add('eventtype5','text', array('label' => 'Event Type5'))
            ->add('eventtype6','text', array('label' => 'Event Type6')) 
//            ->add('eventtype7','text', array('label' => 'Event Type7'))     
        ;
//        $formMapper->add('charge_status', 'choice', array('choices' => Entity::$chargeStatusList));
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper  
            ->add('user.username');
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('eventtype1.description', 'string', array('label' => 'Event Type1'))
            ->add('eventtype2.description', 'string', array('label' => 'Event Type2'))
            ->add('eventtype3.description', 'string', array('label' => 'Event Type3'))
            ->add('user.username', 'string', array('label' => 'User'))
            ->add('user.email', 'string', array('label' => 'User Email'))    
            ->add('eventtype4.description', 'string', array('label' => 'Event Type4'))
            ->add('eventtype5.description', 'string', array('label' => 'Event Type5'))
            ->add('eventtype6.description', 'string', array('label' => 'Event Type6'))
//            ->addIdentifier('eventtype7.description', 'string', array('label' => 'Event Type7'))                   
        ;
    }
    
   public function getExportFields() {
        return ['eventtype1.description', 'eventtype2.description', 'eventtype3.description',
            'user.username','user.email','eventtype4.description','eventtype5.description','eventtype6.description'
            ];
    }
    
    public function getDataSourceIterator()
    {
        $iterator = parent::getDataSourceIterator();
//        $iterator->setDateTimeFormat('d/m/Y'); //change this to suit your needs
        $iterator->setEventtype1();
        return $iterator;
    }
}
