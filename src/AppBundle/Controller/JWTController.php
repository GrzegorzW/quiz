<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class JWTController extends Controller
{
    /**
     * @Route("/api/getToken")
     * @throws \InvalidArgumentException
     */
    public function getTokenAction()
    {
        return new Response('', 401);
    }
}
