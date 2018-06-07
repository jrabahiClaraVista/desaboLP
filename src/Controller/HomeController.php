<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Service\MailerBuilder;

/**
 * Controller used to manage the application security.
 * See https://symfony.com/doc/current/cookbook/security/form_login_setup.html.
 *
 * @author Jerome Rabahi <j.rabahi@claravista.fr>
 */
class HomeController extends Controller
{
    public function home(): Response
    {
        $mailer_service = $this->container->get('App\Service\MailerBuilder');
        $mailer = $mailer_service->getMailer();

        $options['objet'] = "[TEST] Ceci est un test";
        // Config a verified email on AWS SES here !!
        $options['from'] = ["clienteling@news-clarins.us" => "AdminAlias"];
        $options['replyTo'] = ['rabahi.jerome@gmail.com'];
        $options['to'] = ['j.rabahi@claravista.fr'];
        $options['text'] = 'Claravista test';
        $options['html'] = $this->renderView(
            // app/Resources/views/Emails/registration.html.twig
            'email/default.html.twig',
            array(
                'messageHtml'       => 'Some var passed through a var'
            )
        );

        $options['idCampaign'] = null;
        $options['trackingId'] = null;

        $message = $mailer_service->createMessage($options);

        $mailer->send($message);

        return $this->render('app/homepage.html.twig', [
            'mailer' => $mailer,
        ]);
    }
}
