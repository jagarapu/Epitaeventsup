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

        //return new RedirectResponse($this->generateUrl('closepage'));

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

        $event1 = $event2 = $event3 = '';
        if (!empty($subrecord)) {
            $exists = true;
            if ($subrecord->getEventtype1() != null || $subrecord->getEventtype1() != '') {
                $event1 = $subrecord->getEventtype1()->getId();
            }
            if (($subrecord->getEventtype2() != null || $subrecord->getEventtype2() != '')) {
                $event2 = $subrecord->getEventtype2()->getId();
            }
            if (($subrecord->getEventtype3() != null || $subrecord->getEventtype3() != '')) {
                $event3 = $subrecord->getEventtype3()->getId();
            }
        }

        $subscribed = new Subscribed();
        $form = $this->createForm(new EventoneType($subrecord), $subscribed);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($subscribed->getEventtype1() == null ||
                    $subscribed->getEventtype2() == null ||
                    $subscribed->getEventtype3() == null
            ) {
                //User did not choose both the events
                $this->container->get('session')->getFlashBag()->add('error', 'Oh oh! It is mandatory to choose an option for all the events');
                return array('form' => $form->createView());
            }

            $maxculture1 = $this->container->getParameter('max_cultural1');
            $maxculture2 = $this->container->getParameter('max_cultural2');
            $maxculture3 = $this->container->getParameter('max_cultural3');
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
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the "Expatriation & Intercultural Management event". Please choose another time slot for this event');
                        return array('form' => $form->createView());
                    }
                }
            } else {
                if ($total1 > $maxculture1 || $total1 == $maxculture1) {
                    $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the "Expatriation & Intercultural Management event". Please choose another time slot for this event');
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
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected "Food tasting Event". Please choose another time slot for this Event');
                        return array('form' => $form->createView());
                    }
                }
            } else {
                if ($total2 > $maxculture2 || $total2 == $maxculture2) {
                    $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected "Food tasting Event". Please choose another time slot for this Event');
                    return array('form' => $form->createView());
                }
            }

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
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected "cultural activity". Please choose another event for time 3-4pm'
                                . '');
                        return array('form' => $form->createView());
                    }
                }
            } else {
                if ($total3 > $maxculture3 || $total3 == $maxculture3) {
                    $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected "cultural activity". Please choose another event for time 3-4pm');
                    return array('form' => $form->createView());
                }
            }
        }

        if ($form->isValid()) {

            $sub = $em->getRepository('EventsEventsBundle:Subscribed')->findOneBy(array('user' => $user->getId()));
            $eventtype1 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype1()));
            $eventtype2 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype2()));
            $eventtype3 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype3()));

            if (empty($sub)) {
                $subscribed->setUser($user);
                $subscribed->setEventtype1($eventtype1);
                $subscribed->setEventtype2($eventtype2);
                $subscribed->setEventtype3($eventtype3);
                $em->persist($subscribed);
                $copy = $subscribed;
            } else {
                $sub->setEventtype1($eventtype1);
                $sub->setEventtype2($eventtype2);
                $sub->setEventtype3($eventtype3);
                $em->persist($sub);
                $copy = $sub;
            }
            $em->flush();
            $route = 'securedhome';
            $url = $this->generateUrl($route);
            $this->container->get('session')->getFlashBag()->add('success', 'We have your registrations for the events on Tuesday 14th March 2017. Thank you!');
            $message = \Swift_Message::newInstance()
                    ->setSubject('EPITA International - Your Registrations for Tuesday, 14th March 2017')
                    ->setFrom('epitaevents2017@gmail.com')
                    ->setTo($user->getEmailCanonical())
                    ->setContentType("text/html")
                    ->setBody(
                    $this->renderView('EventsEventsBundle:Default:tuesdaymail.html.twig', array('row' => $copy)
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

        $event4 = '';
        if (!empty($subrecord)) {
            $exists = true;
            if ($subrecord->getEventtype4() != null || $subrecord->getEventtype4() != '') {
                $event4 = $subrecord->getEventtype4()->getId();
            }
        }

        $subscribed = new Subscribed();
        $form = $this->createForm(new EventtwoType($subrecord), $subscribed);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($subscribed->getEventtype4() == null) {
                //User did not choose both the events
                $this->container->get('session')->getFlashBag()->add('error', 'Oh oh! It is mandatory to choose an event to attend during the day');
                return array('form' => $form->createView());
            }

            $maxculture4 = $this->container->getParameter('max_cultural4');
            //Now check for the participants limit
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
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the "selected event". Please choose another event');
                        return array('form' => $form->createView());
                    }
                }
            } else {
                if ($total4 > $maxculture4 || $total4 == $maxculture4) {
                    $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the "selected event". Please choose another event');
                    return array('form' => $form->createView());
                }
            }
        }

        if ($form->isValid()) {

            $sub = $em->getRepository('EventsEventsBundle:Subscribed')->findOneBy(array('user' => $user->getId()));
            $eventtype4 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype4()));


            if (empty($sub)) {
                $subscribed->setUser($user);
                $subscribed->setEventtype4($eventtype4);
                $em->persist($subscribed);
                $copy = $subscribed;
            } else {
                $sub->setEventtype4($eventtype4);
                $em->persist($sub);
                $copy = $sub;
            }
            $em->flush();
            $route = 'securedhome';
            $url = $this->generateUrl($route);
            $this->container->get('session')->getFlashBag()->add('success', 'We have your registrations for the events on Wednesday, 15th March 2017 . Thank you!');
            $message = \Swift_Message::newInstance()
                    ->setSubject('EPITA International - Your Registrations for Wednesday, 15th March 2017')
                    ->setFrom('epitaevents2017@gmail.com')
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

        $event5 = $event6 = $event7 = '';
        if (!empty($subrecord)) {
            $exists = true;
            if ($subrecord->getEventtype5() != null || $subrecord->getEventtype5() != '') {
                $event5 = $subrecord->getEventtype5()->getId();
            }
            if (($subrecord->getEventtype6() != null || $subrecord->getEventtype6() != '')) {
                $event6 = $subrecord->getEventtype6()->getId();
            }
            if (($subrecord->getEventtype7() != null || $subrecord->getEventtype7() != '')) {
                $event7 = $subrecord->getEventtype7()->getId();
            }
        }

        $subscribed = new Subscribed();
        $form = $this->createForm(new EventthreeType($subrecord), $subscribed);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($subscribed->getEventtype5() == null ||
                    $subscribed->getEventtype6() == null ||
                    $subscribed->getEventtype7() == null
            ) {
                //User did not choose both the events
                $this->container->get('session')->getFlashBag()->add('error', 'Oh oh! It is mandatory to choose an option for all the events');
                return array('form' => $form->createView());
            }

            $maxculture5 = $this->container->getParameter('max_cultural5'); //110
            $maxviecv = $this->container->getParameter('max_vie_cv'); //100
            $maxdual = $this->container->getParameter('max_dual'); //60
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
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the "selected event". Please choose another event for same time slot');
                        return array('form' => $form->createView());
                    }
                }
            } else {
                if ($total5 > $maxculture5 || $total5 == $maxculture5) {
                    $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the "Event 1(10:30 - 12:00)". Please choose another event for same time slot');
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
                if ($subscribed->getEventtype6() == 19) {
                    //Do cultural shock 1 count
                    if ($event6 != $subscribed->getEventtype6()) {
                        if ($total6 > $maxviecv || $total6 == $maxviecv) {
                            $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for "selected event". Please choose another Event');
                            return array('form' => $form->createView());
                        }
                    }
                } else if ($subscribed->getEventtype6() == 20 || $subscribed->getEventtype6() == 21) {
                    //Do university check
                    if ($event6 != $subscribed->getEventtype6()) {
                        if ($total6 > $maxdual || $total6 == $maxdual) {
                            $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for "Event 2". Please choose another Event');
                            return array('form' => $form->createView());
                        }
                    }
                }
            } else {
                if ($subscribed->getEventtype6() == 19) {
                    //Cultural Schock
                    if ($total6 > $maxviecv || $total6 == $maxviecv) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for "selected event". Please choose another Event');
                        return array('form' => $form->createView());
                    }
                } else if ($subscribed->getEventtype6() == 20 || $subscribed->getEventtype6() == 21) {
                    //university event
                    if ($total6 > $maxdual || $total6 == $maxdual) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for "Event 2". Please choose another Event');
                        return array('form' => $form->createView());
                    }
                }
            }


            //Now check for the participants limit
            $qb7 = $em->createQueryBuilder();
            $qb7->select('count(subscribed.id)');
            $qb7->from('EventsEventsBundle:Subscribed', 'subscribed');
            $qb7->where('subscribed.eventtype7 = :bar');
            $qb7->setParameter('bar', $subscribed->getEventtype7());

            $total7 = $qb7->getQuery()->getSingleScalarResult();

            if ($exists) {
                if ($subscribed->getEventtype7() == 22) {
                    //Do cultural shock 1 count
                    if ($event7 != $subscribed->getEventtype7()) {
                        if ($total7 > $maxviecv || $total7 == $maxviecv) {
                            $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for "selected event". Please choose another Event');
                            return array('form' => $form->createView());
                        }
                    }
                } else if ($subscribed->getEventtype7() == 23 || $subscribed->getEventtype7() == 24) {
                    //Do university check
                    if ($event7 != $subscribed->getEventtype7()) {
                        if ($total7 > $maxdual || $total7 == $maxdual) {
                            $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for "Event 3". Please choose another Event');
                            return array('form' => $form->createView());
                        }
                    }
                }
            } else {
                if ($subscribed->getEventtype7() == 22) {
                    //Cultural Schock
                    if ($total7 > $maxviecv || $total7 == $maxviecv) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for "selected event". Please choose another Event');
                        return array('form' => $form->createView());
                    }
                } else if ($subscribed->getEventtype7() == 23 || $subscribed->getEventtype7() == 24) {
                    //university event
                    if ($total7 > $maxdual || $total7 == $maxdual) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for "Event 3". Please choose another Event');
                        return array('form' => $form->createView());
                    }
                }
            }
        }

        if ($form->isValid()) {

            $sub = $em->getRepository('EventsEventsBundle:Subscribed')->findOneBy(array('user' => $user->getId()));
            $eventtype5 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype5()));
            $eventtype6 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype6()));
            $eventtype7 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype7()));

            if (empty($sub)) {
                $subscribed->setUser($user);
                $subscribed->setEventtype5($eventtype5);
                $subscribed->setEventtype6($eventtype6);
                $subscribed->setEventtype7($eventtype7);
                $em->persist($subscribed);
                $copy = $subscribed;
            } else {
                $sub->setEventtype5($eventtype5);
                $sub->setEventtype6($eventtype6);
                $sub->setEventtype7($eventtype7);
                $em->persist($sub);
                $copy = $sub;
            }
            $em->flush();
            $route = 'securedhome';
            $url = $this->generateUrl($route);
            $this->container->get('session')->getFlashBag()->add('success', 'We have your registrations for the events on Thursday, 16th March 2017. Thank you!');
            $message = \Swift_Message::newInstance()
                    ->setSubject('EPITA International - Your Registrations for Thursday, 16th March 2017')
                    ->setFrom('epitaevents2017@gmail.com')
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

}
