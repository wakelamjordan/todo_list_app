<?php

namespace App\State;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManager;
use App\Dto\UserRegistrationDto;
use Symfony\Component\Mime\Email;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @implements ProcessorInterface<UserRegistrationDto, User>
 */
class UserRegistrationStateProcessor implements ProcessorInterface
{
    // private UserPasswordHasherInterface $passwordHasher;
    // private EntityManagerInterface $entityManager;
    // private MailerInterface $mailer;

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private EmailVerifier $emailVerifier
    ) {
        // $this->passwordHasher = $passwordHasher;
        // $this->entityManager = $entityManager;
        // $this->mailer = $mailer;
    }

    /**
     * @param UserRegistrationDto $data
     * 
     * @throws NotFoundHTTPExeption
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $checkEmail = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data->email]);

        $email = (new Email())
            ->from($_ENV['MAILER_FROM'])
            ->to($data->email);

        if (!$checkEmail) {
            $user = new User;

            $user
                ->setEmail($data->email)
                ->setPassword($this->passwordHasher->hashPassword($user, $data->email));

            $email
                ->subject('Confirmez votre inscription')
                ->text('votre email de confirmation')
                ->html('<h1>votre email de confirmation</h1>');
        } else {

            $email
                ->subject('Demande d\'inscription')
                ->text('Une demande d\'inscription a été faite avec cette addresse mail, si vous en ête l\'auteur sachez que vous disposez déjà d\'un compte à cette addresse, si vous avez perdu vos identifiants de connection vous pouvez les récupérer ici. Si vous n\'êtes pas à l\'origine de cette demande d\'inscription n\'en tenez pas compte.')
                ->html('<h1>Une demande d\'inscription a été faite avec cette addresse mail, si vous en ête l\'auteur sachez que vous disposez déjà d\'un compte à cette addresse, si vous avez perdu vos identifiants de connection vous pouvez les récupérer ici. Si vous n\'êtes pas à l\'origine de cette demande d\'inscription n\'en tenez pas compte.</h1>');
        }

        $this->mailer->send($email);

        $return = [
            'status' => 'success',
            'message' => 'Opération effectuée avec succès.',
            'data' => ['account_registration' => $data->email]
        ];

        return new JsonResponse($return, JsonResponse::HTTP_OK);
    }
}
