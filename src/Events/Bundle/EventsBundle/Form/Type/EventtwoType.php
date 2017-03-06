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

           if($this->subscribed->getEventtype4() == null){
                $eventtype4 = '';   
           }
           else {
               $eventtype4 = $this->subscribed->getEventtype4()->getId();
           }

       }
       else {

           $eventtype4 = '';
       }
  
       //Eventtype4
        $builder->add('eventtype4','choice',array(
            'choices' => array('12' => 'Six Hats Challenge', 
                               '13' => 'Shark Tank Challenge',
                               '14' => 'Effective Project Management in a Hostile Multicultural Environment',
                               '15' => 'Designing adverts with Cultural Dimensions in Mind',
                               '16' => 'Implement a Factory in Africa',
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Wednesday, 15th March (9:00am - 6:00pm)',
            'required' => false,
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