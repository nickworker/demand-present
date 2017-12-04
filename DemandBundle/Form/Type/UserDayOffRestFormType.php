<?php

namespace DemandBundle\Form\Type;

use AppBundle\Manager\UserManager;
use DemandBundle\Manager\DayOffTypeManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use DemandBundle\Request\DayOffRestCollectionRequest;
use Symfony\Component\Validator\Constraints\Count;

class UserDayOffRestFormType extends AbstractType
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var DayOffTypeManager
     */
    private $dayOffTypeManager;

    /**
     * @param UserManager $userManager
     * @param DayOffTypeManager $dayOffTypeManager
     */
    public function __construct(UserManager $userManager, DayOffTypeManager $dayOffTypeManager)
    {
        $this->userManager = $userManager;
        $this->dayOffTypeManager = $dayOffTypeManager;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $types      = $this->getDayOffTypes();
        $totalTypes = count($types);

        $builder->add('days', CollectionType::class, [
            'allow_add'     => true,
            'allow_delete'  => true,
            'entry_type'    => DayOffRestFormType::class,
            'entry_options' => [
                DayOffRestFormType::OPTION_USER     => $options[DayOffRestFormType::OPTION_USER],
                DayOffRestFormType::OPTION_TYPES    => $types
            ],
            'constraints'   => [new Count(['min' => $totalTypes, 'max' => $totalTypes])]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'                    => DayOffRestCollectionRequest::class,
            DayOffRestFormType::OPTION_USER => null,
        ]);
    }

    /**
     * @return array
     */
    private function getDayOffTypes()
    {
        return $this->dayOffTypeManager->getListQuery($this->userManager->getUser(), [
            DayOffTypeManager::FILTER => [
                DayOffTypeManager::FILTER_IS_DISABLED   => DayOffTypeManager::FILTER_NO,
                DayOffTypeManager::FILTER_IS_AUTO       => DayOffTypeManager::FILTER_NO,
            ]
        ])->getResult();
    }
}
