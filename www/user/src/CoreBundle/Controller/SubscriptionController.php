<?php

namespace CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Token;

class SubscriptionController extends Controller
{
    /**
     * @Route("/offers", name="offers")
     */
    public function offersAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/offers.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
    /**
     * @Route("/offers/subscribe", name="subscribe")
     * @Method({"GET", "POST"})
     */
    public function subscribeAction(Request $request){
        
        //chercher symfony > forms
        $pseudo = $request->request->get('pseudo');
        $email  = $request->request->get('email');
        $cn     = $request->request->get('card-number');
        $expire = $request->request->get('card-expire-month') . "/" . $request->request->get('card-expire-year');
        $expire_month   = $request->request->get('card-expire-month');
        $expire_year    = $request->request->get('card-expire-year');
        $cvc    = $request->request->get('card-cvc');
        $ch     = $request->request->get('card-holder');
        $plan   = $request->request->get('plan');
        
        $cardInfo = array(
            "pseudo"        => $pseudo,
            "email"         => $email,
            'card-number'   => $cn,
            'card-expire'   => $expire,
            'card-expire-month' => $expire_month,
            'card-expire-year'  => $expire_year,
            'card-cvc'      => $cvc,
            'card-holder'   => $ch,
            'plan'          => $plan
        );
        
        $cardErrors = $this->checkErrors($cardInfo);
        if($cardErrors)
        {
            $response = new JsonResponse($cardErrors);
            $response->setStatusCode(400);
            return $response;
        }
        
        try{
            $stripeRegisterCustomer = $this->registerCard($cardInfo);
        } catch (Exception $ex) {
            error_log($ex);
            $response = new JsonResponse(array("message"=>"merde"));
            $response->setStatusCode(403);
            return $response;
        }
        
        if($stripeRegisterCustomer && isset($stripeRegisterCustomer["object"]) && $stripeRegisterCustomer["object"] === "customer")
        {
            $cusId = $stripeRegisterCustomer["id"]; //to save
        }else
        {
            $cusId = 0;
        }
        
        $response = new Response();
        $response->setContent(json_encode(array(
            "cusId"         => $cusId,
            "cardInfo"      => $cardInfo,
            "stripeStatus"  => $stripeRegisterCustomer
        )));
        if(!$cusId)
        {
            $response->setStatusCode(400);
        }
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /**
     * @Route("/my-subscriptions", name="my-subscriptions")
     */
    public function mySubscriptionsAction(Request $request){
        
        if($this->getUser())
        {
            $response = new Response();
            $response->setContent(json_encode(array(
                "you are" => $this->getUser()->getId(),
                "plan" => "normal"
            )));
            $response->headers->set('Content-Type', 'application/json');
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
    private function registerCard($cardInfo){
//        https://stripe.com/docs/api/php
//        https://stripe.com/docs/api/php#create_customer
//        https://stripe.com/docs/subscriptions/subscribing-customers
        Stripe::setApiKey("sk_test_gVtc7rbsbgJxVPW2KwV5XMxS");
//        $retrive = Charge::retrieve("ch_18ugP5BcamaAYw0etfDcE54p");
//        return $retrive;
        try{
            
            $token = Token::create(
                array(
                    "card" => array(
                        "name" => $cardInfo['card-holder'],
                        "number" => $cardInfo['card-number'],
                        "exp_month" => $cardInfo['card-expire-month'],
                        "exp_year" => $cardInfo['card-expire-year'],
                        "cvc" => $cardInfo['card-cvc']
                    )
                )
            );
        } catch(\Stripe\Error\ApiConnection $ex){
            return array(
                "type"  => "token error",
                "error" => $ex
            );
        }catch (Exception $ex) {
            return array(
                "type"  => "token error",
                "error" => $ex
            );
        }
        
        if($token && $token["id"])
        {
            $tokenId = $token["id"];
        }else
        {
            return $token;
        }
        
        try
        {
            $customer = \Stripe\Customer::create(array(
                "source" => $tokenId, // obtained from Stripe.js
                "plan" => $cardInfo['plan'],
                "email" => $cardInfo['email']
            ));
        } catch (Exception $ex) {
            return array(
                "type"  => "customer error",
                "error" => $ex
            );
        }
        return $customer;
    }
    private function checkErrors($cardInfo){
        return false;
    }
}

/*
{"stripeStatus":{"id":"cus_9NfL80Jck25mmJ","object":"customer","account_balance":0,"created":1476545019,"currency":"eur","default_source":"card_194u0cBcamaAYw0ee8DLBwOg","delinquent":false,"description":null,"discount":null,"email":"antoine.duwab+mypseudo@gmail.com","livemode":false,"metadata":[],"shipping":null,"sources":{"object":"list","data":[{"id":"card_194u0cBcamaAYw0ee8DLBwOg","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":"cus_9NfL80Jck25mmJ","cvc_check":"pass","dynamic_last4":null,"exp_month":6,"exp_year":2018,"fingerprint":"gH0cMhbMwaF4V3ms","funding":"credit","last4":"4242","metadata":[],"name":"Jean-Micheng Dard","tokenization_method":null}],"has_more":false,"total_count":1,"url":"\/v1\/customers\/cus_9NfL80Jck25mmJ\/sources"},"subscriptions":{"object":"list","data":[{"id":"sub_9NfL2dAd7HeBZn","object":"subscription","application_fee_percent":null,"cancel_at_period_end":false,"canceled_at":null,"created":1476545019,"current_period_end":1479223419,"current_period_start":1476545019,"customer":"cus_9NfL80Jck25mmJ","discount":null,"ended_at":null,"livemode":false,"metadata":[],"plan":{"id":"normal-plan","object":"plan","amount":1000,"created":1476537940,"currency":"eur","interval":"month","interval_count":1,"livemode":false,"metadata":[],"name":"Plan Normal","statement_descriptor":null,"trial_period_days":null},"quantity":1,"start":1476545019,"status":"active","tax_percent":null,"trial_end":null,"trial_start":null}],"has_more":false,"total_count":1,"url":"\/v1\/customers\/cus_9NfL80Jck25mmJ\/subscriptions"}}}
 */