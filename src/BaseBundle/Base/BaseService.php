<?php

namespace BaseBundle\Base;

use BaseBundle\Traits\ServiceTrait;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

abstract class BaseService implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ServiceTrait;
}
