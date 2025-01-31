<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class TaskVoter extends Voter
{
    public const DELETE = 'TASK_DELETE';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::DELETE])
            && $subject instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $task = $subject;
        $user = $token->getUser();

        // Utilisateur non connecté
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::DELETE:
                // Cas 1 : L'utilisateur est administrateur et peut supprimer une tâche anonyme
                if ($task->getUser() && $task->getUser()->getUsername() === 'anonyme') {
                    return $this->security->isGranted('ROLE_ADMIN');
                }
                // Cas 2 : L'utilisateur est le créateur de la tâche
                return $task->getUser() === $user;
                break;
        }

        return false;
    }
}
