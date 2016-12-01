<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\ApiController;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends ApiController
{
    /**
     * @ApiDoc(
     *   section = "user",
     *   description = "Get user",
     *   views = {"admin"},
     *   authentication=true,
     *   authenticationRoles={"ROLE_ADMIN"},
     *   resource = true,
     *   statusCodes = {
     *      200 = "Success",
     *      401 = "Authentication required",
     *      403 = "Unauthorized",
     *      404 = "Not found"
     *   },
     *   output= {
     *      "class" = "AppBundle\Entity\User",
     *      "groups"={"user_simple", "user_detailed", "user_admin"}
     *   },
     *   requirements={
     *      {"name"="userId", "dataType"="string", "description"="User ID"}
     *   }
     * )
     *
     * @Rest\Get("/users/{userId}")
     *
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getAction($userId)
    {
        $user = $this->get('app.user_repository')->findUserByShortId($userId, true);
        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found.');
        }

        return $this->response($user, 200, ['user_simple', 'user_detailed', 'user_admin']);
    }

    /**
     * @ApiDoc(
     *   section = "user",
     *   description = "Update user",
     *   views = {"admin"},
     *   authentication=true,
     *   authenticationRoles={"ROLE_ADMIN"},
     *   resource = true,
     *   statusCodes = {
     *     204 = "Success",
     *     400 = "Invalid params",
     *     401 = "Authentication required",
     *     403 = "Unauthorized",
     *     404 = "Not found"
     *   },
     *   input= {
     *       "class" = "AppBundle\Form\UserType"
     *   },
     *   requirements={
     *       {"name"="userId", "dataType"="string", "description"="User ID"}
     *   }
     * )
     *
     * @Rest\Put("/users/{userId}")
     *
     * @param Request $request
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function putAction(Request $request, $userId)
    {
        $user = $this->get('app.user_repository')->findUserByShortId($userId, true);
        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found.');
        }

        $formFactory = $this->get('form.factory');
        $form = $formFactory->createNamed('', UserType::class, $user, ['method' => 'PUT']);

        $this->handleForm($form, $request);
        if (!$form->isValid()) {
            return $this->createValidationErrorResponse($form);
        }

        $this->get('fos_user.user_manager')->updateUser($user);

        return $this->response('', 204);
    }

    /**
     * @ApiDoc(
     *   section = "user",
     *   description = "Delete user - soft",
     *   views = {"admin"},
     *   authentication=true,
     *   authenticationRoles={"ROLE_ADMIN"},
     *   resource = true,
     *   statusCodes = {
     *     204 = "Success",
     *     401 = "Authentication required",
     *     403 = "Unauthorized",
     *     404 = "Not found"
     *   },
     *   requirements={
     *       {"name"="userId", "dataType"="string", "description"="User ID"}
     *   }
     * )
     *
     * @Rest\Delete("/users/{userId}")
     *
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteAction($userId)
    {
        $userRepository = $this->get('app.user_repository');

        $user = $userRepository->findUserByShortId($userId, true);
        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found.');
        }

        $user->setDeleted(true);
        $userRepository->save($user);

        return $this->response('', 204);
    }

    /**
     * @ApiDoc(
     *   section = "user",
     *   description = "List users",
     *   views = {"user", "admin"},
     *   authentication=true,
     *   authenticationRoles={"ROLE_ADMIN"},
     *   resource = true,
     *   statusCodes = {
     *     200 = "Success",
     *     400 = "Invalid params",
     *     401 = "Authentication required",
     *     403 = "Unauthorized"
     *   },
     *   output= {
     *       "class" = "AppBundle\Entity\User",
     *       "groups"={"user_simple", "user_detailed"},
     *       "collection" = true
     *   },
     *   filters={
     *       {"name"="phrase", "dataType"="string", "description"="Search phrase"},
     *       {"name"="allowDisabled", "dataType"="bool", "description"="Allow disabled users. Default: true"}
     *   }
     * )
     *
     * @Rest\Get("/users")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \LogicException
     * @throws \Pagerfanta\Exception\OutOfRangeCurrentPageException
     * @throws \Pagerfanta\Exception\NotIntegerCurrentPageException
     * @throws \Pagerfanta\Exception\LessThan1CurrentPageException
     */
    public function listAction(Request $request)
    {
        $phrase = $request->query->get('phrase');
        if ($phrase && !is_string($phrase)) {
            throw new BadRequestHttpException('Phrase must be type of string');
        }

        $allowDisabled = $request->query->getBoolean('allowDisabled', true);

        $usersQB = $this->get('app.user_repository')->getUsersQB($phrase, $allowDisabled);

        $result = $this->get('app.pagination_manager')
            ->paginate($usersQB, $request->query->get('sorting', ['createdAt' => 'desc']))
            ->setCurrentPage($request->query->getInt('page', 1));

        return $this->responseWithPaginator($result, 200, ['user_simple', 'user_detailed']);
    }
}
