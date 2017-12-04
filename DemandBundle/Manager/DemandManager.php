<?php

namespace DemandBundle\Manager;

use AppBundle\Entity\User;
use AppBundle\Entity\Group;
use AppBundle\Manager\UserManager;
use DemandBundle\Entity\DemandDayOff;
use DemandBundle\Entity\DemandPayment;
use DemandBundle\Entity\Demand;
use DemandBundle\Form\Type\DemandPaymentFormType;
use DemandBundle\Form\Type\DemandDayOffFormType;
use DemandBundle\Form\Type\DemandFormType;
use AppBundle\Manager\GroupManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Workflow\Workflow;
use AppBundle\Helper\FormErrorHelper;

class DemandManager
{
    const RESULT_DATA   = 'result';
    const RESULT_STATUS = 'status';
    const RESULT_GROUPS = 'groups';

    const FILTER        = 'filter';
    const FILTER_STATE  = 'state';
    const FILTER_USER   = 'user';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var Workflow
     */
    private $workflow;
    /**
     * @var FormErrorHelper
     */
    private $formErrorHelper;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory
     * @param Workflow $workflow
     * @param FormErrorHelper $formErrorHelper
     * @param UserManager $userManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        Workflow $workflow,
        FormErrorHelper $formErrorHelper,
        UserManager $userManager
    ) {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->workflow = $workflow;
        $this->formErrorHelper = $formErrorHelper;
        $this->userManager = $userManager;
    }

    /**
     * @param User $user
     * @param $type
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function create(User $user, $type, Request $request)
    {
        $item       = ($type == Demand::TYPE_PAYMENT) ? new DemandPayment() : new DemandDayOff();
        $item->setUser($user);

        if (!$this->workflow->can($item, Demand::STATE_START)) {
            return [
                static::RESULT_DATA     => null,
                static::RESULT_STATUS   => Response::HTTP_FORBIDDEN,
                static::RESULT_GROUPS   => []
            ];
        }

        $this->workflow->apply($item, Demand::STATE_START);

        /** @var Form $form */
        $form = $this->formFactory->create($this->getFormClass($type), $item, ['csrf_protection' => false]);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return [
                static::RESULT_DATA     => $this->formErrorHelper->getErrors($form),
                static::RESULT_STATUS   => Response::HTTP_BAD_REQUEST,
                static::RESULT_GROUPS   => []
            ];
        }

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return [
            static::RESULT_DATA     => $item,
            static::RESULT_STATUS   => Response::HTTP_CREATED,
            static::RESULT_GROUPS   => $this->getSerializationGroupForUser($user, $type)
        ];
    }

    /**
     * @param User $user
     * @param Demand $item
     * @param Request $request
     * @return array
     */
    public function update(User $user, Demand $item, Request $request)
    {
        $type = ($item instanceof DemandPayment) ? Demand::TYPE_PAYMENT : Demand::TYPE_DAY_OFF;
        $form = $this->formFactory->create($this->getFormClass($type), $item, [
            'csrf_protection'   => false,
            'method'            => Request::METHOD_PUT
        ]);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return [
                static::RESULT_DATA     => $this->formErrorHelper->getErrors($form),
                static::RESULT_STATUS   => Response::HTTP_BAD_REQUEST,
                static::RESULT_GROUPS   => []
            ];
        }

        if (!$this->workflow->can($item, $form->get(DemandFormType::FIELD_TRANSITION)->getData())) {
            return [
                static::RESULT_DATA     => null,
                static::RESULT_STATUS   => Response::HTTP_FORBIDDEN,
                static::RESULT_GROUPS   => []
            ];
        }

        $this->workflow->apply($item, $form->get(DemandFormType::FIELD_TRANSITION)->getData());

        $this->entityManager->flush();

        return [
            static::RESULT_DATA     => $item,
            static::RESULT_STATUS   => Response::HTTP_OK,
            static::RESULT_GROUPS   => $this->getSerializationGroupForUser($user, $type)
        ];
    }

    /**
     * @param User $user
     * @param $type
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function getListQuery(User $user, $type, array $params = [])
    {
        $repo   = $this->entityManager->getRepository($this->getDemandType($type));
        $result = [static::RESULT_STATUS => Response::HTTP_OK];
        $params = $this->rebuildParams($params);

        if ($user->hasGroup(Group::GROUP_EMPLOYEE)) {
            $result[static::RESULT_DATA]    = $repo->getByUserQuery($user->getId(), $params);
            $result[static::RESULT_GROUPS]  = [];

        } else {
            $usersIds = (isset($params[static::FILTER]) && isset($params[static::FILTER][static::FILTER_USER]))
                        ? $this->userManager->getChildrenIds($user, $params[static::FILTER][static::FILTER_USER])
                        : $this->userManager->getChildrenIds($user);

            $result[static::RESULT_DATA]    = count($usersIds) ? $repo->getByUsersIdQuery($usersIds, $params) : [];
            $result[static::RESULT_GROUPS]  = $this->getSerializationGroupForUser($user, $type);
        }

        return $result;
    }

    /**
     * @param User $user
     * @param $type
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function getCountByState(User $user, $type, array $params = [])
    {
        $repo   = $this->entityManager->getRepository($this->getDemandType($type));
        $states = [];

        if ($user->hasGroup(Group::GROUP_EMPLOYEE)) {
            $states = $repo->findCountByStatusForUser($user->getId());
        }

        $usersIds = (isset($params[static::FILTER]) && isset($params[static::FILTER][static::FILTER_USER]))
                    ? $this->userManager->getChildrenIds($user, $params[static::FILTER][static::FILTER_USER])
                    : $this->userManager->getChildrenIds($user);

        if (count($usersIds)) {
            $states = $repo->findCountByStatusForUsers($usersIds);
        }

        $result = [
            ['state' => Demand::INTERNAL_STATE_WAITING_FOR_DRH,     'total' => 0],
            ['state' => Demand::INTERNAL_STATE_WAITING_FOR_MANAGER, 'total' => 0],
            ['state' => Demand::STATE_ACCEPT_BY_DRH,                'total' => 0],
            ['state' => Demand::STATE_REJECT,                       'total' => 0],
        ];

        foreach ($result as $key => $val) {
            foreach ($states as $state) {
                if ($val['state'] == $state['state']) {
                    $result[$key] = $state;
                }
            }
        }

        return $result;
    }

    /**
     * @param User $user
     * @param $demandId
     * @param $type
     * @return array
     * @throws \Exception
     */
    public function getEntity(User $user, $demandId, $type)
    {
        $item = $this->entityManager->getRepository($this->getDemandType($type))->find($demandId);

        if (!$item) {
            return [
                static::RESULT_DATA     => null,
                static::RESULT_STATUS   => Response::HTTP_NOT_FOUND,
                static::RESULT_GROUPS   => []
            ];
        }

        return [
            static::RESULT_DATA     => $item,
            static::RESULT_STATUS   => Response::HTTP_OK,
            static::RESULT_GROUPS   => $this->getSerializationGroupForUser($user, $type)
        ];
    }

    /**
     * @return array
     */
    private function getDemandListTypes()
    {
        return [
            Demand::TYPE_PAYMENT    => DemandPayment::class,
            Demand::TYPE_DAY_OFF    => DemandDayOff::class
        ];
    }

    /**
     * @param $type
     * @return mixed
     * @throws \Exception
     */
    private function getDemandType($type)
    {
        $types = $this->getDemandListTypes();

        if (!isset($types[$type])) {
            throw new \Exception(sprintf('Demand type %s is not supported'));
        }

        return $types[$type];
    }

    /**
     * @return array
     */
    public static function getStateByUserGroups()
    {
        return [
            Group::GROUP_EMPLOYEE   => [Demand::STATE_START, Demand::STATE_REOPEN, Demand::STATE_REJECT],
            Group::GROUP_DRH        => [Demand::STATE_ACCEPT_BY_DRH, Demand::STATE_REJECT],
            Group::GROUP_MANAGER    => [Demand::STATE_ACCEPT_BY_MANAGER, Demand::STATE_REJECT],
            Group::GROUP_OWNER      => [
                Demand::STATE_ACCEPT_BY_MANAGER, Demand::STATE_ACCEPT_BY_DRH, Demand::STATE_REJECT
            ]
        ];
    }

    /**
     * @param Demand $demand
     * @param User $user
     * @return array
     */
    public static function getStateByUserRelation(Demand $demand, User $user)
    {
        $states     = static::getStateByUserGroups();
        $result     = [];
        $isOwner    = $demand->getUser() && $demand->getUser()->getId() == $user->getId();

        if (!$demand->getId() || $isOwner) {
            $result = $states[Group::GROUP_EMPLOYEE];
        }

        if ($demand->getId() && $user->hasGroup(Group::GROUP_DRH)
            && ($demand->getUser()->hasGroup(Group::GROUP_MANAGER) || $demand->getUser()->hasGroup(Group::GROUP_DRH))) {
            $result = array_merge($result, $states[Group::GROUP_OWNER]);
        } elseif ($demand->getId() && !$isOwner && $demand->getUser()->hasGroup(Group::GROUP_EMPLOYEE)) {
            $result = isset($states[$user->getGroupSystemName()])
                        ? array_merge($result, $states[$user->getGroupSystemName()]) : $result;
        }

        return array_unique($result);
    }

    /**
     * @return array
     */
    private function getSerializationGroups()
    {
        return [
            Demand::TYPE_PAYMENT => [
                'view_demand',
                'view_demand_payment'
            ],
            Demand::TYPE_DAY_OFF => [
                'view_demand',
                'view_demand_day_off',
                'view_day_off_type'
            ],
        ];
    }

    /**
     * @param User $user
     * @param $demandType
     * @return array
     */
    private function getSerializationGroupForUser(User $user, $demandType)
    {
        $groups = $this->getSerializationGroups();

        if (!isset($groups[$demandType])) {
            return [];
        }

        return $user->hasGroup(Group::GROUP_EMPLOYEE)
                ? $groups[$demandType] : array_merge($groups[$demandType], ['view_user_id', 'name_last_name']);
    }

    /**
     * @param $type
     * @return mixed
     */
    private function getFormClass($type)
    {
        return ($type == Demand::TYPE_PAYMENT) ? DemandPaymentFormType::class : DemandDayOffFormType::class;
    }

    /**
     * @param $data
     * @return array
     */
    private function rebuildParams($data)
    {
        foreach ($data as &$val) {
            if (is_array($val)) {
                $val = $this->rebuildParams($val);
            }
        }

        return array_filter($data);
    }
}
