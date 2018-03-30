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
            'choices' => array('18' => "Doing business in the countries of V4 & EU (09:30 am – 11:00 am)",
                               '19' => 'Travailler aux/avec les USA (09:30 am – 11:00 am)', 
                               '20' => 'L’interculturalité sans risque (09:30 am – 11:00 am)',
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Event 1 (Friday, 13th April)',
            'required' => true,
            'data' => $eventtype5,
        ));
        //Eventtype6
        $builder->add('eventtype6','choice',array(
            'choices' => array('21' => 'Intégration professionnelle dans un monde globalisé (11:00 am – 12:30 am)',
                               '22' => 'Travailler en/avec la Chine (11:00 am – 12:30 am)', 
                               '23' => 'Le CV à l’international (11:00 am – 12:30 am)',
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Event 1 (Friday, 13th April)',
            'required' => true,
            'data' => $eventtype6,
        ));
        //Eventtype7
//        $builder->add('eventtype7','choice',array(
//            'choices' => array('24' => 'Atelier relecture de CV (02:00 am – 06:00 am)',
//                ),
//            'expanded' => true,
//            'multiple' => false,
//            'label' => 'Friday, 13th April Event 3',
//            'required' => false,
//            'data' => $eventtype7,
//        ));
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