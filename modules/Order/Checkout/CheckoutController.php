<?php

namespace Modules\Order\Checkout;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Modules\Order\Contracts\PendingPayment;
use Modules\Payment\Exceptions\PaymentFailedException;
use Modules\Payment\PaymentGateway;
use Modules\Product\CartItemCollection;
use Modules\User\UserDto;

class CheckoutController
{
    public function __construct(
        protected PurchaseItem $purchaseItem,
        protected PaymentGateway $paymentGateway,
    ) {}

    /**
     * @throws ValidationException
     */
    public function __invoke(CheckoutRequest $request): JsonResponse
    {
        $cartItems = CartItemCollection::fromCheckoutData($request->input('products'));;
        $pendingPayment = new PendingPayment($this->paymentGateway, $request->input('payment_token'));
        $userDto = UserDto::fromEloquentModel($request->user());

        try {
            $order = $this->purchaseItem->handle(
                $cartItems,
                $pendingPayment,
                $userDto,
            );
        }catch (PaymentFailedException $e) {
            throw ValidationException::withMessages([
                'payment_token' => [
                    'The given payment token is invalid.',
                ],
            ]);
        }

        return response()->json([
            'order_url' => $order->url,
        ], 201);
    }
}
