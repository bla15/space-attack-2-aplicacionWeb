<?php

namespace userBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use userBundle\Entity\Foro;
use userBundle\Form\ForoType;

class ForoController extends Controller
{
	 public function indexAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
        $dql   = "SELECT f FROM userBundle:Foro f ORDER BY f.id DESC";
        $foros = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
     	$pagination = $paginator->paginate(
            $foros,
            $request->query->getInt('page', 1),
            3
        );

     	return $this->render('userBundle:Foro:index.html.twig', array('pagination' => $pagination));
  
    }

    public function customAction(Request $request)
    {
      
        $idUser = $this->container->get('security.context')->getToken()->getUser()->getId();;
        
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT f FROM userBundle:Foro f JOIN f.user u WHERE u.id = :idUser ORDER BY f.id DESC";
        
        $foros = $em->createQuery($dql)->setParameter('idUser' , $idUser);
        
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $foros,
            $request->query->getInt('page', 1),
            3
        );
        
        $updateForm = $this->createCustomForm(':FORO_ID', 'PUT', 'foro_process');
        
        return $this->render('userBundle:Foro:custom.html.twig', array('pagination' => $pagination, 'update_form' => $updateForm->createView()));
    }
    public function processAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $foro = $em->getRepository('userBundle:Foro')->find($id);
        
        //si no existe la tarea
        if(!$foro)
        {
            throw $this>createNotFoundException('Topic not found');
        }
        
        $form = $this->createCustomForm($foro->getId(), 'PUT', 'foro_process');
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
        	$successMessage = $this->get('translator')->trans('The topic has been processed.');
            $warningMessage = $this->get('translator')->trans('The topic has already been processed.');

            if ($foro->getStatus() == 0)
            {
                $foro->setStatus(1);
                $em->flush();
                
                if($request->isXMLHttpRequest())
                {
                    return new Response(
                        json_encode(array('processed' => 1)),
                        200,
                        array('Content-Type' => 'application/json')
                    );
                }
            }
            //si ya esta finalizado que no lo vuelva a finalizar
            else
            {
                if($request->isXMLHttpRequest())
                {
                    return new Response(
                        json_encode(array('processed' => 0)),
                        200,
                        array('Content-Type' => 'application/json')
                    );
                }            
            }
        }
    }

	public function addAction(){
		
		$foro = new Foro();
		$form = $this->createCreateForm($foro);

		return $this->render('userBundle:Foro:add.html.twig', array('form' => $form->createView()));
	}
	private function createCreateForm(Foro $entity)
    {
        $form = $this->createForm(new ForoType(), $entity, array(
            'action' => $this->generateUrl('foro_create'),
            'method' => 'POST'
        ));
        
        return $form;
    }
    public function createAction(Request $request)
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
        $foro = new Foro();
        $form = $this->createCreateForm($foro);
        $form->handleRequest($request);
        
        //Validar si los datos del formulario son validos
        if($form->isValid())
        {
            $foro->setStatus(0);
            $foro->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($foro);
            $em->flush();
            
            $successMessage = $this->get('translator')->trans('The topic has been created.');
            $this->addFlash('mensaje', $successMessage); 
            return $this->redirectToRoute('foro_index');
        }
        
        return $this->render('userBundle:Foro:add.html.twig', array('form' => $form->createView()));
    }

    public function viewAction($id)
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
        $foro = $this->getDoctrine()->getRepository('userBundle:foro')->find($id);
        if(!$foro)
        {
            throw $this->createNotFoundException('The Topic does not exist.');
        }
        $deleteForm = $this->createCustomForm($foro->getId(), 'DELETE', 'foro_delete'); 

        $userTopic = $foro->getUser();
        
        return $this->render('userBundle:Foro:view.html.twig', array('foro' => $foro, 'user' => $user, 'userTopic' => $userTopic, 'delete_form' => $deleteForm->createView()));
    }

     public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $foro = $em->getRepository('userBundle:Foro')->find($id);
        
        if(!$foro)
        {
            throw $this->createNotFoundException('Topic not found');
        }
        
        $form = $this->createEditForm($foro);
        
        return $this->render('userBundle:Foro:edit.html.twig', array('foro' => $foro, 'form' => $form->createView()));
    }
    private function createEditForm(Foro $entity)
    {
        $form = $this->createForm(new ForoType(), $entity, array(
            'action' => $this->generateUrl('foro_update', array('id' => $entity->getId())),
            'method' => 'PUT'
        ));
        
        return $form;
    }
    public function updateAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $foro = $em->getRepository('userBundle:Foro')->find($id);
        
        if(!$foro)
        {
            throw $this->createNotFoundException('topic not found');
        }
        
        $form = $this->createEditForm($foro);
        $form->handleRequest($request);
        
        if($form->isSubmitted() and $form->isValid())
        {
            $foro->setStatus(0);
            $em->flush();
            $this->addFlash('mensaje', 'The topic has been modified');
            return $this->redirectToRoute('foro_edit', array('id' => $foro->getId()));
        }
        
        return $this->render('userBundle:Foro:edit.html.twig', array('foro' => $foro, 'form' => $form->createView()));
    }
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $foro = $em->getRepository('userBundle:Foro')->find($id);
        
        if(!$foro)
        {
            throw $this->createNotFoundException('topic not found');
        }
        
        $form = $this->createCustomForm($foro->getId(), 'DELETE', 'foro_delete');
        $form->handleRequest($request);
        
        if($form->isSubmitted() and $form->isValid())
        {
            $em->remove($foro);
            $em->flush();
            
            $this->addFlash('mensaje', 'The topic has been deleted');
            
            return $this->redirectToRoute('foro_index');
        }
    }
    private function createCustomForm($id, $method, $route)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod($method)
            ->getForm();
    }
   
}

