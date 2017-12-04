<?php

namespace DemandBundle\Form\Type;

use DemandBundle\Entity\DayOffRest;
use DemandBundle\Manager\DayOffTypeManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class DayOffRestFormType extends AbstractType
{
    const OPTION_USER   = 'user';
    const OPTION_TYPES  = 'types';

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', IntegerType::class)
            ->add('type', null, [
                'choices'   => $options[static::OPTION_TYPES]
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($options) {
            $data = $event->getData();

            $data->setStatus(DayOffRest::STATUS_INIT);

            if (isset($options[static::OPTION_USER]) && $options[static::OPTION_USER]) {
                $data->setUser($options[static::OPTION_USER]);
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'        => DayOffRest::class,
            static::OPTION_USER => null
        ]);

        $resolver->setRequired(static::OPTION_TYPES);
    }
}
