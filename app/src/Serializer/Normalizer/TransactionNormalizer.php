<?php

namespace App\Serializer\Normalizer;

use App\Service\DateFormatter;
use App\Service\MoneyFormatter;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TransactionNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;
    private $moneyFormatter;
    private $dateFormatter;

    public function __construct(ObjectNormalizer $normalizer, MoneyFormatter $moneyFormatter, DateFormatter $dateFormatter)
    {
        $this->normalizer = $normalizer;
        $this->moneyFormatter = $moneyFormatter;
        $this->dateFormatter = $dateFormatter;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);
        $data['amount'] = $this->moneyFormatter->format($object->getAmount());
        $data['createdAt'] = $this->dateFormatter->format($object->getCreatedAt());
        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof \App\Entity\Transaction;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
