<?php

namespace DemandBundle\Controller\Api;

use AppBundle\Controller\Api\ViewController;
use AppBundle\Entity\User;
use AppBundle\Security\Authorization\Model\Attribute;
use DemandBundle\Form\Type\DayOffRestFormType;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use DemandBundle\Entity\DayOffRest;
use DemandBundle\Form\Type\UserDayOffRestFormType;

/**
 * @Route("/v1/rest-day-offs")
 */
class DayOffRestController extends ViewController
{
    /**
     * @Post("/users/{id}", name="api_user_update_rest_days_offs", requirements={"id": "null|\d+"})
     * @ApiDoc(
     *      statusCodes={
     *         200="Returned when successful",
     *         401="Returned when the user is Unauthorized"
     *      },
     *      description="Update User Rest Days Off"
     * )
     * @param Request $request
     * @param null $id
     * @return Response
     */
    public function updateAction(Request $request, $id = null)
    {
        $em     = $this->getDoctrine()->getManager();
        $user   = ($id) ? $em->getRepository(User::class)->find($id) : $this->getUser();

        if (!$user) {
            return $this->handleView($this->view([], Response::HTTP_NOT_FOUND));
        }

        if (($id && !$this->isGranted(Attribute::EDIT_CHILD, $user))
            || (!$id && !$this->isGranted(Attribute::EDIT_OWNER, $user))
        ) {
            return $this->handleView($this->view([], Response::HTTP_FORBIDDEN));
        }

        /** @var \DemandBundle\Manager\DayOffRestManager $dayOffRestManager */
        $dayOffRestManager = $this->get('demand.manager.day_off_rest');
        $item              = $dayOffRestManager->buildRequest($user);

        /** @var Form $form */
        $form = $this->createForm(UserDayOffRestFormType::class, $item, [
            DayOffRestFormType::OPTION_USER => $user,
            'csrf_protection'               => false
        ]);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->handleView($this->view($this->getFormErrors($form), Response::HTTP_BAD_REQUEST));
        }

        $activeDays = $dayOffRestManager->updateFromRequest($user, $item);
        $result     = array_merge($item->days, $activeDays);

        $view = $this->viewWithSerializationContext($result, Response::HTTP_OK, [
            'view_day_off_rest', 'view_day_off_type'
        ]);

        return $this->handleView($view);
    }

    /**
     * @Get("/users/{id}", name="api_user_get_rest_days_offs", requirements={"id": "null|\d+"})
     * @ApiDoc(
     *      statusCodes={
     *         200="Returned when successful",
     *         401="Returned when the user is Unauthorized"
     *      },
     *      description="Update User Rest Days Off"
     * )
     *
     * @param null $id
     * @return Response
     */
    public function listAction($id = null)
    {
        $em     = $this->getDoctrine()->getManager();
        $userId = ($id) ? $id : $this->getUser()->getId();
        $items  =  $em->getRepository(DayOffRest::class)->findBy(['user' => $userId]);

        if (count($items)) {
            $firstItem = reset($items);

            if (($id && !$this->isGranted(Attribute::VIEW_CHILD, $firstItem))
                || (!$id && !$this->isGranted(Attribute::VIEW_OWNER, $firstItem))
            ) {
                return $this->handleView($this->view([], Response::HTTP_FORBIDDEN));
            }
        }

        $view   = $this->viewWithSerializationContext($items, Response::HTTP_OK, [
            'view_day_off_rest', 'view_day_off_type', 'view_day_off_type_is_disabled', 'view_day_off_type_is_auto'
        ]);

        return $this->handleView($view);
    }
}
