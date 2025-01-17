<?php

namespace App\Service;

use App\Component\Builder\OrderBuilder;
use App\Component\Factory\EntityFactory;
use App\Component\Factory\SimpleResponseFactory;
use App\Component\Message\SendTransactionMessage;
use App\Component\Utils\Enum\OrderStatusEnum;
use App\Component\Utils\Postman;
use App\Dto\ControllerRequest\BaseRequest;
use App\Dto\ControllerRequest\CheckoutRequest;
use App\Dto\ControllerResponse\AcquiringResponse;
use App\Dto\ControllerResponse\OrderListResponse;
use App\Dto\ControllerResponse\SuccessResponse;
use App\Entity\Order;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderStatusRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderStatusRepository $orderStatusRepository,
        private readonly CartRepository $cartRepository,
        private readonly TransactionRepository $transactionRepository,
        private readonly OrderBuilder $orderBuilder,
        private readonly MessageBusInterface $bus
    ) {
    }

    public function checkout(CheckoutRequest $request): SuccessResponse
    {
        $cart = $this->cartRepository->getCartBySessionId($request->session, true);
        $order = $this->orderBuilder
            ->setCheckoutRequest($request)
            ->setCart($cart)
            ->build()
            ->getResult()
        ;

        $orderStatus = $this->orderStatusRepository->getOrderStatusByCode(OrderStatusEnum::NewOrder->value);
        $historyOrderStatus = EntityFactory::createHistoryOrderStatus($orderStatus);
        $order->addHistoryOrderStatus($historyOrderStatus);

        foreach ($cart->getCartProducts() as $cartProduct) {
            $orderProduct = EntityFactory::createOrderProduct($cartProduct->getProduct(), $cartProduct->getQuantity());
            $order->addOrderProduct($orderProduct);
        }

        $this->orderRepository->save($order, true);
        $this->cartRepository->remove($cart, true);
        Postman::getInstance()->dispatchHistoryOrderStatus($historyOrderStatus);

        return SimpleResponseFactory::createSuccessResponse(true);
    }

    public function createTransaction(Order $order): AcquiringResponse
    {
        //todo Тут можно прикрутить эквайринг
        //todo Пока создается фиктивная транзакция, которая порождает успешную оплату через сообщение в rabbit

        $paymentLink = 'http://localhost/api/doc';
        $transaction = EntityFactory::createTransaction($order);
        $transaction->setPaymentLink($paymentLink);
        $this->transactionRepository->save($transaction, true);

        $this->bus->dispatch(new SendTransactionMessage($transaction->getId()));

        return SimpleResponseFactory::createAcquiringResponse($paymentLink);
    }

    public function getOrderList(BaseRequest $request): OrderListResponse
    {
        $orders = $this->orderRepository->findBy(['sessionId' => $request->session]);

        return SimpleResponseFactory::createOrderListResponse($orders);
    }
}
