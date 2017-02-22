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
           if($this->subscribed->getEventtype4() == null){
                $eventtype4 = '';   
           }
           else {
               $eventtype4 = $this->subscribed->getEventtype4()->getId();
           }
       }
       else {
           $eventtype1 = '';
           $eventtype2 = '';
           $eventtype3 = '';
           $eventtype4 = '';
       }
       //Eventtype1
       $builder->add('eventtype1','choice',array(
            'choices' => array('1' => 'Préparation au choc culturel 1(2pm - 3:30pm)', 
                               '2' => 'Study Abroad at CSUMB(2pm - 3pm)'
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Thursday, 24th March Events(2pm - 3:30pm)',
            'required' => false,
            'data' => $eventtype1,
        ));
       //Eventtype2
        $builder->add('eventtype2','choice',array(
            'choices' => array('3' => 'Préparation au choc culturel 2(4pm - 5:30pm)', 
                               '4' => 'Study Abroad at Stafford(4pm - 5pm)'
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Thursday, 24th March Events(4pm - 5:30pm)',
            'required' => false,        
            'data' =>  $eventtype2,
        ));
        
       //Eventtype3
        $builder->add('eventtype3','choice',array(
            'choices' => array('5' => 'Study Abroad at Griffith College Dublin(2pm - 3pm)', 
                               '6' => 'Study Abroad at Boston University(2pm - 3pm)',
                               '7' => 'Study Abroad at Ahlia University(2pm - 3pm)',
                               '8' => 'Préparation au choc culturel 3(2pm - 3:30pm)',                           
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Friday, 25th March Events(2pm - 3:30pm)',
            'required' => false,
            'data' => $eventtype3,
        ));
       //Eventtype4
        $builder->add('eventtype4','choice',array(
            'choices' => array('9' => 'Study Abroad at CSU-Channel Islands(4pm - 5pm)', 
                               '10' => 'Study Abroad at Oxford Brookes(4pm - 5pm)',
                               '11' => 'Préparation au choc culturel 4(4pm - 5:30pm)',
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Friday, 25th March Events(4pm - 5:30pm)',
            'required' => false,
            'data' => $eventtype4,
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