<?php

namespace Upgradelabs\Ifthenpay\MBWay;

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Exception\InvalidSource;
use CuyZ\Valinor\Mapper\Source\JsonSource;
use CuyZ\Valinor\MapperBuilder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Upgradelabs\Ifthenpay\MBWay\Contracts\MBWayPayments;
use Upgradelabs\Ifthenpay\MBWay\DTO\MBWayPaymentStatusResponse;
use Upgradelabs\Ifthenpay\MBWay\Enums\MBWayPaymentCheckStatus;
use Upgradelabs\Ifthenpay\MBWay\Exceptions\IfThenPayMBWayApiException;
use Upgradelabs\Ifthenpay\MBWay\Models\MBWayPaymentStatusModel;

class MBWayPaymentStatus implements MBWayPayments
{
    private string $url = 'https://ifthenpay.com/api/spg/payment/mbway/status';

    public function __construct(private readonly string $requestId) {}

    /**
     * @throws ConnectionException
     * @throws \Exception
     */
    public function send(): array
    {
        $response = Http::withUrlParameters([
            'endpoint' => $this->url,
            'mbWayKey' => config('ifthenpay-laravel.mbway.key'),
            'requestId' => $this->requestId,
        ])
            ->get('{+endpoint}?mbWayKey={mbWayKey}&requestId={requestId}')
            ->body();

        try {
            $dto = (new MapperBuilder)->enableFlexibleCasting()->mapper()
                ->map(MBWayPaymentStatusResponse::class, new JsonSource($response));

            return match ($dto->Status) {
                MBWayPaymentCheckStatus::Success->value => $this->handleSuccess($dto),
                MBWayPaymentCheckStatus::TransactionRejected->value => $this->handleTransactionRejected($dto),
                MBWayPaymentCheckStatus::TransactionDeclined->value => $this->handleTransactionDeclined($dto),
                MBWayPaymentCheckStatus::TransactionAwaitingPayment->value => $this->handleTransactionAwaiting($dto),
                MBWayPaymentCheckStatus::TransactionExpired->value => $this->handleTransactionExpired($dto),
                default => throw new IfThenPayMBWayApiException('Unknown status code, from MBWay Payment Status'),
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

    private function handleSuccess(MBWayPaymentStatusResponse $dto): array
    {
        $this->toDatabase($dto);

        return [
            'success' => true,
            'message' => $dto->Message,
            'data' => collect($dto)->toArray(),
        ];
    }

    private function handleTransactionRejected(MBWayPaymentStatusResponse $dto): array
    {
        $this->toDatabase($dto);

        return [
            'success' => false,
            'message' => $dto->Message,
            'data' => collect($dto)->toArray(),
        ];
    }

    private function handleTransactionDeclined(MBWayPaymentStatusResponse $dto): array
    {
        $this->toDatabase($dto);

        return [
            'success' => false,
            'message' => $dto->Message,
            'data' => collect($dto)->toArray(),
        ];
    }

    private function handleTransactionAwaiting(MBWayPaymentStatusResponse $dto): array
    {
        $this->toDatabase($dto);

        return [
            'success' => false,
            'message' => 'Awaiting Payment',
            'data' => collect($dto)->toArray(),
        ];
    }

    private function handleTransactionExpired(MBWayPaymentStatusResponse $dto): array
    {
        return [
            'success' => false,
            'message' => $dto->Message,
            'data' => collect($dto)->toArray(),
        ];
    }

    private function convertDate(string $timestamp): \DateTime
    {
        $format = 'd-m-Y H:i:s';

        return Carbon::createFromFormat($format, $timestamp)->toDateTime();
    }

    private function toDatabase(MBWayPaymentStatusResponse $dto): void
    {
        MBWayPaymentStatusModel::updateOrCreate(['request_id' => $dto->RequestId], [
            'status' => $dto->Status,
            'message' => $dto->Message,
            'created_at' => $this->convertDate($dto->CreatedAt),
            'updated_at' => $this->convertDate($dto->UpdateAt),
        ]);

    }
}
