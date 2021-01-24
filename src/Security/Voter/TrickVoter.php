<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TrickVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['ENTITY_EDIT', 'ENTITY_DELETE'])
            && $subject instanceof \App\Entity\Trick;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // If Admin
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // If Owner
        switch ($attribute) {
            case 'ENTITY_EDIT':
            case 'ENTITY_DELETE':
                return $subject->getOwner() === $user;
                break;
        }

        return false;
    }
}
