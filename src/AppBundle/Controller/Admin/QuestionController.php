<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\ApiController;
use AppBundle\Entity\Question;
use AppBundle\ErrorSerializer\FormErrorsSerializer;
use AppBundle\Event\ImageUploadedEvent;
use AppBundle\Form\QuestionType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class QuestionController extends ApiController
{
    /**
     * @ApiDoc(
     *   section = "quiestion",
     *   description = "Create question",
     *   views = {"admin"},
     *   authentication=true,
     *   authenticationRoles={"ROLE_ADMIN"},
     *   resource = true,
     *   statusCodes = {
     *     201 = "Success",
     *     400 = "Invalid params",
     *     401 = "Authentication required",
     *     403 = "Unauthorized",
     *     404 = "Not found",
     *   },
     *   input= {
     *      "class" = "AppBundle\Form\QuestionType"
     *   },
     *   output= {
     *       "class" = "AppBundle\Entity\Question",
     *       "groups"={"question_simple", "question_detailed", "question_admin"}
     *   },
     *   requirements={
     *        {"name"="categoryId", "dataType"="string", "description"="Category ID"}
     *   }
     * )
     *
     * @Rest\Post("/categories/{categoryId}/questions")
     *
     * @param Request $request
     * @param $categoryId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function postAction(Request $request, $categoryId)
    {
        $category = $this->get('app.category_repository')->findOneBy(['shortId' => $categoryId]);
        if (!$category) {
            throw new NotFoundHttpException();
        }

        $question = new Question($category);
        $form = $this->get('form.factory')->createNamed('', QuestionType::class, $question);

        $this->handleForm($form, $request);
        if (!$form->isValid()) {
            return $this->createValidationErrorResponse($form);
        }

        $this->get('event_dispatcher')->dispatch(
            ImageUploadedEvent::IMAGE_UPLOADED,
            new ImageUploadedEvent($question->getImage())
        );

        $this->get('app.question_repository')->save($question);

        return $this->response($question, 201, ['question_simple', 'question_detailed', 'question_admin']);
    }

//    /**
//     * @ApiDoc(
//     *   section = "category",
//     *   description = "Update category",
//     *   views = {"admin"},
//     *   authentication=true,
//     *   authenticationRoles={"ROLE_ADMIN"},
//     *   resource = true,
//     *   statusCodes = {
//     *     204 = "Success",
//     *     400 = "Invalid params",
//     *     401 = "Authentication required",
//     *     403 = "Unauthorized",
//     *     404 = "Not found"
//     *   },
//     *   input= {
//     *      "class" = "AppBundle\Form\CategoryType"
//     *   },
//     *   requirements={
//     *        {"name"="categoryId", "dataType"="string", "description"="Category ID"}
//     *   }
//     * )
//     *
//     * @Rest\Put("/categories/{categoryId}")
//     *
//     * @param Request $request
//     * @param $categoryId
//     * @return \Symfony\Component\HttpFoundation\Response
//     * @throws \Doctrine\ORM\OptimisticLockException
//     * @throws \Doctrine\ORM\ORMInvalidArgumentException
//     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
//     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
//     */
//    public function putAction(Request $request, $categoryId)
//    {
//        $category = $this->get('app.category_repository')->findOneBy(['shortId' => $categoryId]);
//        if (!$category) {
//            throw new NotFoundHttpException();
//        }
//
//        $form = $this->get('form.factory')->createNamed('', CategoryType::class, $category, ['method' => 'PUT']);
//
//        $this->handleForm($form, $request);
//        if (!$form->isValid()) {
//            return $this->createValidationErrorResponse($form);
//        }
//
//        $this->get('app.category_repository')->save($category);
//
//        return $this->response($category, 204);
//    }
//
//    /**
//     * @ApiDoc(
//     *   section = "category",
//     *   description = "Delete category",
//     *   views = {"admin"},
//     *   authentication=true,
//     *   authenticationRoles={"ROLE_ADMIN"},
//     *   resource = true,
//     *   statusCodes = {
//     *     204 = "Success",
//     *     401 = "Authentication required",
//     *     403 = "Unauthorized",
//     *     404 = "Not found"
//     *   },
//     *   requirements={
//     *        {"name"="categoryId", "dataType"="string", "description"="Category ID"}
//     *   }
//     * )
//     *
//     * @Rest\Delete("/categories/{categoryId}")
//     *
//     * @param $categoryId
//     * @return \Symfony\Component\HttpFoundation\Response
//     * @throws \Doctrine\ORM\OptimisticLockException
//     * @throws \Doctrine\ORM\ORMInvalidArgumentException
//     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
//     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
//     */
//    public function deleteAction($categoryId)
//    {
//        $category = $this->get('app.category_repository')->findOneBy(['shortId' => $categoryId]);
//        if (!$category) {
//            throw new NotFoundHttpException();
//        }
//
//        $this->get('app.category_repository')->remove($category);
//
//        return $this->response($category, 204);
//    }
}
