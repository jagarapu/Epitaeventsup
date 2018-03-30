<?php

namespace Events\Bundle\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Events\Bundle\EventsBundle\Entity\Subscribed;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventtwoType extends AbstractType {
    
    protected $subscribed;
    
    public function __construct($subscribed){
        
        $this->subscribed = $subscribed;
       
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
       if (!empty($this->subscribed)){
           
           if($this->subscribed->getEventtype3() == null){
                $eventtype3 = '';   
           }
           else {
               $eventtype3 = $this->subscribed->getEventtype3()->getId();
           }
           
           if($this->subscribed->getEventtype4() == null){  
               $eventtype4 = '';   
           }
           else {
               $eventtype4 = $this->subscribed->getEventtype4()->getId();
           }

       }
       else {
           $eventtype3 = '';
           $eventtype4 = '';
       }
  
       //Eventtype3
        $builder->add('eventtype3','choice',array(
            'choices' => array('9' => 'Food tasting 1 (12:00 pm - 01:00 pm)',
                               '10' => 'Food tasting 2 (01:00 pm - 02:00 pm)',
                               
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Event 1 (Thursday, 12th April)',
            'required' => true,            
            'data' =>  $eventtype3,
        ));
        
       //Eventtype4
        $builder->add('eventtype4','choice',array(
            'choices' => array('11' => 'African Games (02:30 pm - 04:00 pm)',
                               '12' => 'Chinese Cooking (dumplings) (02:30 pm - 04:00 pm)', 
                               '13' => 'Indian Holi (02:30 pm - 04:00 pm)',
                               '14' => 'Bollywood (02:30 pm - 04:00 pm)',
                               '15' => 'Nepali Music (02:30 pm - 04:00 pm)',
                               '16' => 'Eggs painting (02:30 pm - 04:00 pm)',
                               '17' => 'Indian cricket (02:30 pm - 04:00 pm)',
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Event 2 (Thursday, 12th April)',
            'required' => true,
            'data' => $eventtype4,
        ));

         
 }

    public function getDefaultOptions(array $options) {
        return array('csrf_protection' => false);
    }

    public function getName() {
        return 'eventtwo';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
         $resolver->setDefaults(array(
            'data_class' => 'Events\Bundle\EventsBundle\Entity\Subscribed',
        ));
    }
}