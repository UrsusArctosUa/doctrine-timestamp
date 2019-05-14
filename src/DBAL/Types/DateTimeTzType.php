<?php
/**
 * Created by PhpStorm.
 * User: ursus
 * Date: 14.05.19
 * Time: 12:41
 */

namespace UrsusArctosUA\DoctrineTimestamp\DBAL\Types;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

class DateTimeTzType extends \Doctrine\DBAL\Types\DateTimeTzType
{
    public function canRequireSQLConversion()
    {
        return true;
    }

    /**
     * @inheritdoc
     *
     * @param \DateTime $value
     * @param AbstractPlatform $platform
     *
     * @return int
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($platform->getName() != 'mysql') {
            parent::convertToDatabaseValue($value, $platform);
        }

        if (!($value instanceof DateTimeInterface)) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $this->getName(),
                ['null', 'DateTime']
            );
        }

        return $value->getTimestamp();
    }

    /**
     * @inheritdoc
     *
     * @param string $sqlExpr
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        if ($platform->getName() != 'mysql') {
            parent::convertToDatabaseValueSQL($sqlExpr, $platform);
        }

        return 'FROM_UNIXTIME(' . $sqlExpr . ')';
    }

    /**
     * @inheritdoc
     *
     * @param mixed $value
     * @param AbstractPlatform $platform
     *
     * @return DateTimeInterface
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($platform->getName() != 'mysql') {
            return parent::convertToPHPValue($value, $platform);
        }

        if ($value === null || $value instanceof DateTimeInterface) {
            return $value;
        }

        $val = DateTime::createFromFormat('U', $value);

        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), 'U');
        }

        return $val;
    }

    /**
     * @inheritdoc
     *
     * @param string $sqlExpr
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        if ($platform->getName() != 'mysql') {
            return parent::convertToPHPValueSQL($sqlExpr, $platform);
        }

        return 'UNIX_TIMESTAMP(' . $sqlExpr . ')';
    }

    /**
     * @inheritdoc
     *
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        if ($platform->getName() != 'mysql') {
            return $platform->getDateTimeTzTypeDeclarationSQL($fieldDeclaration);
        }

        if (empty($fieldDeclaration['notnull'])) {
            return 'TIMESTAMP NULL';
        }

        if (empty($fieldDeclaration['default'])) {
            return 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP';
        }

        return 'TIMESTAMP';
    }
}