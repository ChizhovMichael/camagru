<?php

declare(strict_types=1);

namespace Camagru\Controller;

use Camagru\Common\Constant;
use Camagru\Kernel\Attribute\Middleware;
use Camagru\Kernel\Component\Request;
use Camagru\Kernel\Component\Response;
use Camagru\Kernel\Abstract\AbstractController;
use Camagru\Middleware\ConfirmMiddleware;
use Camagru\Middleware\SecurityMiddleware;
use Camagru\Model\User;

class MainController extends AbstractController
{
    public function __construct(
    ) {
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: ConfirmMiddleware::class)]
    public function welcome(Request $request): Response
    {
        $securityUser = $request->request->get('security_user');
        $user = $securityUser
            ? User::find($securityUser)
            : null;

        return $this->render('welcome', [
            'footer' => Constant::FOOTER,
            'header' => Constant::HEADER,
            'username' => $user?->getUsername()
        ]);
    }

    #[Middleware(class: SecurityMiddleware::class)]
    public function about(Request $request): Response
    {
        $securityUser = $request->request->get('security_user');
        $user = $securityUser
            ? User::find($securityUser)
            : null;

        return $this->render('about', [
            'header' => Constant::HEADER,
            'footer' => Constant::FOOTER,
            'username' => $user?->getUsername(),
        ]);
    }

    #[Middleware(class: SecurityMiddleware::class)]
    public function terms(Request $request): Response
    {
        $securityUser = $request->request->get('security_user');
        $user = $securityUser
            ? User::find($securityUser)
            : null;

        return $this->render('terms', [
            'header' => Constant::HEADER,
            'footer' => Constant::FOOTER,
            'username' => $user?->getUsername(),
        ]);
    }
}
