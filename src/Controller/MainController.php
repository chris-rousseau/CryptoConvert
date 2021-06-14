<?php

namespace App\Controller;

use Binance;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(): RedirectResponse
    {
        return $this->redirectToRoute('form_usd');
    }

    /**
     * @Route("/eur", name="form_eur", methods={"GET"})
     */
    public function formConvertEur(): Response
    {
        return $this->render('main/eur.html.twig', []);
    }

    /**
     * @Route("/eur", name="post_eur", methods={"POST"})
     */
    public function postConvertEur(Request $request): Response
    {
        // Connect API with API & secret keys
        require __DIR__ . '../../../config/apiKeys.php';
        $api = new Binance\API($apiK, $secretK);

        // Replace ',' to '.' & convert var to float
        $amount = str_replace(',', '.', $request->get('devise'));
        $amountEUR = floatval(filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));

        $oneCrypto = $api->price($request->get('crypto'));
        $oneEURinCrypto = 1 / floatval($oneCrypto);

        $crypto = str_replace('EUR', '', $request->get('crypto'));

        // On converti le nombre de l'user en Crypto
        $amountCrypto = number_format($amountEUR * $oneEURinCrypto, 6);

        return $this->render('main/eur.html.twig', [
            'amountCrypto' => $amountCrypto,
            'amountEUR' => $amountEUR,
            'crypto' => $crypto
        ]);
    }

    /**
     * @Route("/usd", name="form_usd", methods={"GET"})
     */
    public function formConvertUsd(): Response
    {
        return $this->render('main/usd.html.twig', []);
    }

    /**
     * @Route("/usd", name="post_usd", methods={"POST"})
     */
    public function postConvertUsd(Request $request): Response
    {
        // Connect API with API & secret keys
        require __DIR__ . '../../../config/apiKeys.php';
        $api = new Binance\API($apiK, $secretK);

        // Replace ',' to '.' & convert var to float
        $amount = str_replace(',', '.', $request->get('devise'));
        $amountUSD = floatval(filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));

        $oneCrypto = $api->price($request->get('crypto'));
        $oneUSDinCrypto = 1 / floatval($oneCrypto);

        $crypto = str_replace('USDT', '', $request->get('crypto'));

        // On converti le nombre de l'user en Crypto
        $amountCrypto = number_format($amountUSD * $oneUSDinCrypto, 6);

        return $this->render('main/usd.html.twig', [
            'amountCrypto' => $amountCrypto,
            'amountUSD' => $amountUSD,
            'crypto' => $crypto
        ]);
    }
}
