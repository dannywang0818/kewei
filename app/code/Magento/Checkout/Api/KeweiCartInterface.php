<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Checkout\Api;

/**
 * Interface KeweiCartInterface
 * @api
 * @since 100.0.2
 */
interface KeweiCartInterface
{

    /**
     * Save quote at checkout page
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\CartInterface $cart
     * @return bool|void
     */
    public function save($cartId, \Magento\Quote\Api\Data\CartInterface $cart);

    /**
     * Update customer's shopping cart
     * @param array $cartData
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return void
     */
    public function updateShoppingCart(array $cartData);


}
