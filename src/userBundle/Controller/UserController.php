<?php

namespace userBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use userBundle\Entity\User;
use userBundle\Form\UserType;

class UserController extends Controller
{
    public function homeAction()
    {
        $user = new User();
        return $this->render('userBundle:User:home.html.twig');
    }

    public function homeForStrangersAction()
    {
        $user = new User();
        return $this->render('userBundle:User:home.html.twig');
    }

    public function planetsAction()
    {
        $user = new User();
        return $this->render('userBundle:User:planetas.html.twig');
    }
    public function enemiesAction()
    {
        $user = new User();
        return $this->render('userBundle:User:enemies.html.twig');
    }

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //$users = $em->getRepository('userBundle:User')->findAll();
        $dql   = "SELECT u FROM userBundle:User u ORDER BY u.id DESC";
        $users = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $users, $request->query->getInt('page', 1),
            10
        );

        $deleteFormAjax = $this->createCustomForm(':USER_ID', 'DELETE', 'user_delete');

        return $this->render('userBundle:User:index.html.twig', array('pagination' => $pagination, 'delete_form_ajax' => $deleteFormAjax->createView()));

    }

    public function addAdminAction()
    {
        $user = new User();
        $user->setRole('ROLE_USER');
        $form = $this->createCreateFormAdmin($user);

        return $this->render('userBundle:User:addAdmin.html.twig', array('form' => $form->createView()));
        //return new Response('aaa');
    }
    private function createCreateFormAdmin(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_create_admin'),
            'method' => 'POST',
        ));

        return $form;
    }
    public function createAdminAction(Request $request)
    {
        $user = new User();
        $form = $this->createCreateForm($user);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $password = $form->get('password')->getData();

            $passwordConstraint = new Assert\NotBlank();
            $errorList          = $this->get('validator')->validate($password, $passwordConstraint);

            if (count($errorList) == 0) {
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $password);

                $user->setPassword($encoded);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $successMessage = $this->get('translator')->trans('The user has been created.');
                $this->addFlash('mensaje', $successMessage);

                return $this->redirectToRoute('user_index');
            } else {
                $errorMessage = new FormError($errorList[0]->getMessage());
                $form->get('password')->addError($errorMessage);
            }

        }

        return $this->render('userBundle:User:add.html.twig', array('form' => $form->createView()));
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

            $passwordConstraint = new Assert\NotBlank();
            $errorList          = $this->get('validator')->validate($password, $passwordConstraint);

            if (count($errorList) == 0) {
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $password);

                $user->setPassword($encoded);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $successMessage = $this->get('translator')->trans('The user has been created.');
                $this->addFlash('mensaje', $successMessage);

                return $this->redirectToRoute('user_homepage');
            } else {
                $errorMessage = new FormError($errorList[0]->getMessage());
                $form->get('password')->addError($errorMessage);
            }

        }

        return $this->render('userBundle:User:add.html.twig', array('form' => $form->createView()));
    }

    public function editAction($id)
    {
        $em   = $this->getDoctrine()->getManager();
        $user = $em->getRepository('userBundle:User')->find($id);

        if (!$user) {
            $messageException = $this->get('translator')->trans('User not found.');
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createEditForm($user);

        return $this->render('userBundle:User:edit.html.twig', array('user' => $user, 'form' => $form->createView()));

    }
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array('action' => $this->generateUrl('user_update', array('id' => $entity->getId())), 'method' => 'PUT'));

        return $form;
    }
    public function updateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('userBundle:User')->find($id);
        if (!$user) {
            $messageException = $this->get('translator')->trans('User not found.');
            throw $this->createNotFoundException($messageException);
        }

        $form = $this->createEditForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            //si el usuario modifica la contraseña metemos una contraseña nueva
            if (!empty($password)) {
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $password);
                $user->setPassword($encoded);
            } else {
                $recoverPass = $this->recoverPass($id);
                $user->setPassword($recoverPass[0]['password']);
            }

            if ($form->get('role')->getData() == 'ROLE_ADMIN') {
//si el role del usuario a modificar es admin, esta siempre active
                $user->setIsActive(1);
            }
            $em->flush();

            $successMessage = $this->get('translator')->trans('The user has been modified.');
            $this->addFlash('mensaje', $successMessage);

            return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }
        return $this->render('userBundle:User:edit.html.twig', array('user' => $user, 'form' => $form->createView()));
    }
    private function recoverPass($id)
    {
        $em    = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT u.password
            FROM userBundle:User u
            WHERE u.id = :id'
        )->setParameter('id', $id);

        $currentPass = $query->getResult();

        return $currentPass;
    }

    public function viewAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('userBundle:User');

        $user = $repository->find($id);

        if (!$user) {
            $messageException = $this->get('translator')->trans('User not found.');
            throw $this->createNotFoundException($messageException);
        }

        $deleteForm = $this->createCustomForm($user->getId(), 'DELETE', 'user_delete');

        return $this->render('userBundle:User:view.html.twig', array('user' => $user, 'delete_form' => $deleteForm->createView()));
    }

    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('userBundle:User')->find($id);

        if (!$user) {
            $messageException = $this->get('translator')->trans('User not found.');
            throw $this->createNotFoundException($messageException);
        }

        //calculamos los usuarios que quedan puesto que ajax no recarga la pagina
        $allUsers   = $em->getRepository('userBundle:User')->findAll();
        $countUsers = count($allUsers);

        // $form = $this->createDeleteForm($user);
        $form = $this->createCustomForm($user->getId(), 'DELETE', 'user_delete');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->isXMLHttpRequest()) {
                $res = $this->deleteUser($user->getRole(), $em, $user);

                return new Response(
                    json_encode(array('removed' => $res['removed'], 'message' => $res['message'], 'countUsers' => $countUsers)),
                    200,
                    array('Content-Type' => 'application/json')
                );
            }

            $res = $this->deleteUser($user->getRole(), $em, $user);
            $this->addFlash($res['alert'], $res['message']);
            return $this->redirectToRoute('user_index');
        }
    }
    private function deleteUser($role, $em, $user)
    {
        if ($role == 'ROLE_USER') {
            $em->remove($user);
            $em->flush();

            $message = $this->get('translator')->trans('The user has been deleted.');
            $removed = 1;
            $alert   = 'mensaje';
        } elseif ($role == 'ROLE_ADMIN') {
            $message = $this->get('translator')->trans('The user could not be deleted.');
            $removed = 0;
            $alert   = 'error';
        }

        return array('removed' => $removed, 'message' => $message, 'alert' => $alert);
    }
    private function createCustomForm($id, $method, $route)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod($method)
            ->getForm();
    }
}
