<?php

namespace App\Form;

use App\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerOrderType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', MoneyType::class, [
                'html5' => true,
                'currency' => 'KZT',
                'label_format' => $this->translator->trans('customer_order.amount'),
                'attr' => ['autocomplete' => 'off'],
                'constraints' => [
                    new NotBlank(['message' => 'Не должно быть пустым']),
                ],
            ])
            ->add('type', null, [
                'label_format' => $this->translator->trans('customer_order.type'),
                'constraints' => [
                    new NotBlank(['message' => 'Не должно быть пустым']),
                ],
            ])
            ->add('payment', null, ['label_format' => $this->translator->trans('customer_order.payment')])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
