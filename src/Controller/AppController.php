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
use App\Service\SplioScpApi;

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

    /**
     * @var SplioAPI
     */
    private $splioScpApi;

    public function __construct(MailBuilder $mail_builder, SplioAPI $splioAPI, SplioScpApi $splioScpApi)
    {
        $this->mail_builder = $mail_builder;
        $this->splioAPI = $splioAPI;
        $this->splioScpApi = $splioScpApi;
    }


    public function homepage(): Response
    {
        throw new NotFoundHttpException('404 Not Found');
    }

    public function testScp(Request $request, $method, $email): Response
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
            $auth = $this->splioScpApi->auth();
            $splioToken->setToken($auth->token);
            $em->persist($splioToken);
            $em->flush();
        }
        elseif($splioToken->getCreatedAt() < $date->modify("-59 minutes")) {
            $splioToken = new SplioToken();
            $auth = $this->splioScpApi->auth();
            $splioToken->setToken($auth->token);
            $em->persist($splioToken);
            $em->flush();
        }

        $token = $splioToken->getToken();

        //$create = $this->splioScpApi->create("Jerome","Rabahi", $email, $token);
        $exists = $this->splioScpApi->exists($email, $token);

        $result = null;
        switch($method){
            case 'DELETE' :
                $result = $this->splioScpApi->deleteBlacklistPerso($email, $token);
            break;
            case 'POST' :
                $result[0] = $this->splioScpApi->AddBlacklistPerso($email, $token);
                $result[1] = $this->splioScpApi->update($email,["test_campagne","test_api"],$token);
            break;
            default :
                $result = $this->splioScpApi->isBlacklist($email, $token);
            break;
        }        

        return $this->render('app/_test.html.twig', [
            'method'        => $method,
            'exists'        => $exists,
            'result'        => $result,
            'auth'          => $splioToken
        ]);
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
            $auth = $this->splioScpApi->auth();
            $splioToken->setToken($auth->token);
            $em->persist($splioToken);
            $em->flush();
        }
        elseif($splioToken->getCreatedAt() < $date->modify("-59 minutes")) {
            $splioToken = new SplioToken();
            $auth = $this->splioScpApi->auth();
            $splioToken->setToken($auth->token);
            $em->persist($splioToken);
            $em->flush();
        }

        $token = $splioToken->getToken();

        $exists = $this->splioScpApi->exists($email, $token);
        $isBlacklist = $this->splioScpApi->isBlacklist($email, $token);

        if( isset($exists->email)) {
            if ($exists->email != $email) {
                throw new NotFoundHttpException('404 Not Found');
            }
        }
        else {
            throw new NotFoundHttpException('404 Not Found');
        }

        if( isset($isBlacklist->count_element)) {
            if ($isBlacklist->count_element > 0) {
                return $this->redirectToRoute('validate');
            }
        }

        $email_hash = "";

        foreach ($exists->custom_fields as $key => $field) {
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
            $update = $this->splioScpApi->update($email,[$campaign, $data['choice']], $token);

            if( isset($update->email)) {
                if ($update->email != $email) {
                    throw new NotFoundHttpException('404 Not Found');
                }
                else{
                    $addBlacklist = $this->splioScpApi->AddBlacklistPerso($email, $token);
                    if(isset($addBlacklist)) {
                        if(count($addBlacklist->successes) == 0){
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
