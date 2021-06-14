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
    public function postConvertEur(): Response
    {
        // Connect API with API & secret keys
        require __DIR__ . '../../../config/apiKeys.php';
        $api = new Binance\API($apiK, $secretK);

        // Replace ',' to '.' & convert var to float
        $amount = str_replace(',', '.', $_POST['devise']);
        $amountEUR = floatval(filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));

        $oneBTC = $api->price("BTCEUR");
        $oneEURinBTC = 1 / floatval($oneBTC);

        // On converti le nombre de l'user en BTC
        $amountBTC = number_format($amountEUR * $oneEURinBTC, 6);

        return $this->render('main/eur.html.twig', [
            'amountBTC' => $amountBTC,
            'amountEUR' => $amountEUR
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
    public function postConvertUsd(): Response
    {
        // Connect API with API & secret keys
        require __DIR__ . '../../../config/apiKeys.php';
        $api = new Binance\API($apiK, $secretK);

        // Replace ',' to '.' & convert var to float
        $amount = str_replace(',', '.', $_POST['devise']);
        $amountUSD = floatval(filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));

        $oneBTC = $api->price("BTCUSDT");
        $oneUSDinBTC = 1 / floatval($oneBTC);

        // On converti le nombre de l'user en BTC
        $amountBTC = number_format($amountUSD * $oneUSDinBTC, 6);

        return $this->render('main/usd.html.twig', [
            'amountBTC' => $amountBTC,
            'amountUSD' => $amountUSD
        ]);
    }
}
