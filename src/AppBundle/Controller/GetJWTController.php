<?php

namespace AppBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class GetJWTController extends Controller
{
    /**
     * /**
     * @ApiDoc(
     *   section = "token",
     *   description = "Get token",
     *   views = {"user", "admin"},
     *   authentication=false,
     *   resource = false,
     *   statusCodes = {
     *     200 = "Success",
     *     400 = "Invalid params"
     *   },
     *   requirements={
     *   {
     *       "name"="username",
     *       "dataType"="string",
     *       "description"="User email is his username"
     *   }, {
     *       "name"="password",
     *       "dataType"="string",
     *       "description"="User password"
     *   }
     *  }
     * )
     *
     * @throws \InvalidArgumentException
     */
    public function getTokenAction()
    {
        return new Response('', 401);
    }
}
