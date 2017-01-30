<?php

namespace userBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function indexAction()
    {
       return new Response('aaa');
    }

    public function articulesAction($page)
    {
       return new Response('Este es mi articulo' . $page);
    }
}
