<?php

namespace DemandBundle\Form\Type;

use AppBundle\Entity\Group;
use DemandBundle\Entity\DemandPayment;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;

class DemandPaymentFormType extends DemandFormType
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
            'data_class'  => DemandPayment::class
        ]);
    }

    /**
     * @param FormEvent $event
     */
    protected function preSetData(FormEvent $event)
    {
        $form   = $event->getForm();
        $data   = $event->getData();
        $user   = $this->getUser();

        if (!$user->hasGroup(Group::GROUP_EMPLOYEE)) {
            $form->add('note');
        }

        if ($data->getId()) {
            $form->add(static::FIELD_TRANSITION, ChoiceType::class, [
                'choices'       => $this->getTransitionChoices(),
                'mapped'        => false,
                'constraints'   => [new NotBlank()]
            ]);
        }

        if (!$data->getId() || ($data->getState() == DemandPayment::STATE_REJECT
                && $data->getUser()->getId() == $this->getUser()->getId()
        )) {
            $form
                ->add('amount')
                ->add('year')
                ->add('month')
            ;
        }
    }
}
