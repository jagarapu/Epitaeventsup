<?php

namespace Events\Bundle\EventsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Events\Bundle\EventsBundle\Entity\User;
use Events\Bundle\EventsBundle\Form\Type\UserType;
use Symfony\Component\HttpFoundation\Request;
use Events\Bundle\EventsBundle\Entity\Subscribed;
use Events\Bundle\EventsBundle\Form\Type\EventoneType;
use Events\Bundle\EventsBundle\Form\Type\EventtwoType;
use Events\Bundle\EventsBundle\Form\Type\EventthreeType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller {

    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction() {

        // return new RedirectResponse($this->generateUrl('closepage'));
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('securedhome'));
        }
        return array();
    }

    /**
     * @Route("/closepage",name="closepage")
     * @Template()
     */
    public function closeAction() {

        return array();
    }

    /**
     * @Route("/register",name="register")
     * @Template()
     */
    public function registerAction(Request $request) {

      // return new RedirectResponse($this->generateUrl('closepage'));

        $em = $this->getDoctrine()->getManager();
        //Check to see if the user has already logged in
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('securedhome'));
        }

        $user = new User();

        $form = $this->createForm(new UserType(), $user);
        $form->handleRequest($request);
        if ($form->isValid()) {
            //Do the needful
            $date = new \DateTime();
            $user->setCreatedon($date);
            $user->setEnabled(TRUE);
            $em->persist($user);
            $em->flush();
            $this->authenticateUser($user);
            $route = 'securedhome';
            $url = $this->generateUrl($route);
            return $this->redirect($url);
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/secured/home",name="securedhome")
     * @Template()
     */
    public function homeAction(Request $request) {

        // return new RedirectResponse($this->generateUrl('closepage'));

        $em = $this->getDoctrine()->getManager();

        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('events_events_default_index'));
        }
        $user = $em->getRepository('EventsEventsBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());

        if (!is_object($user) || !$user instanceof User) {
            throw new \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException('This user does not have access to this section.');
        }

        return array();
    }

    /**
     * @Route("/secured/eventone",name="eventone")
     * @Template()
     */
    public function eventoneAction(Request $request) {

        $exists = false;

        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('events_events_default_index'));
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('EventsEventsBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());

        if (!is_object($user) || !$user instanceof User) {
            throw new \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException('This user does not have access to this section.');
        }

        $subrecord = $em->getRepository('EventsEventsBundle:Subscribed')->findOneBy(array('user' => $user->getId()));

        $event1 = $event2 = '';
        if (!empty($subrecord)) {
            $exists = true;
            if ($subrecord->getEventtype1() != null || $subrecord->getEventtype1() != '') {
                $event1 = $subrecord->getEventtype1()->getId();
            }
            if (($subrecord->getEventtype2() != null || $subrecord->getEventtype2() != '')) {
                $event2 = $subrecord->getEventtype2()->getId();
            }  
        }

        $subscribed = new Subscribed();
        $form = $this->createForm(new EventoneType($subrecord), $subscribed);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($subscribed->getEventtype1() == null ||
                    $subscribed->getEventtype2() == null 
            ) {
                //User did not choose both the events
                $this->container->get('session')->getFlashBag()->add('error', 'Oh oh! It is mandatory to choose an option for all the events');
                return array('form' => $form->createView());
            }

            $maxculture1 = $this->container->getParameter('max_cultural1');//135
            $maxculture2 = $this->container->getParameter('max_cultural2');//45
            //Now check for the participants limit
            $qb1 = $em->createQueryBuilder();
            $qb1->select('count(subscribed.id)');
            $qb1->from('EventsEventsBundle:Subscribed', 'subscribed');
            $qb1->where('subscribed.eventtype1 = :bar');
            $qb1->setParameter('bar', $subscribed->getEventtype1());

            $total1 = $qb1->getQuery()->getSingleScalarResult();

            if ($exists) {
                //Do count check only if event is different one for already registered users
                if ($event1 != $subscribed->getEventtype1()) {
                    if ($total1 > $maxculture1 || $total1 == $maxculture1) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for selected "Event1(Wednesday, 11th April)". Please choose another time slot for this event');
                        return array('form' => $form->createView());
                    }
                }
            } else {
                if ($total1 > $maxculture1 || $total1 == $maxculture1) {
                    $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected "Event1(Wednesday, 11th April)". Please choose another time slot for this event');
                    return array('form' => $form->createView());
                }
            }

            $qb2 = $em->createQueryBuilder();
            $qb2->select('count(subscribed.id)');
            $qb2->from('EventsEventsBundle:Subscribed', 'subscribed');
            $qb2->where('subscribed.eventtype2 = :bar');
            $qb2->setParameter('bar', $subscribed->getEventtype2());

            $total2 = $qb2->getQuery()->getSingleScalarResult();

            if ($exists) {
                //Do count check only if event is different one for already registered users
                if ($event2 != $subscribed->getEventtype2()) {
                    if ($total2 > $maxculture2 || $total2 == $maxculture2) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected "Event2(Wednesday, 11th April)". Please choose another time slot for this Event');
                        return array('form' => $form->createView());
                    }
                }
            } else {
                if ($total2 > $maxculture2 || $total2 == $maxculture2) {
                    $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected "Event2(Wednesday, 11th April)". Please choose another time slot for this Event');
                    return array('form' => $form->createView());
                }
            }

        }

        if ($form->isValid()) {

            $sub = $em->getRepository('EventsEventsBundle:Subscribed')->findOneBy(array('user' => $user->getId()));
            $eventtype1 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype1()));
            $eventtype2 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype2()));

            if (empty($sub)) {
                $subscribed->setUser($user);
                $subscribed->setEventtype1($eventtype1);
                $subscribed->setEventtype2($eventtype2);
                $em->persist($subscribed);
                $copy = $subscribed;
            } else {
                $sub->setEventtype1($eventtype1);
                $sub->setEventtype2($eventtype2);
                $em->persist($sub);
                $copy = $sub;
            }
            $em->flush();
            $route = 'securedhome';
            $url = $this->generateUrl($route);
            $this->container->get('session')->getFlashBag()->add('success', 'We have your registrations for the events on Wednesday 11th April 2018. Thank you!');
            $message = \Swift_Message::newInstance()
                    ->setSubject('EPITA International - Your Registrations for Wednesday, 11th April 2018')
                    ->setFrom('epitaevents2018@gmail.com')
                    ->setTo($user->getEmailCanonical())
                    ->setContentType("text/html")
                    ->setBody(
                    $this->renderView('EventsEventsBundle:Default:wednesdaymail.html.twig', array('row' => $copy)
            ));
            $this->get('mailer')->send($message);
            return $this->redirect($url);
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/secured/eventtwo",name="eventtwo")
     * @Template()
     */
    public function eventtwoAction(Request $request) {

        $exists = false;

        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('events_events_default_index'));
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('EventsEventsBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());

        if (!is_object($user) || !$user instanceof User) {
            throw new \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException('This user does not have access to this section.');
        }

        $subrecord = $em->getRepository('EventsEventsBundle:Subscribed')->findOneBy(array('user' => $user->getId()));

        $event3 = $event4 = '';
        
        if (!empty($subrecord)) {
            $exists = true;
            if ($subrecord->getEventtype3() != null || $subrecord->getEventtype3() != '') {
                $event3 = $subrecord->getEventtype3()->getId();
            }
            if ($subrecord->getEventtype4() != null || $subrecord->getEventtype4() != '') {
                $event4 = $subrecord->getEventtype4()->getId();
            }
        }

        $subscribed = new Subscribed();
        $form = $this->createForm(new EventtwoType($subrecord), $subscribed);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($subscribed->getEventtype3() == null || $subscribed->getEventtype4() == null) {
                //User did not choose both the events
                $this->container->get('session')->getFlashBag()->add('error', 'Oh oh! It is mandatory to choose an event to attend during the day');
                return array('form' => $form->createView());
            }
            $maxculture3 = $this->container->getParameter('max_cultural3');//135
            $maxculture4 = $this->container->getParameter('max_cultural4');//38
            //Now check for the participants limit
            
            $qb3 = $em->createQueryBuilder();
            $qb3->select('count(subscribed.id)');
            $qb3->from('EventsEventsBundle:Subscribed', 'subscribed');
            $qb3->where('subscribed.eventtype3 = :bar');
            $qb3->setParameter('bar', $subscribed->getEventtype3());

            $total3 = $qb3->getQuery()->getSingleScalarResult();

            if ($exists) {
                //Do count check only if event is different one for already registered users
                if ($event3 != $subscribed->getEventtype3()) {
                    if ($total3 > $maxculture3 || $total3 == $maxculture3) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected "Event1(Thursday, 12th April)". Please choose another time slot for this Event'
                                . '');
                        return array('form' => $form->createView());
                    }
                }
            } else {
                if ($total3 > $maxculture3 || $total3 == $maxculture3) {
                    $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected "Event1(Thursday, 12th April)". Please choose another time slot for this Event');
                    return array('form' => $form->createView());
                }
            }
                      
            $qb4 = $em->createQueryBuilder();
            $qb4->select('count(subscribed.id)');
            $qb4->from('EventsEventsBundle:Subscribed', 'subscribed');
            $qb4->where('subscribed.eventtype4 = :bar');
            $qb4->setParameter('bar', $subscribed->getEventtype4());

            $total4 = $qb4->getQuery()->getSingleScalarResult();

            if ($exists) {
                //Do count check only if event is different one for already registered users
                if ($event4 != $subscribed->getEventtype4()) {
                    if ($total4 > $maxculture4 || $total4 == $maxculture4) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected "Event2(Thursday, 12th April)". Please choose another time slot for this Event');
                        return array('form' => $form->createView());
                    }
                }
            } else {
                if ($total4 > $maxculture4 || $total4 == $maxculture4) {
                    $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected "Event2(Thursday, 12th April)". Please choose another time slot for this Event');
                    return array('form' => $form->createView());
                }
            }
        }

        if ($form->isValid()) {

            $sub = $em->getRepository('EventsEventsBundle:Subscribed')->findOneBy(array('user' => $user->getId()));
            $eventtype3 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype3()));
            $eventtype4 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype4()));


            if (empty($sub)) {
                $subscribed->setUser($user);
                $subscribed->setEventtype3($eventtype3);
                $subscribed->setEventtype4($eventtype4);
                $em->persist($subscribed);
                $copy = $subscribed;
            } else {
                $sub->setEventtype3($eventtype3);
                $sub->setEventtype4($eventtype4);
                $em->persist($sub);
                $copy = $sub;
            }
            $em->flush();
            $route = 'securedhome';
            $url = $this->generateUrl($route);
            $this->container->get('session')->getFlashBag()->add('success', 'We have your registrations for the events on Thursday, 12th April 2018 . Thank you!');
            $message = \Swift_Message::newInstance()
                    ->setSubject('EPITA International - Your Registrations for Thursday, 12th April 2018')
                    ->setFrom('epitaevents2018@gmail.com')
                    ->setTo($user->getEmailCanonical())
                    ->setContentType("text/html")
                    ->setBody(
                    $this->renderView('EventsEventsBundle:Default:thursdaymail.html.twig', array('row' => $copy)
            ));
            $this->get('mailer')->send($message);
            return $this->redirect($url);
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/secured/eventthree",name="eventthree")
     * @Template()
     */
    public function eventthreeAction(Request $request) {

        $exists = false;

        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('events_events_default_index'));
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('EventsEventsBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());

        if (!is_object($user) || !$user instanceof User) {
            throw new \Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException('This user does not have access to this section.');
        }

        $subrecord = $em->getRepository('EventsEventsBundle:Subscribed')->findOneBy(array('user' => $user->getId()));

        $event5 = $event6 = '';
//        $event7 = '';
        if (!empty($subrecord)) {
            $exists = true;
            if ($subrecord->getEventtype5() != null || $subrecord->getEventtype5() != '') {
                $event5 = $subrecord->getEventtype5()->getId();
            }
            if (($subrecord->getEventtype6() != null || $subrecord->getEventtype6() != '')) {
                $event6 = $subrecord->getEventtype6()->getId();
            }
//            if (($subrecord->getEventtype7() != null || $subrecord->getEventtype7() != '')) {
//                $event7 = $subrecord->getEventtype7()->getId();
//            }
        }

        $subscribed = new Subscribed();
        $form = $this->createForm(new EventthreeType($subrecord), $subscribed);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($subscribed->getEventtype5() == null ||
                    $subscribed->getEventtype6() == null 
//                    ||$subscribed->getEventtype7() == null
            ) {
                //User did not choose both the events
                $this->container->get('session')->getFlashBag()->add('error', 'Oh oh! It is mandatory to choose an option for all the events');
                return array('form' => $form->createView());
            }

            $maxculture5 = $this->container->getParameter('max_cultural5'); //90
            $maxculture6 = $this->container->getParameter('max_vie_cv'); //90
//            $maxculture7 = $this->container->getParameter('max_dual'); //24
            //Now check for the participants limit
            $qb5 = $em->createQueryBuilder();
            $qb5->select('count(subscribed.id)');
            $qb5->from('EventsEventsBundle:Subscribed', 'subscribed');
            $qb5->where('subscribed.eventtype5 = :bar');
            $qb5->setParameter('bar', $subscribed->getEventtype5());

            $total5 = $qb5->getQuery()->getSingleScalarResult();

            if ($exists) {
                //Do count check only if event is different one for already registered users
                if ($event5 != $subscribed->getEventtype5()) {
                    if ($total5 > $maxculture5 || $total5 == $maxculture5) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the "Event 1(Friday, 13th April)". Please choose another event for same time slot');
                        return array('form' => $form->createView());
                    }
                }
            } else {
                if ($total5 > $maxculture5 || $total5 == $maxculture5) {
                    $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the "Event 1(Friday, 13th April)". Please choose another event for same time slot');
                    return array('form' => $form->createView());
                }
            }

            //Now check for the participants limit
            $qb6 = $em->createQueryBuilder();
            $qb6->select('count(subscribed.id)');
            $qb6->from('EventsEventsBundle:Subscribed', 'subscribed');
            $qb6->where('subscribed.eventtype6 = :bar');
            $qb6->setParameter('bar', $subscribed->getEventtype6());

            $total6 = $qb6->getQuery()->getSingleScalarResult();

            if ($exists) {
                if ($event6 != $subscribed->getEventtype6()) {
                    if ($total6 > $maxculture6 || $total6 == $maxculture6) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the "Event 2(Friday, 13th April)". Please choose another event for same time slot');
                        return array('form' => $form->createView());
                    }
                }
            } else {
                if ($total6 > $maxculture6 || $total6 == $maxculture6) {
                    $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the "Event 2(Friday, 13th April)". Please choose another event for same time slot');
                    return array('form' => $form->createView());
                }
            }

            
            //Now check for the participants limit
