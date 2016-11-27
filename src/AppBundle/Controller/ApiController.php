<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends FOSRestController
{
    public function handleForm(Form $form, Request $request)
    {
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            $form->submit([]);
        }
    }

    /**
     * @param $form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function createValidationErrorResponse($form)
    {
        return $this->response(
            [
                'message' => 'There was a validation error.',
                'code' => 400,
                'errors' => $this->getErrorsFromForm($form)
            ],
            400
        );
    }

    protected function response($obj, $code = 200, array $groups = ['none'])
    {
        return new Response($this->serialize($obj, $groups), $code);
    }

    protected function serialize($obj, array $groups = ['none'])
    {
        return $this->get('serializer')->serialize(
            $obj,
            $this->get('request_stack')->getCurrentRequest()->get('_format', 'json'),
            SerializationContext::create()->setGroups($groups)->setSerializeNull(true));
    }

    protected function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }

    protected function responseWithPaginator(Pagerfanta $pager, $code = 200, array $groups = ['none'])
    {
        return new Response($this->serializeWithPaginator($pager, $groups), $code);
    }

    protected function serializeWithPaginator(Pagerfanta $pager, array $groups = ['none'])
    {
        return $this->serialize(
            [
                'data' => $pager->getIterator()->getArrayCopy(),
                'paging' => [
                    'totalItems' => $pager->getNbResults(),
                    'totalPages' => $pager->getNbPages(),
                    'itemsPerPage' => $pager->getMaxPerPage(),
                    'currentPage' => $pager->getCurrentPage(),
                    'currentItemInPage' => count($pager->getIterator())
                ]
            ],
            $groups
        );
    }
}
