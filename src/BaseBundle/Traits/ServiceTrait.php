<?php

namespace BaseBundle\Traits;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\VarDumper\VarDumper;
use Doctrine\ORM\Proxy\Proxy;

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

    protected function saveEntity($entity)
    {
        $em = $this->getManager();
        $em->persist($entity);
        $em->flush($entity);
    }

    protected function getRealEntity($proxy)
    {
        if ($proxy instanceof Proxy) {
            $metadata              = $this->getManager()->getMetadataFactory()->getMetadataFor(get_class($proxy));
            $class                 = $metadata->getName();
            $entity                = new $class();
            $reflectionSourceClass = new \ReflectionClass($proxy);
            $reflectionTargetClass = new \ReflectionClass($entity);
            foreach ($metadata->getFieldNames() as $fieldName) {
                $reflectionPropertySource = $reflectionSourceClass->getProperty($fieldName);
                $reflectionPropertySource->setAccessible(true);
                $reflectionPropertyTarget = $reflectionTargetClass->getProperty($fieldName);
                $reflectionPropertyTarget->setAccessible(true);
                $reflectionPropertyTarget->setValue($entity, $reflectionPropertySource->getValue($proxy));
            }

            return $entity;
        }

        return $proxy;
    }
}
