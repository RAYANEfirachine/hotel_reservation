<?php

namespace App\Service;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;

class PaymentService
{
    public function __construct(private PaymentRepository $repo, private EntityManagerInterface $em)
    {
    }

    public function recordPayment(Payment $payment): Payment
    {
        $payment->setPaymentDate(new \DateTimeImmutable());
        $this->em->persist($payment);
        $this->em->flush();

        return $payment;
    }


}
