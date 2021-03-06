<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\ApiController;
use AppBundle\Entity\Category;
use AppBundle\Entity\Question;
use AppBundle\Event\ImageUploadedEvent;
use AppBundle\Form\QuestionStatusType;
use AppBundle\Form\QuestionType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class QuestionController extends ApiController
{
    /**
     * @ApiDoc(
     *   section = "question",
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
        $statuses = [Category::STATUS_ENABLED, Category::STATUS_DISABLED];
        $category = $this->get('app.category_repository')->findCategoryByShortId($categoryId, $statuses);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException('Category not found.');
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

    /**
     * @ApiDoc(
     *   section = "question",
     *   description = "Update question status",
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
     *      "class" = "AppBundle\Form\QuestionStatusType"
     *   },
     *   requirements={
     *        {"name"="questionId", "dataType"="string", "description"="Question ID"}
     *   }
     * )
     *
     * @Rest\Patch("/questions/{questionId}/statuses")
     *
     * @param Request $request
     * @param $questionId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function patchAction(Request $request, $questionId)
    {
        $questionRepository = $this->get('app.question_repository');
        $statuses = [Question::STATUS_ENABLED, Question::STATUS_DISABLED];
        $question = $questionRepository->findQuestionByShortId($questionId, $statuses);
        if (!$question instanceof Question) {
            throw new NotFoundHttpException('Question not found.');
        }

        $formFactory = $this->get('form.factory');
        $form = $formFactory->createNamed('', QuestionStatusType::class, $question, ['method' => 'PATCH']);

        $this->handleForm($form, $request);
        if (!$form->isValid()) {
            return $this->createValidationErrorResponse($form);
        }

        $questionRepository->save($question);

        return $this->response('', 204);
    }

    /**
     * @ApiDoc(
     *   section = "question",
     *   description = "Get question",
     *   views = {"admin"},
     *   authentication=true,
     *   authenticationRoles={"ROLE_ADMIN"},
     *   resource = true,
     *   statusCodes = {
     *     200 = "Success",
     *     401 = "Authentication required",
     *     403 = "Unauthorized",
     *     404 = "Not found"
     *   },
     *   output= {
     *       "class" = "AppBundle\Entity\Question",
     *       "groups"={"question_simple", "question_detailed", "question_admin"}
     *   },
     *   requirements={
     *        {"name"="questionId", "dataType"="string", "description"="Question ID"}
     *   }
     * )
     *
     * @Rest\Get("/questions/{questionId}")
     *
     * @param $questionId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getAction($questionId)
    {
        $statuses = [Question::STATUS_ENABLED, Question::STATUS_DISABLED];
        $question = $this->get('app.question_repository')->findQuestionByShortId($questionId, $statuses);
        if (!$question instanceof Question) {
            throw new NotFoundHttpException('Question not found.');
        }

        return $this->response($question, 200, ['question_simple', 'question_detailed', 'question_admin']);
    }

    /**
     * @ApiDoc(
     *   section = "question",
     *   description = "Delete question - HARD",
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
     *        {"name"="questionId", "dataType"="string", "description"="Question ID"}
     *   }
     * )
     *
     * @Rest\Delete("/questions/{questionId}")
     *
     * @param $questionId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteAction($questionId)
    {
        $questionRepository = $this->get('app.question_repository');

        $statuses = [Question::STATUS_ENABLED, Question::STATUS_DISABLED];
        $question = $questionRepository->findQuestionByShortId($questionId, $statuses);
        if (!$question instanceof Question) {
            throw new NotFoundHttpException('Question not found.');
        }

        $questionRepository->remove($question);

        return $this->response('', 204);
    }

    /**
     * @ApiDoc(
     *   section = "question",
     *   description = "List questions",
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
     *       "class" = "AppBundle\Entity\Question",
     *       "groups"={"question_simple", "question_admin"},
     *       "collection" = true
     *   }
     * )
     *
     * @Rest\Get("/questions")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \Pagerfanta\Exception\OutOfRangeCurrentPageException
     * @throws \Pagerfanta\Exception\NotIntegerCurrentPageException
     * @throws \Pagerfanta\Exception\LessThan1CurrentPageException
     */
    public function listAction(Request $request)
    {
        $statuses = [Question::STATUS_ENABLED, Question::STATUS_DISABLED];
        $categoriesQB = $this->get('app.question_repository')->getQuestionsQB($statuses);

        $result = $this->get('app.pagination_manager')
            ->paginate($categoriesQB, $request->query->get('sorting', ['createdAt' => 'desc']))
            ->setCurrentPage($request->query->getInt('page', 1));

        return $this->responseWithPaginator($result, 200, ['question_simple', 'question_admin']);
    }
}
