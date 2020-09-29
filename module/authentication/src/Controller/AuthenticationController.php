<?php

declare(strict_types=1);

namespace CfdiAdmin\Authentication\Controller;

use CfdiAdmin\Authentication\Security\BruteForceChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $helper, BruteForceChecker $bruteForceChecker): Response
    {
        if (!$bruteForceChecker->canLogin($request)) {
            return $this->render('@CfdiAdminAuthentication/too_many_attempts.html.twig');
        }

        // if user is already logged in, don't display the login page again
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('@CfdiAdminAuthentication/login.html.twig', [
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
