<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmationController extends AbstractController
{

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer une commande")
     */
    public function confirm(Request $request, CartService $cartService, PurchasePersister $persister)
    {
        // 1. Nous voulons lire les données du formulaire
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);

        // 2. Si le formulaire n'a pas été soumis : dégager
        if (!$form->isSubmitted()) {
            // Message Flash puis redirection
            $this->addFlash('warning', "Vous devez remplir le formulaire de confirmation");
            return $this->redirectToRoute('cart_show');
        }

        // 4. Si il n'y a pas de produits dans mon panier : dégager
        $cartItems = $cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {
            $this->addFlash('warning', "Vous ne pouvez pas confirmer une commande avec un panier vide");
            return $this->redirectToRoute('cart_show');
        }

        // 5. Nous allons créer une purchase
        /** @var Purchase */
        $purchase = $form->getData();

        $persister->storePurchase($purchase);

        $cartService->empty();

        $this->addFlash('success', "La commande à bien été enregistrée");
        return $this->redirectToRoute('purchase_index');
    }
}
