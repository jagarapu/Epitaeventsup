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
use Symfony\Component\Filesystem\Filesystem;

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

        if (!empty($subrecord)) {
            $exists = true;
            if ($subrecord->getEventtype1() != null || $subrecord->getEventtype1() != '') {
                $event1 = $subrecord->getEventtype1()->getId();
            } else {
                $event1 = '';
            }
            if (($subrecord->getEventtype2() != null || $subrecord->getEventtype2() != '')) {
                $event2 = $subrecord->getEventtype2()->getId();
            } else {
                $event2 = '';
            }
            if (($subrecord->getEventtype3() != null || $subrecord->getEventtype3() != '')) {
                $event3 = $subrecord->getEventtype3()->getId();
            } else {
                $event3 = '';
            }
            if (($subrecord->getEventtype4() != null || $subrecord->getEventtype4() != '')) {
                $event4 = $subrecord->getEventtype4()->getId();
            } else {
                $event4 = '';
            }
        }

        $subscribed = new Subscribed();

        $form = $this->createForm(new EventoneType($subrecord), $subscribed);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //First check the value entered by the user
            if ($subscribed->getEventtype1() == 1 ||
                    $subscribed->getEventtype2() == 3 ||
                    $subscribed->getEventtype3() == 8 ||
                    $subscribed->getEventtype4() == 11
            ) {
                if (($subscribed->getEventtype1() == 1 && $subscribed->getEventtype2() == 3) ||
                        ($subscribed->getEventtype1() == 1 && $subscribed->getEventtype3() == 8) ||
                        ($subscribed->getEventtype1() == 1 && $subscribed->getEventtype4() == 11) ||
                        ($subscribed->getEventtype2() == 3 && $subscribed->getEventtype3() == 8) ||
                        ($subscribed->getEventtype2() == 3 && $subscribed->getEventtype4() == 11) ||
                        ($subscribed->getEventtype3() == 8 && $subscribed->getEventtype4() == 11)) {
                    $this->container->get('session')->getFlashBag()->add('error', 'You can select to attend ONLY ONE Cultural Shock session"');
                    return array('form' => $form->createView());
                }
            } else {
                //User must select atleast 1 Cultural Shock Session
                //User did not choose both the events
                $this->container->get('session')->getFlashBag()->add('error', 'You must choose ANY ONE Cultural shock session"');
                return array('form' => $form->createView());
            }

            //Identical events should not be selected
            if ($subscribed->getEventtype1() == 2 ||
                    $subscribed->getEventtype2() == 4 ||
                    $subscribed->getEventtype3() == 5 ||
                    $subscribed->getEventtype3() == 6 ||
                    $subscribed->getEventtype3() == 7 ||
                    $subscribed->getEventtype4() == 9 ||
                    $subscribed->getEventtype4() == 10
            ) {
                //Check for more than 2 options selected by user
                if (($subscribed->getEventtype1() != null &&
                        $subscribed->getEventtype2() != null &&
                        $subscribed->getEventtype3() != null ) ||
                        ($subscribed->getEventtype1() != null &&
                        $subscribed->getEventtype3() != null &&
                        $subscribed->getEventtype4() != null ) ||
                        ($subscribed->getEventtype2() != null &&
                        $subscribed->getEventtype3() != null &&
                        $subscribed->getEventtype4() != null )
                ) {
                    $this->container->get('session')->getFlashBag()->add('error', 'You must choose to attend only two events .ANY ONE University presentation and ANY ONE Cultural Schock Session');
                    return array('form' => $form->createView());
                }
            } else {
                //User chose identical events
                $this->container->get('session')->getFlashBag()->add('error', 'You must choose to attend ANY ONE University presentation');
                return array('form' => $form->createView());
            }


            $maxculture1 = $this->container->getParameter('max_cultural1');
            $maxculture2 = $this->container->getParameter('max_cultural2');
            $maxuniversity = $this->container->getParameter('max_university');
            //Now check for the participants limit
            $qb1 = $em->createQueryBuilder();
            $qb1->select('count(subscribed.id)');
            $qb1->from('EventsEventsBundle:Subscribed', 'subscribed');
            $qb1->where('subscribed.eventtype1 = :bar');
            $qb1->setParameter('bar', $subscribed->getEventtype1());

            $total1 = $qb1->getQuery()->getSingleScalarResult();

            if ($exists) {
                if ($subscribed->getEventtype1() == 1) {
                    //Do cultural shock 1 count
                    if ($event1 != $subscribed->getEventtype1()) {
                        if ($total1 > $maxculture1 || $total1 == $maxculture1) {
                            $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for this Cultural Shock Event. Please choose another time slot for the Culture Schock Event');
                            return array('form' => $form->createView());
                        }
                    }
                } else if ($subscribed->getEventtype1() == 2) {
                    //Do university check
                    if ($event1 != $subscribed->getEventtype1()) {
                        if ($total1 > $maxuniversity || $total1 == $maxuniversity) {
                            $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for this University Presentation. Please choose another University Presentation Event');
                            return array('form' => $form->createView());
                        }
                    }
                }
            } else {
                if ($subscribed->getEventtype1() == 1) {
                    //Cultural Schock
                    if ($total1 > $maxculture1 || $total1 == $maxculture1) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for this Cultural Shock Event. Please choose another time slot for the Culture Schock Event');
                        return array('form' => $form->createView());
                    }
                } else if ($subscribed->getEventtype1() == 2) {
                    //university event
                    if ($total1 > $maxuniversity || $total1 == $maxuniversity) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for this University Presentation. Please choose another University Presentation Event');
                        return array('form' => $form->createView());
                    }
                }
            }

            $qb2 = $em->createQueryBuilder();
            $qb2->select('count(subscribed.id)');
            $qb2->from('EventsEventsBundle:Subscribed', 'subscribed');
            $qb2->where('subscribed.eventtype2 = :bar');
            $qb2->setParameter('bar', $subscribed->getEventtype2());

            $total2 = $qb2->getQuery()->getSingleScalarResult();
            if ($exists) {
                if ($subscribed->getEventtype2() == 3) {
                    //Culture Schock Event
                    if ($event2 != $subscribed->getEventtype2()) {
                        if ($total2 > $maxculture1 || $total2 == $maxculture1) {
                            $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for this Cultural Shock Event. Please choose another time slot for the Culture Schock Event');
                            return array('form' => $form->createView());
                        }
                    }
                } else if ($subscribed->getEventtype2() == 4) {
                    //Univeristy Event
                    if ($event2 != $subscribed->getEventtype2()) {
                        if ($total2 > $maxuniversity || $total2 == $maxuniversity) {
                            $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for this University Presentation. Please choose another');
                            return array('form' => $form->createView());
                        }
                    }
                }
            } else {
                if ($subscribed->getEventtype2() == 3) {
                    //Culture Shock
                    if ($total2 > $maxculture1 || $total2 == $maxculture1) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected Cultural Schock Session. Please choose another Cultural Shock event');
                        return array('form' => $form->createView());
                    }
                } else if ($subscribed->getEventtype2() == 4) {
                    //University
                    if ($total2 > $maxuniversity || $total2 == $maxuniversity) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected University Presentation. Please choose another University Presentation event');
                        return array('form' => $form->createView());
                    }
                }
            }

            $qb3 = $em->createQueryBuilder();
            $qb3->select('count(subscribed.id)');
            $qb3->from('EventsEventsBundle:Subscribed', 'subscribed');
            $qb3->where('subscribed.eventtype3 = :bar');
            $qb3->setParameter('bar', $subscribed->getEventtype3());

            $total3 = $qb3->getQuery()->getSingleScalarResult();

            if ($exists) {
                if ($subscribed->getEventtype3() == 8) {
                    //Culture Shock
                    if ($event3 != $subscribed->getEventtype3()) {
                        if ($total3 > $maxculture2 || $total3 == $maxculture2) {
                            $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected Cultural Shock Event.Please choose another Cultural Shock event');
                            return array('form' => $form->createView());
                        }
                    }
                } else {
                    //Other university events
                    if ($event3 != $subscribed->getEventtype3()) {
                        if ($total3 > $maxuniversity || $total3 == $maxuniversity) {
                            $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected University Presentation Event.Please choose another event');
                            return array('form' => $form->createView());
                        }
                    }
                }
            } else {

                if ($subscribed->getEventtype3() == 8) {
                    //Culture Shock
                    if ($total3 > $maxculture2 || $total3 == $maxculture2) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected Cultural Shock Event.Please choose another Cultural Shock event');
                        return array('form' => $form->createView());
                    }
                } else {
                    //University
                    if ($total3 > $maxuniversity || $total3 == $maxuniversity) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected University Presentation Event.Please choose another University Presentation');
                        return array('form' => $form->createView());
                    }
                }
            }
            
            $qb4 = $em->createQueryBuilder();
            $qb4->select('count(subscribed.id)');
            $qb4->from('EventsEventsBundle:Subscribed', 'subscribed');
            $qb4->where('subscribed.eventtype4 = :bar');
            $qb4->setParameter('bar', $subscribed->getEventtype4());

            $total4 = $qb4->getQuery()->getSingleScalarResult();

            if ($exists) {
                if ($subscribed->getEventtype4() == 11) {
                    //Culture Shock
                    if ($event4 != $subscribed->getEventtype4()) {
                        if ($total4 > $maxculture2 || $total4 == $maxculture2) {
                            $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected Cultural Shock Event.Please choose another Cultural Shock event');
                            return array('form' => $form->createView());
                        }
                    }
                } else {
                    //Other university events
                    if ($event4 != $subscribed->getEventtype4()) {
                        if ($total4 > $maxuniversity || $total4 == $maxuniversity) {
                            $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected University Presentation Event.Please choose another event');
                            return array('form' => $form->createView());
                        }
                    }
                }
            } else {

                if ($subscribed->getEventtype4() == 11) {
                    //Culture Shock
                    if ($total4 > $maxculture2 || $total4 == $maxculture2) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected Cultural Shock Event.Please choose another Cultural Shock event');
                        return array('form' => $form->createView());
                    }
                } else {
                    //University
                    if ($total4 > $maxuniversity || $total4 == $maxuniversity) {
                        $this->container->get('session')->getFlashBag()->add('error', 'The registrations are full for the selected University Presentation Event.Please choose another University Presentation');
                        return array('form' => $form->createView());
                    }
                }
            }
        }


        if ($form->isValid()) {

            $sub = $em->getRepository('EventsEventsBundle:Subscribed')->findOneBy(array('user' => $user->getId()));
            $eventtype1 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype1()));
            $eventtype2 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype2()));
            $eventtype3 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype3()));
            $eventtype4 = $em->getRepository('EventsEventsBundle:Eventtype')->findOneBy(array('id' => $subscribed->getEventtype4()));

            if (empty($sub)) {
                $subscribed->setUser($user);
                $subscribed->setEventtype1($eventtype1);
                $subscribed->setEventtype2($eventtype2);
                $subscribed->setEventtype3($eventtype3);
                $subscribed->setEventtype4($eventtype4);
                $em->persist($subscribed);
                $copy = $subscribed;
            } else {
                $sub->setEventtype1($eventtype1);
                $sub->setEventtype2($eventtype2);
                $sub->setEventtype3($eventtype3);
                $sub->setEventtype4($eventtype4);
                $em->persist($sub);
                $copy = $sub;
            }
            $em->flush();
            $route = 'securedhome';
            $url = $this->generateUrl($route);
            $this->container->get('session')->getFlashBag()->add('success', 'We have your registrations for the events on Wednesday and Thursday. Thank you!');
            $message = \Swift_Message::newInstance()
                    ->setSubject('EPITA International - Your Registrations for Thursday, 24th March and Friday 25th March 2016')
                    ->setFrom('epitaevents2016@gmail.com')
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