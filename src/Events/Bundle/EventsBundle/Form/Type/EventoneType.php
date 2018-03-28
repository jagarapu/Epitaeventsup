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
       } 
       else {
           $eventtype1 = '';
           $eventtype2 = '';
       }
       //Eventtype1
       $builder->add('eventtype1','choice',array(
            'choices' => array('1' => 'Speed friending & quizz 1 (10:00 am – 11:30 am)', 
                               '2' => 'Speed friending & quizz 2 (11:30 am – 01:00 pm)'
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Event 1 (Wednesday, 11th April)',
            'required' => false,
            'data' => $eventtype1,
        ));
       //Eventtype2
        $builder->add('eventtype2','choice',array(
            'choices' => array('3' => 'Discovering crypto currencies (02:00 pm - 04:30 pm)', 
                               '4' => 'New - York under attack (02:00 pm - 04:30 pm)',
                               '5' => 'Web Analytics (02:00 pm – 04:30 pm)', 
                               '6' => 'Innovative adverts (02:00 pm – 04:30 pm)',
                               '7' => 'How does an IPO work (02:00 pm – 04:30 pm)',
                               '8' => 'Industry 4.0 (02:00 pm – 04:30 pm)',
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Event 2 (Wednesday, 11th April)',
            'required' => false,            
            'data' =>  $eventtype2,
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