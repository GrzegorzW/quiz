<?php

namespace AppBundle\Controller\User;

use AppBundle\Controller\ApiController;
use AppBundle\Entity\AnswersResult;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class AnswerController extends ApiController
{
    /**
     * @ApiDoc(
     *   section = "answer",
     *   description = "Post answers",
     *   views = {"user", "admin"},
     *   authentication=true,
     *   authenticationRoles={"ROLE_USER"},
     *   resource = true,
     *   statusCodes = {
     *     200 = "Success",
     *     400 = "Invalid params",
     *     401 = "Authentication required",
     *     403 = "Unauthorized",
     *     404 = "Not found"
     *   },
     *   requirements={
     *   {
     *       "name"="answers[]",
     *       "dataType"="array",
     *       "description"="Array of answers ids"
     *   }
     *  }
     * )
     *
     * @Rest\Post("/answers")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function postAction(Request $request)
    {
        $answersManager = $this->get('app.answers_manager');

        $answers = $answersManager->handleAnswers($request->request->get('answers'));
        $score = $answersManager->getCorrectAnswersCount($answers);

        $result = new AnswersResult($score, $answers);

        return $this->response($result, 200, ['answer_simple', 'question_simple']);
    }
}
