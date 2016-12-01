<?php

namespace AppBundle\Controller\User;

use AppBundle\Controller\ApiController;
use AppBundle\Entity\User;
use AppBundle\Form\RegisterUserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends ApiController
{
    /**
     * @ApiDoc(
     *   section = "user",
     *   description = "Create user",
     *   views = {"user", "admin"},
     *   authentication = false,
     *   resource = true,
     *   statusCodes = {
     *     201 = "Success",
     *     400 = "Invalid params",
     *     401 = "Authentication required",
     *     403 = "Unauthorized"
     *   },
     *   input = {"class"="AppBundle\Form\RegisterUserType"},
     *   output = {
     *      "class" = "AppBundle\Entity\User",
     *      "groups"={"user_simple", "user_detailed"}
     *   }
     * )
     *
     * @Rest\Post("public/users")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function postAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $form = $this->get('form.factory')->createNamed('', RegisterUserType::class, $user);

        $this->handleForm($form, $request);
        if (!$form->isValid()) {
            return $this->createValidationErrorResponse($form);
        }

        $userManager->updateUser($user);

        $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');

        $result = [
            'token' => $jwtManager->create($user),
            'user' => $user
        ];

        return $this->response($result, 200, ['user_simple', 'user_detailed']);
    }

    /**
     * @ApiDoc(
     *   section = "user",
     *   description = "Get logged user",
     *   views = {"user", "admin"},
     *   authentication = true,
     *   authenticationRoles = {"ROLE_USER"},
     *   resource = true,
     *   statusCodes = {
     *      200 = "Success",
     *      401 = "Authentication required",
     *      404 = "Not found"
     *   },
     *   output = {
     *      "class" = "AppBundle\Entity\User",
     *      "groups"={"user_simple", "user_detailed"}
     *   }
     * )
     *
     * @Rest\Get("/users")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getAction()
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found.');
        }

        return $this->response($user, 200, ['user_simple', 'user_detailed']);
    }
}
