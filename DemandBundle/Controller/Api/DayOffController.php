<?php

namespace DemandBundle\Controller\Api;

use AppBundle\Controller\Api\ViewController;
use AppBundle\Entity\Group;
use DemandBundle\Form\Type\DayOffTypeFormType;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use DemandBundle\Entity\DayOffType;
use AppBundle\Security\Authorization\Model\Attribute;

/**
 * @Route("/v1/day-off-types")
 */
class DayOffController extends ViewController
{
    /**
     * @Get("", name="api_day_off_type_list")
     * @ApiDoc(
     *      statusCodes={
     *         200="Returned when successful",
     *         401="Returned when the user is Unauthorized"
     *      },
     *      description="Get Day Off Type List"
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        $user           = $this->getUser();
        $isAdminOrOwner = $user->hasGroup(Group::GROUP_OWNER) || $user->hasGroup(Group::GROUP_ADMIN);
        $query          = $this->get('demand.manager.day_off_type')->getListQuery($user, $request->query->all());
        $pagination     = $this->getPagination($query, $request->query->get('page', 1));
        $result         = $this->getPaginationResult($pagination);

        if (!$this->isTraversableGranted(Attribute::VIEW, $pagination->getItems(), [Attribute::COMPANY])) {
            return $this->handleView($this->view([], Response::HTTP_FORBIDDEN));
        }

        $groups = $isAdminOrOwner ? ['view_day_off_type', 'view_day_off_type_by_admin'] : ['view_day_off_type'];

        return $this->handleView($this->viewWithSerializationContext($result, Response::HTTP_OK, $groups));
    }

    /**
     * @Get("/{id}", name="api_day_off_type_view", requirements={"id": "\d+"})
     * @ApiDoc(
     *      statusCodes={
     *         200="Returned when successful",
     *         401="Returned when the user is Unauthorized"
     *      },
     *      description="View Day Off Type"
     * )
     *
     * @param $id
     * @return Response
     */
    public function viewAction($id)
    {
        $em     = $this->getDoctrine()->getManager();
        $item   = $em->getRepository(DayOffType::class)->find($id);

        if (!$item) {
            return $this->handleView($this->view([], Response::HTTP_NOT_FOUND));
        }

        if (!$this->isGranted(Attribute::VIEW_COMPANY, $item)) {
            return $this->handleView($this->view([], Response::HTTP_FORBIDDEN));
        }

        $user           = $this->getUser();
        $isAdminOrOwner = $user->hasGroup(Group::GROUP_OWNER) || $user->hasGroup(Group::GROUP_ADMIN);
        $groups         = $isAdminOrOwner ? ['view_day_off_type', 'view_day_off_type_by_admin'] : ['view_day_off_type'];

        /** @var View $view */
        $view = $this->viewWithSerializationContext($item, Response::HTTP_OK, $groups);

        return $this->handleView($view);
    }

    /**
     * @Post("", name="api_day_off_type_create")
     * @ApiDoc(
     *      statusCodes={
     *         201="Returned when successful",
     *         401="Returned when the user is Unauthorized"
     *      },
     *      description="Create Day Off Type"
     * )
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $item = new DayOffType();

        if (!$this->isGranted(Attribute::CREATE, $item)) {
            return $this->handleView($this->view([], Response::HTTP_FORBIDDEN));
        }

        $form = $this->createForm(DayOffTypeFormType::class, $item, ['csrf_protection' => false]);

        $item->setOwner($this->getUser()->getCompany()->getUser());
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->handleView($this->view($this->getFormErrors($form), Response::HTTP_BAD_REQUEST));
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($item);
        $em->flush();

        /** @var View $view */
        $view = $this->viewWithSerializationContext($item, Response::HTTP_CREATED, [
            'view_day_off_type',
            'view_day_off_type_by_admin'
        ]);

        return $this->handleView($view);
    }

    /**
     * @Put("/{id}", name="api_day_off_type_edit", requirements={"id": "\d+"})
     * @ApiDoc(
     *      statusCodes={
     *         200="Returned when successful",
     *         401="Returned when the user is Unauthorized"
     *      },
     *      description="Edit Day Off Type"
     * )
     *
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function editAction($id, Request $request)
    {
        $em         = $this->getDoctrine()->getManager();
        $item       = $em->getRepository(DayOffType::class)->find($id);

        if (!$item) {
            return $this->handleView($this->view([], Response::HTTP_NOT_FOUND));
        }

        if (!$this->isGranted(Attribute::EDIT_COMPANY, $item)) {
            return $this->handleView($this->view([], Response::HTTP_FORBIDDEN));
        }

        $form   = $this->createForm(DayOffTypeFormType::class, $item, [
            'method'            => Request::METHOD_PUT,
            'csrf_protection'   => false
        ]);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->handleView($this->view($this->getFormErrors($form), Response::HTTP_BAD_REQUEST));
        }

        $em->flush();

        /** @var View $view */
        $view = $this->viewWithSerializationContext($item, Response::HTTP_OK, [
            'view_day_off_type',
            'view_day_off_type_by_admin'
        ]);

        return $this->handleView($view);
    }

    /**
     * @Delete("/{id}", name="api_day_off_type_remove", requirements={"id": "\d+"})
     * @ApiDoc(
     *      statusCodes={
     *         200="Returned when successful",
     *         401="Returned when the user is Unauthorized"
     *      },
     *      description="Delete Day Off Type"
     * )
     *
     * @param $id
     * @return Response
     */
    public function removeAction($id)
    {
        $em     = $this->getDoctrine()->getManager();
        $item   = $em->getRepository(DayOffType::class)->find($id);

        if (!$item) {
            return $this->handleView($this->view([], Response::HTTP_NOT_FOUND));
        }

        if (!$this->isGranted(Attribute::DELETE_COMPANY, $item)) {
            return $this->handleView($this->view([], Response::HTTP_FORBIDDEN));
        }

        try {
            $em->remove($item);
            $em->flush();
        } catch (\Exception $e) {
            return $this->handleView($this->view(['errors' => 'Unable To Delete'], Response::HTTP_BAD_REQUEST));
        }

        return $this->handleView($this->view([], Response::HTTP_OK));
    }
}
