<?php

namespace userBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use userBundle\Entity\User;
use userBundle\Form\UserType;

class UserController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('userBundle:User')->findAll();

        return $this->render('userBundle:User:index.html.twig', array('users' => $users));

    }

    public function addAction()
    {
        $user = new User();
        $form = $this->createCreateForm($user);

        return $this->render('userBundle:User:add.html.twig', array('form' => $form->createView()));
        //return new Response('aaa');
    }

    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        return $form;
    }
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createCreateForm($user);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $password = $form->get('password')->getData();

            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);

            $user->setPassword($encoded);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $successMessage = $this->get('translator')->trans('The user has been created.');
            $this->addFlash('mensaje', $successMessage);

            return $this->redirectToRoute('user_index');
        }

        return $this->render('userBundle:User:add.html.twig', array('form' => $form->createView()));
    }

    public function viewAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('userBundle:User');

        $user = $repository->find($id);

        // $user = $repository->findOneByUsername($nombre);

        return new Response('Usuario: ' . $user->getNick() . ' con email: ' . $user->getEmail());

    }
}
