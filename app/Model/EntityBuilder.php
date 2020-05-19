<?php
namespace App\Model;

use MyCLabs\Enum\Enum;
use Nette\Database\IRow;
use Nette\Database\Row;
use Nette\Utils\DateTime;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

class EntityBuilder {
    public const USED_NAMESPACE = 'App\Model\\';

    /**
     * @param IRow $row
     * @param String $entityClass
     * @return AEntity
     * @throws ReflectionException
     */
    public static function createEntityFromDatabaseResult(IRow $row, String $entityClass):AEntity {
        $parameters = self::getEntityParameters($entityClass);
        $entity = new $entityClass;
        if(!$row instanceof Row){
            throw new RuntimeException('Wrong entity type');
        }
        foreach ($row->getIterator() as $key=>$value) {
            if(strpos($key, '_') !== false) {
                $fragments = explode('_', $key);
            } else {
                $fragments = [$key];
            }
               $currentProperty = $entity;
                $currentParameters = self::getEntityParameters($entityClass);
            foreach ($fragments as $i => $iValue) {
                $subEntity = $iValue;
                if($i === count($fragments)-1) {
                    if($currentParameters[$subEntity]['type'] === 'String'|| $currentParameters[$subEntity]['type'] === 'int' ||$currentParameters[$subEntity]['type'] === 'float') {
                        $currentProperty->$subEntity = $value;
                    } elseif ($currentParameters[$subEntity]['type'] === 'Enum') {
                        $currentProperty->$subEntity = new $currentParameters[$subEntity]['class']($value);
                    } elseif ($currentParameters[$subEntity]['type'] === 'DateTime') {
                        $currentProperty->$subEntity = $value;
                    } elseif ($currentParameters[$subEntity]['type'] === 'entity') {
                        $currentProperty->$subEntity = new $currentParameters[$subEntity]['class'](self::createEntityFromDatabaseResult($value, $currentParameters[$subEntity]['class']));
                    }
                    break;
                }
                    if ($currentProperty->$subEntity === null) {
                        $currentProperty->$subEntity = new $currentParameters[$subEntity]['class'];
                    }
                    $currentParameters = self::getEntityParameters($currentParameters[$subEntity]['class']);
                    $currentProperty = $currentProperty->$subEntity;
           }
        }
        return $entity;
    }

    /**
     * @param array $rowList
     * @param String $entityClass
     * @return array
     * @throws ReflectionException
     */
    public static function createEntityCollectionFromDatabaseResult(array $rowList, String $entityClass):array {
        $entityList = array();
        foreach ($rowList as $row) {
            $entityList[] = self::createEntityFromDatabaseResult($row, $entityClass);
        }
        return $entityList;
    }

    /**
     * @param String $class
     * @return array
     * @throws ReflectionException
     */
    public static function getEntityParameters(String $class):array {

        $parameters = array_keys(get_class_vars($class));
        $reflectionClass = new ReflectionClass($class);
        $outputParameters = [];
        foreach ($parameters as $parameter) {
            $property = $reflectionClass->getProperty($parameter);
            $comment = $property->getDocComment();
            $re = '/\/[\*]+\s\@var\s([a-zA-Z0-9\\\\\\\\]+)(<[a-zA-Z0-9]+>)?(\|null)?/m';
            preg_match_all($re, $comment, $matches, PREG_SET_ORDER, 0);
            if ($matches[0][1] === 'int') {
                $outputParameters[$parameter] = ['name' => $parameter, 'type' => 'int', 'class' => null];
            } elseif ($matches[0][1] === 'String') {
                $outputParameters[$parameter] = ['name' => $parameter, 'type' => 'String', 'class' => null];
            } elseif ($matches[0][1] === 'float') {
                $outputParameters[$parameter] = ['name' => $parameter, 'type' => 'float', 'class' => null];
            } elseif ($matches[0][1] === 'double') {
                $outputParameters[$parameter] = ['name' => $parameter, 'type' => 'double', 'class' => null];
            } elseif ($matches[0][1] === 'DateTime') {
                $outputParameters[$parameter] = ['name' => $parameter, 'type' => 'DateTime', 'class' => DateTime::class];
            } elseif (is_subclass_of(self::USED_NAMESPACE.$matches[0][1],Enum::class)) {
                $outputParameters[$parameter] = ['name' => $parameter, 'type' => 'Enum', 'class' => self::USED_NAMESPACE.$matches[0][1]];
            } else {
                $outputParameters[$parameter] = ['name' => $parameter, 'type' => 'entity', 'class' => self::USED_NAMESPACE.$matches[0][1]];
            }
        }
        return $outputParameters;
    }
}