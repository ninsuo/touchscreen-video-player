<?php

namespace BaseBundle\Traits;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\VarDumper\VarDumper;

trait ServiceTrait
{
    use ContainerAwareTrait;

    protected function get($service)
    {
        return $this->container->get($service);
    }

    protected function getParameter($parameter)
    {
        return $this->container->getParameter($parameter);
    }

    protected function dump($var)
    {
        VarDumper::dump($var);
    }

    protected function trans($property, array $parameters = [])
    {
        return $this->container->get('translator')->trans($property, $parameters);
    }

    protected function isGranted($attributes, $object = null)
    {
        return $this->container->get('security.authorization_checker')->isGranted($attributes, $object);
    }

    protected function getManager($manager = null)
    {
        $em = $this
           ->get('doctrine')
           ->getManager()
        ;

        if (!is_null($manager)) {
            return $em->getRepository($manager);
        }

        return $em;
    }

    protected function getEntityById($manager, $id)
    {
        $em     = $this->getManager($manager);
        $entity = $em->findOneById($id);

        if (!$entity) {
            throw $this->createNotFoundException();
        }

        return $entity;
    }
}
