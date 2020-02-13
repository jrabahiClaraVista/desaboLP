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

use App\Entity\SplioToken;

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

    public function test(Request $request, $method, $email): Response
    {
        $session = $request->getSession();

        // check if email exists and is unsub
        $method = urldecode ( $method );
        $email = urldecode ( $email );

        $em = $this->getDoctrine()->getManager();
        $splioToken = $em->getRepository(SplioToken::class)->findOneBy([],["createdAt" => "DESC"]);

        $date = new \DateTime();

        if($splioToken == null){
            $splioToken = new SplioToken();
            $auth = $this->splioAPI->auth($session);
            $splioToken->setToken($auth->token);
            $em->persist($splioToken);
            $em->flush();
        }
        elseif($splioToken->getCreatedAt() < $date->modify("-59 minutes")) {
            $splioToken = new SplioToken();
            $auth = $this->splioAPI->auth($session);
            $splioToken->setToken($auth->token);
            $em->persist($splioToken);
            $em->flush();
        }

        $token = $splioToken->getToken();

        $exists = $this->splioAPI->exists($email);


        switch($method){
            case 'PUT' :
                $result = $this->splioAPI->addBlacklist($email);
            break;
            case 'DELETE' :
                $result = $this->splioAPI->deleteBlacklistPerso($email, $token);
            break;
            case 'POST' :
                $result = $this->splioAPI->AddBlacklistPerso($email, $token);
            break;
            default :
                $result = $this->splioAPI->isBlacklist($email);
            break;
        }

        return $this->render('app/_test.html.twig', [
            'method'        => $method,
            'exists'        => $exists,
            'result'        => $result,
            'auth'          => $splioToken
        ]);
    }

    public function unsubscribe(Request $request, $campaign, $email, $hash): Response
    {
        $session = $request->getSession();
        // check if email exists and is unsub
        $campaign = urldecode ( $campaign );
        $email = urldecode ( $email );
        $hash = urldecode ( $hash );

        $em = $this->getDoctrine()->getManager();
        $splioToken = $em->getRepository(SplioToken::class)->findOneBy([],["createdAt" => "DESC"]);

        $date = new \DateTime();

        if($splioToken == null){
            $splioToken = new SplioToken();
            $auth = $this->splioAPI->auth($session);
            $splioToken->setToken($auth->token);
            $em->persist($splioToken);
            $em->flush();
        }
        elseif($splioToken->getCreatedAt() < $date->modify("-59 minutes")) {
            $splioToken = new SplioToken();
            $auth = $this->splioAPI->auth($session);
            $splioToken->setToken($auth->token);
            $em->persist($splioToken);
            $em->flush();
        }

        $token = $splioToken->getToken();

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

        $email_hash = "";
        foreach ($exists->fields as $key => $field) {
            if ( $field->name == 'email_hash' )
                $email_hash = $field->value;
        }

        // Secure email check
        if($hash != $email_hash){
            $email = null;
        }

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
                    //$addBlacklist = $this->splioAPI->addBlacklist($email);
                    $addBlacklist = $this->splioAPI->AddBlacklistPerso($email, $token);

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
