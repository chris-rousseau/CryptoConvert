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
    public function formEur(): Response
    {
        return $this->render('main/eur.html.twig', []);
    }

    /**
     * @Route("/eur", name="convert_eur", methods={"POST"})
     */
    public function convertEur(Request $request): Response
    {
        // Connection à l'API avec l'API & secret keys
        require __DIR__ . '../../../config/apiKeys.php';
        $api = new Binance\API($apiK, $secretK);

        // On remplace les virgules par des points & on converti la variable en float (optionnel)
        $amount = str_replace(',', '.', $request->get('devise'));
        $amountEUR = floatval(filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));

        // On récupère la valeur d'une crypto en euro et on calcule la valeur d'un euro en crypto
        $oneCrypto = $api->price($request->get('crypto'));
        $oneEURinCrypto = 1 / floatval($oneCrypto);

        // On converti le nombre de l'user en Crypto
        $amountCrypto = $amountEUR * $oneEURinCrypto;

        if ($amountCrypto < 1) {
            $amountCrypto = number_format($amountCrypto, 6);
        } else if ($amountCrypto >= 1) {
            $amountCrypto = number_format($amountCrypto, 2, '.', ' ');
        }

        // On récupère le nom et on le traite pour ne garder que le nom raccourci de la crypto (affichage après le résultat)
        $crypto = str_replace('EUR', '', $request->get('crypto'));

        return $this->render('main/eur.html.twig', [
            'amountCrypto' => $amountCrypto,
            'amountEUR' => number_format($amountEUR, 1, '.', ' '),
            'crypto' => $crypto
        ]);
    }

    /**
     * @Route("/usd", name="form_usd", methods={"GET"})
     */
    public function formUsd(): Response
    {
        return $this->render('main/usd.html.twig', []);
    }

    /**
     * @Route("/usd", name="convert_usd", methods={"POST"})
     */
    public function convertUsd(Request $request): Response
    {
        require __DIR__ . '../../../config/apiKeys.php';
        $api = new Binance\API($apiK, $secretK);

        $amount = str_replace(',', '.', $request->get('devise'));
        $amountUSD = floatval(filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));

        $oneCrypto = $api->price($request->get('crypto'));
        $oneUSDinCrypto = 1 / floatval($oneCrypto);

        $amountCrypto = $amountUSD * $oneUSDinCrypto;

        if ($amountCrypto < 1) {
            $amountCrypto = number_format($amountCrypto, 6);
        } else if ($amountCrypto >= 1) {
            $amountCrypto = number_format($amountCrypto, 2, '.', ' ');
        }

        $crypto = str_replace('USDT', '', $request->get('crypto'));

        return $this->render('main/usd.html.twig', [
            'amountCrypto' => $amountCrypto,
            'amountUSD' => number_format($amountUSD, 1, '.', ' '),
            'crypto' => $crypto
        ]);
    }
}
