<?php

namespace Events\Bundle\EventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Events\Bundle\EventsBundle\Entity\Subscribed;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventthreeType extends AbstractType {
    
    protected $subscribed;
    
    public function __construct($subscribed){
        
        $this->subscribed = $subscribed;
       
    }
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
       if (!empty($this->subscribed)){
           if($this->subscribed->getEventtype5() == null){
                $eventtype5 = '';   
           }
           else {
               $eventtype5 = $this->subscribed->getEventtype5()->getId();
           }
           if($this->subscribed->getEventtype6() == null){
                $eventtype6 = '';   
           }
           else {
               $eventtype6 = $this->subscribed->getEventtype6()->getId();
           }
           if($this->subscribed->getEventtype7() == null){
                $eventtype7 = '';   
           }
           else {
               $eventtype7 = $this->subscribed->getEventtype7()->getId();
           }
       }
       else {
           $eventtype5 = '';
           $eventtype6 = '';
           $eventtype7 = '';
       }
        //Eventtype5
        $builder->add('eventtype5','choice',array(
            'choices' => array('17' => 'L’interculturalité sans risques (10:30am - 12pm)', 
                               '18' => "Le CV à l’international (10:30am - 12pm)",
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Thursday, 16th March Event 1',
            'required' => false,
            'data' => $eventtype5,
        ));
        //Eventtype6
        $builder->add('eventtype6','choice',array(
            'choices' => array('19' => 'VIE – Business France (2 :30pm – 4pm)', 
                               '20' => 'Dual Degree Stevens (3pm – 4pm)',
                               '21' => 'Double diplôme / semestre UQAC (3pm – 4pm)',
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Thursday, 16th March Event 2',
            'required' => false,
            'data' => $eventtype6,
        ));
        //Eventtype7
        $builder->add('eventtype7','choice',array(
            'choices' => array('22' => 'Le Project Management à l’international (4pm – 5:30pm) ', 
                               '23' => 'Dual Degree Griffith (4pm – 5pm)',
                               '24' => 'Dual Degree Boston (4pm – 5pm) ',
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Thursday, 16th March Event 3',
            'required' => false,
            'data' => $eventtype7,
        ));
 }

    public function getDefaultOptions(array $options) {
        return array('csrf_protection' => false);
    }

    public function getName() {
        return 'eventthree';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
         $resolver->setDefaults(array(
            'data_class' => 'Events\Bundle\EventsBundle\Entity\Subscribed',
        ));
    }
}