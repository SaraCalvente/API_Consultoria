<?php
namespace App\Shared\Infraestructure\Persistence;

use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class EmailType extends StringType
{
    public const NAME = 'email_vo';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?EmailValueObject
    {
        return $value !== null ? new EmailValueObject($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof EmailValueObject ? $value->value() : $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}