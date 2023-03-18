<?php
/*
 * Copyright (c) 2023.  Baks.dev <admin@baks.dev>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace BaksDev\Reference\Cars\Type\Modification\Tires;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;


final class CarsModificationTiresUid //implements ValueResolverInterface
{
	public const TYPE = 'cars_modification_tires_uid';
	
	private Uuid $value;
	
	private ?string $option;
	
	public function __construct(AbstractUid|string|null $value = null, string $option = null)
	{
		if($value === null)
		{
			$value = Uuid::v7();
		} else if(is_string($value))
		{
			$value = new UuidV7($value);
		}
		
		$this->value = $value;
		$this->option = $option;
	}
	
	public function __toString() : string
	{
		return $this->value;
	}
	
	public function getValue() : AbstractUid
	{
		return $this->value;
	}
	
	public function getOption() : ?string
	{
		return $this->option;
	}
	
	public function equals(AbstractUid $uid) : bool
	{
		return (string) $this->value === (string) $uid;
	}
	
	public function resolve(Request $request, ArgumentMetadata $argument) : iterable
	{
		$argumentType = $argument->getType();
		
		if($argumentType !== self::class)
		{
			return [];
		}
		
		$value = $request->attributes->get($argument->getName()) ?:
			$request->attributes->get('id') ?:
				$request->get('id');
		
		return [new self($value)];
	}
}