//            $qb7 = $em->createQueryBuilder();
//            $qb7->select('count(subscribed.id)');
//            $qb7->from('EventsEventsBundle:Subscribed', 'subscribed');
//            $qb7->where('subscribed.eventtype7 = :bar');
//            $qb7->setParameter('bar', $subscribed->getEventtype7());

//            $total7 = $qb7->getQuery()->getSingleScalarResult();

//            if ($exists) {
//                if ($event7 != $subscribed->getEventtype7()) {
//                    if ($total7 > $maxculture7 || $total7 == $maxculture7) {
//                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the "Event 3(02:00 am – 06:00 am)". Please choose another event for same time slot');
//                        return array('form' => $form->createView());
//                    }
//                }
//            } else {
//                if ($total7 > $maxculture7 || $total7 == $maxculture7) {
//                    $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the "Event 3(02:00 am – 06:00 am)". Please choose another event for same time slot');
//                    return array('form' => $form->createView());
//                }
//            }
        }

        if ($form->isValid()) {

            $sub = $em->getRepository('EventsEventsBundle:Subscribed')->findOneBy(array('user' => $user->getId()));
            $eventtype5 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype5()));
            $eventtype6 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype6()));
//            $eventtype7 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype7()));

            if (empty($sub)) {
                $subscribed->setUser($user);
                $subscribed->setEventtype5($eventtype5);
                $subscribed->setEventtype6($eventtype6);
//                $subscribed->setEventtype7($eventtype7);
                $em->persist($subscribed);
                $copy = $subscribed;
            } else {
                $sub->setEventtype5($eventtype5);
                $sub->setEventtype6($eventtype6);
//                $sub->setEventtype7($eventtype7);
                $em->persist($sub);
                $copy = $sub;
            }
            $em->flush();
            $route = 'securedhome';
            $url = $this->generateUrl($route);
            $this->container->get('session')->getFlashBag()->add('success', 'We have your registrations for the events on Friday, 13th April 2018. Thank you!');
            $message = \Swift_Message::newInstance()
                    ->setSubject('EPITA International - Your Registrations for Friday, 13th April 2018')
                    ->setFrom('epitaevents2018@gmail.com')
                    ->setTo($user->getEmailCanonical())
                    ->setContentType("text/html")
                    ->setBody(
                    $this->renderView('EventsEventsBundle:Default:fridaymail.html.twig', array('row' => $copy)
            ));
            $this->get('mailer')->send($message);
            return $this->redirect($url);
        }

        return array('form' => $form->createView());
    }
        
    /**
     * Authenticate the user
     * 
     * @param FOS\UserBundle\Model\UserInterface
     */
    protected function authenticateUser(User $user) {  
        try {  
            $this->container->get('security.user_checker')->checkPostAuth($user); 
        } catch (AccountStatusException $e) {
            // Don't authenticate locked, disabled or expired users
            return;
        }

        $providerKey = $this->container->getParameter('fos_user.firewall_name');
        $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $this->container->get('security.context')->setToken($token);
    }
    
    
   /**
     *
     * @Route("/export/tuesday",name="exporttue")
     *      
     */
    public function exporttueAction() {
        $format = 'xls';
        $filename = sprintf('export_students_ing2_tuesday.%s', $format);
        $data = array();
        $em = $this->getDoctrine()->getEntityManager();
        $query = $em->createQuery('SELECT s FROM Events\Bundle\EventsBundle\Entity\Subscribed s');
        $data = $query->getResult();
        $content = $this->renderView('EventsEventsBundle:Default:tuesday.html.twig', array('data' => $data));
        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        return $response;
    }
     /**
     *
     * @Route("/export/wednesday",name="exportwed")
     *      
     */
    public function exportwedAction() {
        $format = 'xls';
        $filename = sprintf('export_students_ing2_wednesday.%s', $format);
        $data = array();
        $em = $this->getDoctrine()->getEntityManager();
        $query = $em->createQuery('SELECT s FROM Events\Bundle\EventsBundle\Entity\Subscribed s');
        $data = $query->getResult();
        $content = $this->renderView('EventsEventsBundle:Default:wednesday.html.twig', array('data' => $data));
        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        return $response;
    }
    /**
     *
     * @Route("/export/thursday",name="exportthu")
     *      
     */
    public function exportthuAction() {
        $format = 'xls';
        $filename = sprintf('export_students_ing2_thursday.%s', $format);
        $data = array();
        $em = $this->getDoctrine()->getEntityManager();
        $query = $em->createQuery('SELECT s FROM Events\Bundle\EventsBundle\Entity\Subscribed s');
        $data = $query->getResult();
        $content = $this->renderView('EventsEventsBundle:Default:thursday.html.twig', array('data' => $data));
        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        return $response;
    }
}
