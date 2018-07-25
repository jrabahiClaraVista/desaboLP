<?php

/*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Form\DesaboType;

use App\Service\MailBuilder;
use App\Service\SplioAPI;

/**
* Controller used to manage the unsuscribe page
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
     * @var SplioAPI
     */
    private $splioAPI;
    public function __construct(MailBuilder $mail_builder, SplioAPI $splioAPI)
    {
        $this->mail_builder = $mail_builder;
        $this->splioAPI = $splioAPI;
    }


    public function homepage(): Response
    {
        throw new NotFoundHttpException('404 Not Found');
    }

    public function unsubscribe(Request $request, $campaign, $email, $hash): Response
    {
        // check if email exists and is unsub

        $exists = $this->splioAPI->exists($email);
        $isBlacklist = $this->splioAPI->isBlacklist($email);

        if( isset($exists->code)) {
            if ($exists->code == 404) {
                throw new NotFoundHttpException('404 Not Found');
            }
        }

        if( isset($isBlacklist->code)) {
            if ($isBlacklist->code == 200) {
                return $this->redirectToRoute('validate');
            }
        }

        // Secure email check
        if($hash != $exists->fields[80]->value){
            $email = null;
        }

        $campaign = urldecode ( $campaign );

        // Start unsuscribe form

        $form = $this->createForm(DesaboType::class);
        $form->handleRequest($request);

        $data = $form->getData();

        if ($request->getMethod() == 'POST' && $form->isSubmitted() && $form->isValid()) {
            $update = $this->splioAPI->update($email,[$campaign, $data['choice']]);


            if(isset($update->code)){
                if($update->code == 404){
                    throw new NotFoundHttpException('404 Not Found');
                }
                else{
                    $addBlacklist = $this->splioAPI->addBlacklist($email);

                    if(isset($addBlacklist->code)){
                        if($addBlacklist->code == 404){
                            throw new NotFoundHttpException('404 Not Found');
                        }
                        else{
                            return $this->redirectToRoute('validate');
                        }
                    }
                }
            }
        }
        else{
        }

        return $this->render('app/unsubscribe.html.twig', [
            'form' => $form->createView(),
            'campaign' => $campaign,
            'email' => $email
        ]);
    }

    public function validate(): Response
    {
        return $this->render('app/_validate.html.twig', [
                ]);
    }


}
