<?php

namespace DemandBundle\Form\Type;

use DemandBundle\Entity\DayOffType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;

class DayOffTypeFormType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('is_disabled', CheckboxType::class, [
                'property_path' => 'isDisabled'
            ])
            ->add('is_auto', CheckboxType::class, [
                'property_path' => 'isAuto'
            ])
            ->add('period', ChoiceType::class, [
                'choices'       => [
                    DayOffType::PERIOD_MONTH    => DayOffType::PERIOD_MONTH,
                    DayOffType::PERIOD_YEAR     => DayOffType::PERIOD_YEAR
                ]
            ])
            ->add('days_amount', IntegerType::class, [
                'property_path' => 'daysAmount'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'        => DayOffType::class,
            'validation_groups' => function (FormInterface $form) {
                /** @var \DemandBundle\Entity\DayOffType $data */
                $data   = $form->getData();
                $groups = [Constraint::DEFAULT_GROUP];

                if ($data->getIsAuto() && (!$data->getPeriod() || !$data->getDaysAmount())) {
                    $groups[] = 'AutoEnabled';
                }

                return $groups;
            },
        ]);
    }
}
