<?php

namespace Events\Bundle\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Events\Bundle\EventsBundle\Entity\Subscribed;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventoneType extends AbstractType {
    
    protected $subscribed;
    
    public function __construct($subscribed){
        
        $this->subscribed = $subscribed;
       
    }



    public function buildForm(FormBuilderInterface $builder, array $options) {
        
       if (!empty($this->subscribed)){
           if($this->subscribed->getEventtype1() == null){
                $eventtype1 = '';   
           }
           else {
               $eventtype1 = $this->subscribed->getEventtype1()->getId();
           }
           if($this->subscribed->getEventtype2() == null){
                $eventtype2 = '';   
           }
           else {
               $eventtype2 = $this->subscribed->getEventtype2()->getId();
           }
           if($this->subscribed->getEventtype3() == null){
                $eventtype3 = '';   
           }
           else {
               $eventtype3 = $this->subscribed->getEventtype3()->getId();
           }
       }
       else {
           $eventtype1 = '';
           $eventtype2 = '';
           $eventtype3 = '';
       }
       //Eventtype1
       $builder->add('eventtype1','choice',array(
            'choices' => array('1' => 'Expatriation & Intercultural Management 1 (9:00am – 10:30am)', 
                               '2' => 'Expatriation & Intercultural Management 2 (11:00am – 12:30pm)'
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Event 1',
            'required' => false,
            'data' => $eventtype1,
        ));
       //Eventtype2
        $builder->add('eventtype2','choice',array(
            'choices' => array('3' => 'Food tasting 1 (12:30pm - 1:30pm)', 
                               '4' => 'Food tasting 2 (1:30pm - 2:30pm)'
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Event 2',
            'required' => false,            
            'data' =>  $eventtype2,
        ));
        
       //Eventtype3
        $builder->add('eventtype3','choice',array(
            'choices' => array('5' => 'Holi (3:00pm – 4:00pm)', 
                               '6' => 'Kabaddi (3:00pm – 4:00pm)',
                               '7' => 'Bollywood (3:00pm – 4:00pm)',
                               '8' => 'African Dance (3:00pm – 4:00pm)',
                               '9' => 'Cricket (3:00pm – 4:00pm)',
                               '10' => 'Dabke (3:00pm – 4:00pm)',
                               '11' => 'Eggs painting (3:00pm – 4:00pm)',
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Event 3',
            'required' => false,
            'data' => $eventtype3,
        ));
         
 }

    public function getDefaultOptions(array $options) {
        return array('csrf_protection' => false);
    }

    public function getName() {
        return 'eventone';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
         $resolver->setDefaults(array(
            'data_class' => 'Events\Bundle\EventsBundle\Entity\Subscribed',
        ));
    }
}