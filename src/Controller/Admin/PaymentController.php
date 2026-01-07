<?php

namespace App\Controller\Admin;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/payments')]
class PaymentController extends AbstractController
{
    #[Route('', name: 'admin_payment_index')]
    public function index(PaymentRepository $paymentRepository): Response
    {
        $payments = $paymentRepository->findAll();

        return $this->render('admin/payment/index.html.twig', [
            'payments' => $payments,
        ]);
    }

    #[Route('/{id}', name: 'admin_payment_show')]
    public function show(Payment $payment): Response
    {
        return $this->render('admin/payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }
}
