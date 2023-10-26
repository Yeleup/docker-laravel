<?php

namespace App\EventSubscriber;

use App\Entity\Customer;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private UserPasswordHasherInterface $passwordHasher;
    private FlashBagInterface $flashBag;

    public function __construct(UserPasswordHasherInterface $passwordHasher, FlashBagInterface $flashBag)
    {
        $this->passwordHasher = $passwordHasher;
        $this->flashBag = $flashBag;
    }

    public function BeforeEntityEvent($event)
    {
        // События при добавления пользователя
        $instance = $event->getEntityInstance();

        if (($instance instanceof User) && $instance->getPlainPassword()) {
            $instance->setPassword($this->passwordHasher->hashPassword($instance, $instance->getPlainPassword()));
        }

        // События при добавления клиента
        if ($instance instanceof Customer) {
            $this->flashBag->add('success', '<b>'.$instance->getName().'</b> успешно добавлен или изменен');
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'BeforeEntityEvent',
            BeforeEntityUpdatedEvent::class => 'BeforeEntityEvent',
        ];
    }
}
