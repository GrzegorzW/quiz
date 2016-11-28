<?php

namespace AppBundle\Controller\User;

use AppBundle\Controller\ApiController;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends ApiController
{
    /**
     * @ApiDoc(
     *   section = "category",
     *   description = "List enabled categories",
     *   views = {"user", "admin"},
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
     *   }
     * )
     *
     * @Rest\Get("/categories")
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
        $categoriesQB = $this->get('app.category_repository')->getCategoriesQB();

        $result = $this->get('app.pagination_manager')
            ->paginate($categoriesQB, $request->query->get('sorting', ['createdAt' => 'desc']))
            ->setCurrentPage($request->query->getInt('page', 1));

        return $this->responseWithPaginator($result, 200, ['category_simple']);
    }
}
