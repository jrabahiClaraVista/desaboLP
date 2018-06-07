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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Service\MailBuilder;

/**
* Controller used to manage the application security.
* See https://symfony.com/doc/current/cookbook/security/form_login_setup.html.
*
* @author Jerome Rabahi <j.rabahi@claravista.fr>
*/
class HomeController extends AbstractController
{
    /**
     * @var MailBuilder
     */
    private $mailer_builder;

    public function __construct(MailBuilder $mailer_builder)
    {
        $this->mailer_builder = $mailer_builder;
    }


    public function home(): Response
    {
        $mailer = $this->mailer_builder->getMailer();

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

        $message = $this->mailer_builder->createMessage($options);

        //Try to send message and get exeption if fail
        try{
            //COMMENT DO NOT SEND EMAIL FOR NOW
            //$mailer->send($message);
            //$var = 1;
        }
        catch(\Swift_TransportException $e){
            $success = 0;
            $response = $e->getMessage() ;
            $request->getSession()->getFlashBag()->add('notice', 'Error while sending the email.');
            //var_dump($response); die();
        }

        return $this->render('app/homepage.html.twig', [
            'mailer' => $mailer,
        ]);
    }
}
