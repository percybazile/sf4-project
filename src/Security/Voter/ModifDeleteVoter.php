<?php

namespace App\Security\Voter;

use App\Entity\ModifDelete;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ModifDeleteVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html

        // Le voter ne doit intervenir que s'il s'agit de l'attribut (similaire à un role) "MODIF_DELETE"
        // et si le sujet (ce sur quoi on vérifie le droit) est une instance de ModifDeleteVoter
        return $attribute === 'MODIF_DELETE'
            && $subject instanceof ModifDelete;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var ModifDelete $subject */

        $user = $token->getUser();
        // Utilisateur non connecté = pas le droit de supprimer
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        // Utilisateur auteur de la ModifDelete = autorisé à supprimer sa propre ModifDelete
        if ($user === $subject->getAuthor()) {
            return true;
        }

        // Aucun des cas précédents = pas le droit de supprimer
        return false;
    }
}
