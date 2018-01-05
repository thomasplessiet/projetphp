<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Event;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('homepage.html.twig');
    }
    
    /**
     * @Route("/read/event", name="readevent")
     */
    public function readAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Event');

        $events = $repository->findAll();

        return $this->render('readevent.html.twig', array(
                    'listeEvents' => $events
        ));
    }
    
    /**
     * @Route("/read/group", name="readgroupe")
     */
    public function readGAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $repository1 = $this->getDoctrine()->getRepository('AppBundle:Groupe');

        $users = $repository->findAll();
        $groupes = $repository1->findAll();

        return $this->render('readgroupe.html.twig', array(
                    'listeUsers' => $users, 'listeGroupes' => $groupes
        ));
    }
    
    /**
     * @Route("/read/user/{id}", name="infoUser")
     */
    public function readUAction($id, Request $request)
    {
       $em = $this->getDoctrine()->getManager();
       $user = $em->getRepository('AppBundle:User')->findOneById($id);
       if (!$user) {
           throw $this->createNotFoundException(
                   'No Event found for ID ' . $id
           );
       }
        
        return $this->render('readuser.html.twig', array(
                    'listeInfoUser' => $user
        ));
    }
    
    /**
     * @Route("/add/event", name="addevent")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addAction(Request $request)
    {
        $event = new Event();

        $form = $this->createFormBuilder($event)
                ->add('nom', TextType::class)
                ->add('dateDebut', DateTimeType::class)
                ->add('dateFin', DateTimeType::class)
                ->add('description', TextType::class)
                ->add('salle', TextType::class)
                ->add('groupe', EntityType::class, array(
                    'class' => 'AppBundle:Groupe',
                    'choice_label' => 'nom',
                    'multiple' => false,
                    'expanded' => true,
                ))
                ->add('user', EntityType::class, array(
                    'class' => 'AppBundle:User',
                    'choice_label' => 'username',
                    'multiple' => false,
                    'expanded' => true,
                ))
                ->add('submit', SubmitType::class, [
                    'label' => 'Valider',
                    'attr' => ['class' => 'btn btn-success'],
                ])
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Evenement ajouté avec succès!')
            ;

            return $this->redirect($this->generateUrl('addevent', array('etat' => 'succes')
            ));
        }

        return $this->render('addevent.html.twig', array(
                    'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/event/{id}/update", name="updateEvent")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction($id, Request $request) {

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('AppBundle:Event')->findOneById($id);
        $event_name = $event->getNom();
        if (!$event) {
            throw $this->createNotFoundException(
                    'No Event found for ID ' . $id
            );
        }


        $form = $this->createFormBuilder($event)
                ->add('nom', TextType::class)
                ->add('dateDebut', DateTimeType::class)
                ->add('dateFin', DateTimeType::class)
                ->add('description', TextType::class)
                ->add('salle', TextType::class)
                ->add('groupe', EntityType::class, array(
                    'class' => 'AppBundle:Groupe',
                    'choice_label' => 'nom',
                    'multiple' => false,
                    'expanded' => true,
                ))
                ->add('user', EntityType::class, array(
                    'class' => 'AppBundle:User',
                    'choice_label' => 'username',
                    'multiple' => false,
                    'expanded' => true,
                ))
                ->add('submit', SubmitType::class, [
                    'label' => 'Modifier',
                    'attr' => ['class' => 'btn btn-success'],
                ])
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();

            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Evenement modifié avec succès!')
            ;

            return $this->redirect($this->generateUrl('readevent', array('etat' => 'succes')
            ));
        }

        return $this->render('updateEvent.html.twig', array(
                    'form' => $form->createView(), 'event_nom' => $event_name
        ));
    }
    
    /**
     * @Route("/group/{id}/update", name="updateGroupe")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editGAction($id, Request $request) {

        $em = $this->getDoctrine()->getManager();
        $groupe = $em->getRepository('AppBundle:Groupe')->findOneById($id);
        $groupe_name = $groupe->getNom();
        if (!$groupe) {
            throw $this->createNotFoundException(
                    'No Event found for ID ' . $id
            );
        }


        $form = $this->createFormBuilder($groupe)
                ->add('nom', TextType::class)
                ->add('submit', SubmitType::class, [
                    'label' => 'Modifier',
                    'attr' => ['class' => 'btn btn-success'],
                ])
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();

            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Groupe modifié avec succès!')
            ;

            return $this->redirect($this->generateUrl('readgroupe', array('etat' => 'succes')
            ));
        }

        return $this->render('updateGroupe.html.twig', array(
                    'form' => $form->createView(), 'groupe_nom' => $groupe_name
        ));
    }
    
    
    /**
     * @Route("/event/{id}/delete", name="deleteEvent")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction($id, Request $request) {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Event');
        $event = $repository->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();

        $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Evenement supprimé avec succès!')
        ;
        return $this->redirect($this->generateUrl('readevent', array('etat' => 'succes')));
    } 
    
     /**
     * @Route("/groupe/{id}/delete", name="deleteGroupe")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deletGeAction($id, Request $request) {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Groupe');
        $event = $repository->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();

        $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Groupe supprimé avec succès!')
        ;
        return $this->redirect($this->generateUrl('readgroupe', array('etat' => 'succes')));
    } 
}
