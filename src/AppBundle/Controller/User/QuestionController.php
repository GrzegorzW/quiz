<?php

namespace AppBundle\Controller\User;

use AppBundle\Controller\ApiController;
use AppBundle\Entity\Category;
use AppBundle\Entity\Question;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class QuestionController extends ApiController
{
    /**
     * @ApiDoc(
     *   section = "question",
     *   description = "Get random questions collection",
     *   views = {"user", "admin"},
     *   authentication=true,
     *   authenticationRoles={"ROLE_USER"},
     *   resource = true,
     *   statusCodes = {
     *     200 = "Success",
     *     401 = "Authentication required",
     *     403 = "Unauthorized"
     *   },
     *   output= {
     *       "class" = "AppBundle\Entity\Question",
     *       "groups"={"question_simple", "question_detailed"},
     *       "collection" = true
     *   },
     *   parameters={
     *       {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "required"=false,
     *          "description"="Questions limit: max: 10; default 10."
     *       }
     *   },
     *   requirements={
     *        {"name"="categoryId", "dataType"="string", "description"="Category ID"}
     *   }
     * )
     *
     * @Rest\Get("/categories/{categoryId}/questions")
     *
     * @param Request $request
     * @param $categoryId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function cgetAction(Request $request, $categoryId)
    {
        $category = $this->get('app.category_repository')->findCategoryByShortId($categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException('Category not found.');
        }

        $limit = $request->request->getInt('limit', Question::RANDOM_QUESTIONS_LIMIT);
        if ($limit > Question::RANDOM_QUESTIONS_LIMIT) {
            $msg = printf('Limit is %s, given %s.', Question::RANDOM_QUESTIONS_LIMIT, $limit);
            throw new BadRequestHttpException($msg);
        }

        $questions = $this->get('app.question_repository')->getRandomQuestions($category->getId(), $limit);

        return $this->response($questions, 200, ['question_simple', 'question_detailed']);
    }


}
