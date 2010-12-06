<?php

namespace Bundle\PaymentBundle\Controller;

use Bundle\PaymentBundle\PluginController\Result;

use Bundle\PaymentBundle\Plugin\Exception\ActionRequiredException;
use Bundle\PaymentBundle\Plugin\Exception\Action\VisitUrl;
use Bundle\PaymentBundle\Entity\ExtendedData;
use Bundle\PaymentBundle\Entity\PaymentInstruction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DemoController extends Controller
{
    public function indexAction()
    {
        $ppc = $this->container->get('payment.plugin_controller');
        
        $instruction = new PaymentInstruction(123, 'EUR', 'paypal_express_checkout', new ExtendedData());
        $ppc->createPaymentInstruction($instruction);
        
        $payment = $ppc->createPayment($instruction->getId(), 123);
        
        $result = $ppc->approve($payment->getId(), 123);
        if (Result::STATUS_PENDING === $result->getStatus()) {
            $ex = $result->getPluginException();
            if ($ex instanceof ActionRequiredException) {
                $action = $ex->getAction();
                
                if ($action instanceof VisitUrl) {
                    return $this->redirect($action->getUrl());
                }
                
                throw $ex;
            }
        }
        else if (Result::STATUS_SUCCESS !== $result->getStatus()) {
            // you can do your error processing here
            throw new \RuntimeException('Transaction was not successful.');
        }
        
        return $this->render('PaymentBundle:Demo:index.php');
    }
}