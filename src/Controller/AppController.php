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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Form\DesaboType;

use App\Service\MailBuilder;

/**
* Controller used to manage the application security.
* See https://symfony.com/doc/current/cookbook/security/form_login_setup.html.
*
* @author Jerome Rabahi <j.rabahi@claravista.fr>
*/
class AppController extends AbstractController
{
    /**
     * @var MailBuilder
     */
    private $mail_builder;

    /**
     * @var string
     */
    private $splio_universe;

    /**
     * @var string
     */
    private $splio_pass;

    public function __construct(MailBuilder $mail_builder, $splio_universe, $splio_pass)
    {
        $this->mail_builder = $mail_builder;
        $this->splio_universe = $splio_universe;
        $this->splio_pass = $splio_pass;
    }


    public function homepage(): Response
    {
        return $this->render('app/homepage.html.twig', [
            
        ]);
    }

    public function unsubscribe(Request $request,$campaign, $email): Response
    {
        $form = $this->createForm(DesaboType::class);
        $form->handleRequest($request);

        return $this->render('app/unsubscribe.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
