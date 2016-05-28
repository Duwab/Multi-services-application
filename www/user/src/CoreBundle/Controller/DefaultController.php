<?php

namespace CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
    /**
     * @Route("/test", name="testpage")
     */
    public function testAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
    /**
     * @Route("/csrf", name="csrf")
     */
    public function csrfAction(Request $request)
    {
        /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrf */
        $csrf = $this->get('security.csrf.token_manager'); //Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
//        $token = $csrf->refreshToken($intention); // Intention is specified in form type
        $token = $csrf->refreshToken('authenticate'); // Intention is specified in form type

        return new Response($token);
    }
    /**
     * @Route("/jwt/me", name="mejwt")
     */
    public function jwtMeAction(Request $request)
    {
        $response = new Response();

        $response->setContent(json_encode(array(
            'username' => $this->getUser()->getUsername(),
            'request' => $request,
            'user' => $this->getUser()
        )));
        $response->headers->set('Content-Type', 'application/json');
        // replace this example code with whatever you need
        return $response;
    }
    /**
     * @Route("/api/me", name="meCookie")
     */
    public function apiMeAction(Request $request)
    {
        if($this->getUser())
        {
            $response = new JsonResponse(array(
                "id"        => $this->getUser()->getId(),
                "username"  => $this->getUser()->getUsername(),
                "roles"     => $this->getUser()->getRoles()
            ));
            return $response;
        }else
        {
            $response = new JsonResponse(array(
                "message"   => "It looks like you are not connected anymore"
            ));
            $response->setStatusCode(403);
            return $response;
        }
    }
}
