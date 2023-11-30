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

namespace BaksDev\Reference\Cars\EntityListeners\Brand;

use BaksDev\Core\Type\Ip\IpAddress;
use BaksDev\Reference\Cars\Entity\Brand\Modify\CarsBrandModify;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CarsBrandModifyListener
{
    private RequestStack $request;
    private TokenStorageInterface $token;
    
    public function __construct(
      RequestStack $request,
      TokenStorageInterface $token,
    )
    {
        $this->request = $request;
        $this->token = $token;
    }
    
    public function prePersist(CarsBrandModify $data, LifecycleEventArgs $event) : void
    {
        $token = $this->token->getToken();
        
        if($token)
        {
            $data->setUsr($token->getUser());
        }
        
        /* Если пользователь не из консоли */
        if($this->request->getCurrentRequest())
        {
            
            $data->upModifyAgent(
              new IpAddress($this->request->getCurrentRequest()->getClientIp()), /* Ip */
              $this->request->getCurrentRequest()->headers->get('User-Agent') /* User-Agent */
            );
        }
    }
    
}