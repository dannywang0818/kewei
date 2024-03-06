<?php

namespace Magento\Checkout\Model\Kewei;

use Magento\Checkout\Api\KeweiCartInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Checkout\Model\Cart\RequestQuantityProcessor;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class CartManagement implements KeweiCartInterface
{


    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CustomerCart
     */
    protected $cart;


    /**
     * @var RequestQuantityProcessor
     */
    private $quantityProcessor;

    /**
     * @param CustomerCart $cart
     * @param RequestQuantityProcessor $quantityProcessor
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        CustomerCart $cart,
        RequestQuantityProcessor $quantityProcessor = null,
        ?LoggerInterface                            $logger = null,
    )
    {
        $this->cart = $cart;
        $this->quantityProcessor = $quantityProcessor ?? ObjectManager::getInstance()->get(RequestQuantityProcessor::class);
        $this->logger = $logger ?? ObjectManager::getInstance()->get(LoggerInterface::class);
    }

    /**
     * @inheritdoc
     *
     */
    public function save($cartId, \Magento\Quote\Api\Data\CartInterface $cart)
    {

        try {
            if (!$cart) {

                $cartItems = $cart->getItems();
                $cartData = array();
                foreach ($cartItems as $value) {
                    $cartData[$value->getItemId()]['qty'] = $value->getQty();
                }

                return true;
            }
            $cartItems = $cart->getItems();
            $cartData = array();
            foreach ($cartItems as $value) {
                $cartData[$value->getItemId()]['qty'] = $value->getQty();
            }

            $this->updateShoppingCart($cartData);

            return true;


//            if (is_array($cartData)) {
//                if (!$this->cart->getCustomerSession()->getCustomerId() && $this->cart->getQuote()->getCustomerId()) {
//                    $this->cart->getQuote()->setCustomerId(null);
//                }
//                $cartData = $this->quantityProcessor->process($cartData);
//                $cartData = $this->cart->suggestItemsQty($cartData);
//                $this->cart->updateItems($cartData)->save();
//            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
//            $this->messageManager->addErrorMessage(
//                $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage())
//            );
        } catch (\Exception $e) {
//            $this->messageManager->addExceptionMessage($e, __('We can\'t update the shopping cart.'));
//            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }

        /*$quote = $this->quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        return $this->getShippingMethods($quote, $address);*/
    }


    /**
     * @inheritdoc
     *
     */
    public function updateShoppingCart(array $cartData)
    {
        try {
            if (is_array($cartData)) {
                if (!$this->cart->getCustomerSession()->getCustomerId() && $this->cart->getQuote()->getCustomerId()) {
                    $this->cart->getQuote()->setCustomerId(null);
                }
                $cartData = $this->quantityProcessor->process($cartData);
                $cartData = $this->cart->suggestItemsQty($cartData);
                $this->cart->updateItems($cartData)->save();
            }
        } catch (LocalizedException $e) {
            $this->logger->critical(
                'Update shopping cart is failed' . $e->getMessage()
            );
            throw new CouldNotSaveException(
                __($e->getMessage()),
                $e
            );
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new CouldNotSaveException(
                __('A server error stopped your cart from being updated. Please try to update your cart again.'),
                $e
            );
        }
    }
}
