<?php

namespace Upgradelabs\Ifthenpay\MBWay\Requests;

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Exception\InvalidSource;
use CuyZ\Valinor\Mapper\Source\JsonSource;
use CuyZ\Valinor\MapperBuilder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Upgradelabs\Ifthenpay\MBWay\Abstract\MBWay;
use Upgradelabs\Ifthenpay\MBWay\Contracts\MBWayPayments;
use Upgradelabs\Ifthenpay\MBWay\DTO\MBWayPaymentRefundResponse;
use Upgradelabs\Ifthenpay\MBWay\Enums\MBWayPaymentRefundStatus;
use Upgradelabs\Ifthenpay\MBWay\Exceptions\IfThenPayMBWayApiException;
use Upgradelabs\Ifthenpay\MBWay\Models\MBWayRefundModel;

final class MBWayPaymentRefunds implements MBWayPayments
{
    private string $url = 'https://ifthenpay.com/api/endpoint/payments/refund';

    public function __construct(
        private readonly string $requestId,
        private readonly string $amount,
        private readonly int $order
    ) {}

    /**
     * @throws IfThenPayMBWayApiException
     */
    public function send(): array
    {
        $data = [
            'backofficekey' => config('ifthenpay-laravel.mbway.backoffice_key'),
            'requestId' => $this->requestId,
            'amount' => $this->amount,
        ];

        $response = Http::post($this->url, $data)->body();

        try {
            //Validate response from MBWAY API
            $dto = (new MapperBuilder)->enableFlexibleCasting()->mapper()
                ->map(MBWayPaymentRefundResponse::class, new JsonSource($response));

            return match ($dto->Code) {
                MBWayPaymentRefundStatus::Success->value => $this->handleSuccess($dto),
                MBWayPaymentRefundStatus::InsufficientFunds->value => $this->handleInsufficientFunds($dto),
                MBWayPaymentRefundStatus::Error->value => $this->handleError($dto),
                default => throw new IfThenPayMBWayApiException('Unknown status code, from MBWay API Refund Request'),

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

    private function handleSuccess(MBWayPaymentRefundResponse $dto): array
    {
        $this->toDatabase($dto);

        return [
            'success' => true,
            'message' => $dto->Message,
            'data' => collect($dto)->toArray(),
        ];

    }

    private function handleInsufficientFunds(MBWayPaymentRefundResponse $dto): array
    {
        $this->toDatabase($dto);

        return [
            'success' => false,
            'message' => $dto->Message,
            'data' => collect($dto)->toArray(),
        ];
    }

    private function handleError(MBWayPaymentRefundResponse $dto): array
    {
        $this->toDatabase($dto);

        return [
            'success' => false,
            'message' => $dto->Message,
            'data' => collect($dto)->toArray(),
        ];
    }

    private function toDatabase(MBWayPaymentRefundResponse $dto): void
    {
        MBWayRefundModel::create([
            'request_id' => $this->requestId,
            'order_id' => $this->order,
            'amount' => $this->amount,
            'code' => $dto->Code,
            'message' => $dto->Message,
            'user_id' => auth()->id(),
        ]);
    }
}

/*
 * {

     "backofficekey": "VOSSA_CHAVE_ACESSO_BACKOFFICE",

     "requestId": "ID_DO_PEDIDO_ATRIBUIDO_NA_TRANSACAO",

     "amount": "VALUE_TO_RETURN"

}
 */
