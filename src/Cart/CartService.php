<?php

namespace App\Cart;

use App\Cart\CartItem;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        $this->session->set('cart', $cart);
    }

    public function empty()
    {
        // Aura pour effet de vider le panier dans la session
        $this->saveCart([]);
    }

    public function add(int $id)
    {
        // 1. Retrouve le panier dans la session (sous forme de tableau)
        // 2. Si il n'existe pas encore, alors prendre un tableau vide
        $cart = $this->getCart();

        // 3. Si le produit n'existe pas il est ajouter avec une quantité à 0
        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }

        // 4. Augmente la quantité du produit +1
        $cart[$id]++;

        // 5. Enregistrer le tableau mis à jour dans la session
        $this->saveCart($cart);
    }

    public function remove(int $id)
    {
        $cart = $this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }

        // Soit le produit est à 1 alors il faut simplement le supprimer
        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }

        // Soit le produit est à plus de 1, alors il faut le décrémenter
        $cart[$id]--;

        $this->saveCart($cart);
    }

    public function getTotal(): Int
    {
        $total = 0;

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $total += $product->getPrice() * $qty;
        }

        return $total;
    }

    /**
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array
    {
        $detailedCart = [];

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }

        return $detailedCart;
    }
}
