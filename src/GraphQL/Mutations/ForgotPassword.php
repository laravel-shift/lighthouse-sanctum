<?php

declare(strict_types=1);

namespace DanielDeWit\LighthouseSanctum\GraphQL\Mutations;

use DanielDeWit\LighthouseSanctum\Contracts\Services\ResetPasswordServiceInterface;
use DanielDeWit\LighthouseSanctum\Enums\ForgotPasswordStatus;
use Exception;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Translation\Translator;

class ForgotPassword
{
    protected PasswordBroker $passwordBroker;
    protected ResetPasswordServiceInterface $resetPasswordService;
    protected Translator $translator;

    public function __construct(
        PasswordBroker $passwordBroker,
        ResetPasswordServiceInterface $resetPasswordService,
        Translator $translator
    ) {
        $this->passwordBroker       = $passwordBroker;
        $this->resetPasswordService = $resetPasswordService;
        $this->translator           = $translator;
    }

    /**
     * @param mixed $_
     * @param array<string, mixed> $args
     * @return array<string, ForgotPasswordStatus|array|string|null>
     * @throws Exception
     */
    public function __invoke($_, array $args): array
    {
        if (isset($args['reset_password_url'])) {
            $this->resetPasswordService->setResetPasswordUrl($args['reset_password_url']['url']);
        }

        $this->passwordBroker->sendResetLink([
            'email' => $args['email'],
        ]);

        return [
            'status'  => ForgotPasswordStatus::EMAIL_SENT(),
            'message' => $this->translator->get('An email has been sent'),
        ];
    }
}
