<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Reference\Cars\Type\Model\Type;

use BaksDev\Reference\Cars\Type\Model\Type\CarsModelClass\Collection\CarsModelClassInterface;
use InvalidArgumentException;

final class CarsModelClass
{

    public const string TYPE = 'car_model_class';

    private CarsModelClassInterface $class;

    public function __construct(CarsModelClassInterface|self|string $class)
    {
        if(is_string($class) && class_exists($class))
        {
            $instance = new $class();

            if($instance instanceof CarsModelClassInterface)
            {
                $this->class = $instance;
                return;
            }
        }

        if($class instanceof CarsModelClassInterface)
        {
            $this->class = $class;
            return;
        }

        if($class instanceof self)
        {
            $this->class = $class->getCarsModelClass();
            return;
        }

        /** @var CarsModelClassInterface $declare */
        foreach(self::getDeclared() as $declare)
        {
            if($declare::equals($class))
            {
                $this->class = new $declare;
                return;
            }
        }

        throw new InvalidArgumentException(sprintf('Not found Cars Model Class %s', $class));
    }

    public function getCarsModelClass(): CarsModelClassInterface
    {
        return $this->class;
    }

    public static function getDeclared(): array
    {
        return array_filter(
            get_declared_classes(),
            static function($className) {
                return in_array(CarsModelClassInterface::class, class_implements($className), true);
            }
        );
    }

    public function equals(mixed $class): bool
    {
        $class = new self($class);

        return $this->getCarsModelClassValue() === $class->getCarsModelClassValue();
    }

    public function getCarsModelClassValue(): string
    {
        return $this->class->getValue();
    }

    public static function cases(): array
    {
        $case = [];

        foreach(self::getDeclared() as $class)
        {
            /** @var CarsModelClassInterface $class */
            $class = new $class;
            $case[$class::sort()] = new self($class);
        }

        ksort($case);

        return $case;
    }

    public function __toString(): string
    {
        return $this->class->getvalue();
    }

}