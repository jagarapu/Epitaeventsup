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
//           if($this->subscribed->getEventtype4() == null){
//                $eventtype4 = '';   
//           }
//           else {
//               $eventtype4 = $this->subscribed->getEventtype4()->getId();
//           }
//           if($this->subscribed->getEventtype5() == null){
//                $eventtype5 = '';   
//           }
//           else {
//               $eventtype5 = $this->subscribed->getEventtype5()->getId();
//           }
//           if($this->subscribed->getEventtype6() == null){
//                $eventtype6 = '';   
//           }
//           else {
//               $eventtype6 = $this->subscribed->getEventtype6()->getId();
//           }
//           if($this->subscribed->getEventtype7() == null){
//                $eventtype7 = '';   
//           }
//           else {
//               $eventtype7 = $this->subscribed->getEventtype7()->getId();
//           }
////           if($this->subscribed->getEventtype8() == null){
//                $eventtype8 = '';   
//           }
//           else {
//               $eventtype8 = $this->subscribed->getEventtype8()->getId();
//           }
//           if($this->subscribed->getEventtype9() == null){
//                $eventtype9 = '';   
//           }
//           else {
//               $eventtype9 = $this->subscribed->getEventtype9()->getId();
//           }
//           if($this->subscribed->getEventtype10() == null){
//                $eventtype10 = '';   
//           }
//           else {
//               $eventtype10 = $this->subscribed->getEventtype10()->getId();
//           }
       }
       else {
           $eventtype1 = '';
           $eventtype2 = '';
           $eventtype3 = '';
//           $eventtype4 = '';
//           $eventtype5 = '';
//           $eventtype6 = '';
//           $eventtype7 = '';
//           $eventtype8 = '';
//           $eventtype9 = '';
//           $eventtype10 = '';
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
            'choices' => array('3' => ' Food tasting 1 (12:30pm - 1:30pm)', 
                               '4' => ' Food tasting 2 (1:30pm - 2:30pm)'
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Event 2',
            'required' => false,            
            'data' =>  $eventtype2,
        ));
        
       //Eventtype3
        $builder->add('eventtype3','choice',array(
            'choices' => array('5' => 'Holi ', 
                               '6' => ' Kabaddi',
                               '7' => ' Bollywood',
                               '8' => ' African Dance',
                               '9' => '  Cricket',
                               '10' => ' Dabke',
                               '11' => '  Eggs painting',
                ),
            'expanded' => true,
            'multiple' => false,
            'label' => 'Event 3 - (3:00pm – 4:00pm)',
            'required' => false,
            'data' => $eventtype3,
        ));
       //Eventtype4
//        $builder->add('eventtype4','choice',array(
//            'choices' => array('12' => 'Six Hats Challenge(9:00am - 6:00pm)', 
//                               '13' => ' Shark Tank Challenge(9:00am - 6:00pm)',
//                               '14' => ' Effective Project Management in a Hostile Multicultural Environment(9:00am - 6:00pm)',
//                               '15' => ' Designing adverts with Cultural Dimensions in Mind (9:00am – 6:00pm)',
//                               '16' => '   Implement a Factory in Africa(9:00am - 6:00pm)',
//                ),
//            'expanded' => true,
//            'multiple' => false,
//            'label' => 'Wednesday, 15th March Events(9:00am - 6:00pm)',
//            'required' => false,
//            'data' => $eventtype4,
//        ));
        //Eventtype5
//        $builder->add('eventtype5','choice',array(
//            'choices' => array('17' => ' Le Project Management à l’International (10:30am – 12:00pm)', 
//                               '18' => '  L’interculturalité sans risques (10:30am – 12:00pm)',
//                ),
//            'expanded' => true,
//            'multiple' => false,
//            'label' => 'Thursday, 16th March Events(10:30am - 12:00pm)',
//            'required' => false,
//            'data' => $eventtype5,
//        ));
        //Eventtype6
//        $builder->add('eventtype6','choice',array(
//            'choices' => array('19' => '  VIE – Business France (2 :30pm – 4 :00pm)', 
//                               '20' => ' Dual Degree Stevens (3:00pm – 4 :00pm)',
//                               '21' => '   Double diplôme / semestre UQAC (3:00pm – 4 :00pm)',
//                ),
//            'expanded' => true,
//            'multiple' => false,
//            'label' => 'Thursday, 16th March Events()',
//            'required' => false,
//            'data' => $eventtype6,
//        ));
        //Eventtype7
//        $builder->add('eventtype7','choice',array(
//            'choices' => array('22' => '  Le CV à l’international (4:00pm – 5:30pm) ', 
//                               '23' => ' Dual Degree Griffith (4:00pm – 5:00pm)',
//                               '24' => '   Dual Degree Boston (4:00pm – 5:00pm) ',
//                ),
//            'expanded' => true,
//            'multiple' => false,
//            'label' => 'Thursday, 16th March Events(4:00pm – 5:00pm)',
//            'required' => false,
//            'data' => $eventtype7,
//        ));
        //Eventtype8
//        $builder->add('eventtype8','choice',array(
//            'choices' => array('25' => 'Ireland & Griffith College (2:00pm – 3:00pm)  ', 
//                               '26' => 'USA & Boston University (2:00pm – 3:00pm)',
//                ),
//            'expanded' => true,
//            'multiple' => false,
//            'label' => 'Preparatory classes 1:Thursday, 16th March Events(2:00pm – 3:00pm)',
//            'required' => false,
//            'data' => $eventtype8,
//        ));
//        //Eventtype9
//        $builder->add('eventtype9','choice',array(
//            'choices' => array('27' => ' UK & Oxford Brookes University (3:00pm – 4:00pm)  ', 
//                               '28' => 'South Korea & Sejong University (3:00pm – 4:00pm) ',
//                ),
//            'expanded' => true,
//            'multiple' => false,
//            'label' => 'Thursday, 16th March Events(3:00pm – 4:00pm)',
//            'required' => false,
//            'data' => $eventtype9,
//        ));
//        //Eventtype10
//        $builder->add('eventtype10','choice',array(
//            'choices' => array('29' => 'Québec & UQAC (4:00pm – 5:00pm)', 
//                               '30' => 'Turkey & Bahçesehir University (4:00pm – 5:00pm)',
//                ),
//            'expanded' => true,
//            'multiple' => false,
//            'label' => 'Thursday, 16th March Events(4:00pm – 5:00pm)',
//            'required' => false,
//            'data' => $eventtype10,
//        ));
         
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