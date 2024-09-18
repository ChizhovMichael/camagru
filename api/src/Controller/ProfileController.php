<?php

declare(strict_types=1);

namespace Camagru\Controller;

use Camagru\Common\Constant;
use Camagru\Kernel\Abstract\AbstractController;
use Camagru\Kernel\Attribute\Middleware;
use Camagru\Kernel\Component\Request;
use Camagru\Kernel\Component\Response;
use Camagru\Middleware\AuthMiddleware;
use Camagru\Middleware\ConfirmMiddleware;
use Camagru\Middleware\SecurityMiddleware;
use Camagru\Model\User;

class ProfileController extends AbstractController
{
    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: ConfirmMiddleware::class)]
    public function index(Request $request): Response
    {
        $securityUser = $request->request->get('security_user');
        $user = User::find($securityUser);

        return $this->render('profile', [
            'header' => Constant::HEADER,
            'footer' => Constant::FOOTER,
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
        ]);
    }
}
