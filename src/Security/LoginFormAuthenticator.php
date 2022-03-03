<?php

namespace App\Security;

use App\Form\LoginFormType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'login';

    private UrlGeneratorInterface $urlGenerator;
    private FormFactoryInterface $formFactory;

    public function __construct(UrlGeneratorInterface $urlGenerator,FormFactoryInterface $formFactory)
    {
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
    }

    public function authenticate(Request $request)
    {   
        $captcha = $request->request->get('g-recaptcha-response');

        $post = $request->request;
        $form = $this->formFactory->create(LoginFormType::class);
        $form->submit($post->all());

        if (!$form->isValid()) { 
            $errors = $form->getErrors(true);
            
            // header("Location: /");
            // die();
            // return new RedirectResponse($this->urlGenerator->generate('dashboard-index')); 
            // dd($errors);
        }

        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('login_form', $request->request->get('_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        

        return new RedirectResponse($this->urlGenerator->generate('dashboard-index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
