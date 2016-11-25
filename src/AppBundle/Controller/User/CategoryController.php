<?php

namespace AppBundle\Controller\User;

use AppBundle\Controller\ApiController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends ApiController
{
    /**
     * @ApiDoc(
     *   section = "category",
     *   description = "List categories",
     *   views = { "user" },
     *   authentication=true,
     *   authenticationRoles={"ROLE_USER"},
     *   resource = true,
     *   statusCodes = {
     *     200 = "Success",
     *     400 = "Invalid params",
     *     401 = "Authentication required",
     *     403 = "Unauthorized"
     *   },
     *   output= {
     *       "class" = "AppBundle\Entity\Category",
     *       "groups"={"category_simple"},
     *       "collection" = true
     *   },
     *   filters={
     *       {"name"="phrase", "dataType"="string"}
     *   }
     * )
     *
     * @Rest\Get("/categories")
     *
     * @param Request $request
     * @param $phrase
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Pagerfanta\Exception\OutOfRangeCurrentPageException
     * @throws \Pagerfanta\Exception\NotIntegerCurrentPageException
     * @throws \Pagerfanta\Exception\LessThan1CurrentPageException
     */
    public function listAction(Request $request, $phrase)
    {
        $categoriesQB = $this->get('app.category_repository')->getCategoriesQB($phrase);

        $result = $this->get('app.pagination_manager')
            ->paginate($categoriesQB, $request->query->get('sorting', ['createdAt' => 'desc']))
            ->setCurrentPage($request->query->getInt('page', 1));

        return $this->responseWithPaginator($result, 200, ['category_simple']);
    }
}
