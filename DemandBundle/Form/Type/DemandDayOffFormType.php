<?php

namespace DemandBundle\Form\Type;

use AppBundle\Entity\Group;
use DemandBundle\Entity\DemandDayOff;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;

class DemandDayOffFormType extends DemandFormType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $this->preSetData($event);
        });
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'  => DemandDayOff::class,
        ]);
    }

    /**
     * @param FormEvent $event
     */
    protected function preSetData(FormEvent $event)
    {
        /** @var \DemandBundle\Entity\DemandDayOff $data */
        $data   = $event->getData();
        $form   = $event->getForm();
        $user   = $this->getUser();

        if (!$user->hasGroup(Group::GROUP_EMPLOYEE)) {
            $form->add('note');
        }

        if ($data->getId()) {
            $form->add(static::FIELD_TRANSITION, ChoiceType::class, [
                'choices'       => $this->getTransitionChoices($data),
                'mapped'        => false,
                'constraints'   => [new NotBlank()]
            ]);
        }

        if (!$data->getId() || ($data->getState() == DemandDayOff::STATE_REJECT
            && $data->getUser()->getId() == $this->getUser()->getId()
        )) {
            $form->add('type')
                ->add('started_at', DateType::class, [
                    'widget'        => 'single_text',
                    'format'        => 'dd/MM/yyyy',
                    'property_path' => 'startedAt'
                ])
                ->add('ended_at', DateType::class, [
                    'widget'        => 'single_text',
                    'format'        => 'dd/MM/yyyy',
                    'property_path' => 'endedAt'
                ])
            ;
        }
    }
}
