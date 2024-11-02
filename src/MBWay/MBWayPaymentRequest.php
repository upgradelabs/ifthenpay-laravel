<?php

namespace Upgradelabs\Ifthenpay\MBWay;

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Exception\InvalidSource;
use CuyZ\Valinor\Mapper\Source\JsonSource;
use CuyZ\Valinor\MapperBuilder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Upgradelabs\Ifthenpay\MBWay\Contracts\MBWayPayments;
use Upgradelabs\Ifthenpay\MBWay\DTO\MBWayPaymentRequestResponse;
use Upgradelabs\Ifthenpay\MBWay\Enums\MBWayPaymentRequestStatus;
use Upgradelabs\Ifthenpay\MBWay\Exceptions\IfThenPayMBWayApiException;
use Upgradelabs\Ifthenpay\MBWay\Models as MBWayPaymentRequestModel;

class MBWayPaymentRequest implements MBWayPayments
{
    private string $url = 'https://ifthenpay.com/api/spg/payment/mbway';

    public function __construct(
        private readonly int $order_id,
        private readonly string $amount,
        private readonly string $mobile_number,
        private readonly string $email = '',
    ) {}

    /**
     * @throws \Exception
     */
    public function send(): array
    {
        $data = [
            'mbWayKey' => config('ifthenpay-laravel.mbway.key'),
            'orderId' => $this->order_id,
            'amount' => $this->amount,
            'mobileNumber' => $this->mobile_number,
            'email' => $this->email,
            'description' => 'Order '.$this->order_id,
        ];

        $response = Http::post($this->url, $data)->body();

        try {
            //Validate response from MBWAY API
            $dto = (new MapperBuilder)->enableFlexibleCasting()->mapper()
                ->map(MBWayPaymentRequestResponse::class, new JsonSource($response));

            return match ($dto->Status) {
                MBWayPaymentRequestStatus::Success->value => $this->handleSuccess($dto),
                MBWayPaymentRequestStatus::CouldNotComplete->value => $this->send(), //The initialization request could not be completed. You can try again.
                MBWayPaymentRequestStatus::TransactionDeclined->value => $this->handleTransactionDeclined($dto),
                MBWayPaymentRequestStatus::Error->value => $this->handleError($dto),
                default => throw new IfThenPayMBWayApiException('Unknown status code, from MBWay API Payment Request'),
            };

        } catch (MappingError $error) {
            Log::error('MappingError', ['error' => $error->getMessage()]);
            $error_validation = $error->getMessage();
        } catch (InvalidSource $e) {
            Log::error('InvalidSource', ['error' => $e->getMessage()]);
            $error_validation = $e->getMessage();
        }

        return [
            'success' => false,
            'message' => $error_validation,
        ];

    }

    private function handleSuccess(MBWayPaymentRequestResponse $dto): array
    {
        $this->toDatabase($dto);

        return [
            'success' => true,
            'message' => $dto->Message,
            'data' => collect($dto)->toArray(),
        ];
    }

    private function handleTransactionDeclined(MBWayPaymentRequestResponse $dto): array
    {
        $this->toDatabase($dto);

        return [
            'success' => false,
            'message' => $dto->Message,
            'data' => collect($dto)->toArray(),
        ];
    }

    private function handleError(MBWayPaymentRequestResponse $dto): array
    {
        $this->toDatabase($dto);

        return [
            'success' => false,
            'message' => $dto->Message,
            'data' => collect($dto)->toArray(),
        ];
    }

    private function toDatabase(MBWayPaymentRequestResponse $dto): void
    {
        MBWayPaymentRequestModel::updateOrCreate(['request_id' => $dto->RequestId], [
            'amount' => $dto->Amount,
            'status' => $dto->Status,
            'message' => $dto->Message,
            'order_id' => $dto->OrderId,
        ]);
    }
}

/*
 * payload example:
 * {
    "mbWayKey": "ABC-123456",
    "orderId": "12345",
    "amount": "100.50",
    "mobileNumber": "351#912345678",
    "email": "",
    "description": "order 123456"
}

    * response example:
 * {
    "Amount": "33.61",
    "Message": "Pending",
    "OrderId": "1887",
    "RequestId": "i2szvoUfPYBMWdSxqO3n",
    "Status": "000"
}
 */
