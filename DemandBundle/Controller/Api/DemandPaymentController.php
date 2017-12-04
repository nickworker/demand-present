<?php

namespace DemandBundle\Controller\Api;

use AppBundle\Controller\Api\ViewController;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use DemandBundle\Manager\DemandManager;
use DemandBundle\Entity\Demand;
use DemandBundle\Entity\DemandPayment;
use AppBundle\Security\Authorization\Model\Attribute;

/**
 * @Route("/v1/demands/payments")
 */
class DemandPaymentController extends ViewController
{
    /**
     * @Get("", name="api_demand_payment_list")
     * @ApiDoc(
     *      statusCodes={
     *         200="Returned when successful",
     *         401="Returned when the user is Unauthorized"
     *      },
     *      description="Get Demand Payment List"
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        /** @var \DemandBundle\Manager\DemandManager $manager */
        $manager    = $this->get('demand.manager.demand');
        $result     = $manager->getListQuery($this->getUser(), Demand::TYPE_PAYMENT, $request->query->all());
        $pagination = $this->getPagination($result[DemandManager::RESULT_DATA], $request->query->get('page', 1));

        if (!$this->isTraversableGranted(
            Attribute::VIEW,
            $pagination->getItems(),
            [Attribute::OWNER, Attribute::CHILD]
        )) {
            return $this->handleView($this->view([], Response::HTTP_FORBIDDEN));
        }

        /** @var View $view */
        $view = $this->viewWithSerializationContext(
            $this->getPaginationResult($pagination),
            $result[DemandManager::RESULT_STATUS],
            $result[DemandManager::RESULT_GROUPS]
        );

        return $this->handleView($view);
    }

    /**
     * @Post("", name="api_demand_payment_create")
     * @ApiDoc(
     *      statusCodes={
     *         201="Returned when successful",
     *         401="Returned when the user is Unauthorized"
     *      },
     *      description="Demand Payment Creation"
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        if (!$this->isGranted(Attribute::CREATE, new DemandPayment())) {
            return $this->handleView($this->view([], Response::HTTP_FORBIDDEN));
        }

        /** @var \DemandBundle\Manager\DemandManager $manager */
        $manager    = $this->get('demand.manager.demand');
        $result     = $manager->create($this->getUser(), Demand::TYPE_PAYMENT, $request);
        $view       = $this->viewWithSerializationContext(
            $result[DemandManager::RESULT_DATA],
            $result[DemandManager::RESULT_STATUS],
            $result[DemandManager::RESULT_GROUPS]
        );

        return $this->handleView($view);
    }

    /**
     * @Get("/{id}", name="api_demand_payment_view", requirements={"id": "\d+"})
     * @ApiDoc(
     *      statusCodes={
     *         200="Returned when successful",
     *         401="Returned when the user is Unauthorized"
     *      },
     *      description="Get Demand Payment"
     * )
     *
     * @return Response
     */
    public function viewAction($id)
    {
        /** @var \DemandBundle\Manager\DemandManager $manager */
        $manager    = $this->get('demand.manager.demand');
        $result     = $manager->getEntity($this->getUser(), $id, Demand::TYPE_PAYMENT);
        $item       = $result[DemandManager::RESULT_DATA];

        if ($item && $item instanceof DemandPayment
            && !$this->isGranted(Attribute::VIEW_OWNER, $item)
            && !$this->isGranted(Attribute::VIEW_CHILD, $item)
        ) {
            return $this->handleView($this->view([], Response::HTTP_FORBIDDEN));
        }

        $view       = $this->viewWithSerializationContext(
            $result[DemandManager::RESULT_DATA],
            $result[DemandManager::RESULT_STATUS],
            $result[DemandManager::RESULT_GROUPS]
        );

        return $this->handleView($view);
    }

    /**
     * @Put("/{id}", name="api_demand_payment_update", requirements={"id": "\d+"})
     * @ApiDoc(
     *      statusCodes={
     *         200="Returned when successful",
     *         401="Returned when the user is Unauthorized"
     *      },
     *      description="Demand Payment Update"
     * )
     *
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function updateAction($id, Request $request)
    {
        /** @var \DemandBundle\Manager\DemandManager $manager */
        $manager    = $this->get('demand.manager.demand');
        $data       = $manager->getEntity($this->getUser(), $id, Demand::TYPE_PAYMENT);
        $item       = $data[DemandManager::RESULT_DATA];

        if (!$item) {
            return $this->handleView($this->view([], Response::HTTP_NOT_FOUND));
        }

        if ($item instanceof DemandPayment
            && !$this->isGranted(Attribute::EDIT_OWNER, $item)
            && !$this->isGranted(Attribute::EDIT_CHILD, $item)
        ) {
            return $this->handleView($this->view([], Response::HTTP_FORBIDDEN));
        }

        $result     = $manager->update($this->getUser(), $item, $request);
        $view       = $this->viewWithSerializationContext(
            $result[DemandManager::RESULT_DATA],
            $result[DemandManager::RESULT_STATUS],
            $result[DemandManager::RESULT_GROUPS]
        );

        return $this->handleView($view);
    }

    /**
     * @Get("/states", name="api_demand_payment_count_state")
     * @ApiDoc(
     *      statusCodes={
     *         200="Returned when successful",
     *         401="Returned when the user is Unauthorized"
     *      },
     *      description="Get Demand Payment Count By States"
     * )
     *
     * @return Response
     */
    public function stateCountAction()
    {
        if (!$this->isGranted(Attribute::VIEW, new DemandPayment())) {
            return $this->handleView($this->view([], Response::HTTP_FORBIDDEN));
        }

        /** @var \DemandBundle\Manager\DemandManager $manager */
        $manager    = $this->get('demand.manager.demand');
        $items      = $manager->getCountByState($this->getUser(), Demand::TYPE_PAYMENT);
        $view     = $this->view($items, Response::HTTP_OK);

        return $this->handleView($view);
    }
}
