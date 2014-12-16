<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 07/12/14
 * Time: 23:25
 */

namespace ISTI\Image\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ImageCompilerPass implements  CompilerPassInterface{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('isti.image.saver.manager')) {



            $definition = $container->getDefinition(
                'isti.image.saver.manager'
            );

            $taggedServices = $container->findTaggedServiceIds(
                'isti.image.saver'
            );

            foreach ($taggedServices as $id => $tags) {
                $definition->addMethodCall(
                    'addSaver',
                    array(new Reference($id))
                );
            }
        }
        if ($container->hasDefinition('isti.image.relation.provider_manager')) {

            $definition = $container->getDefinition(
                'isti.image.relation.provider_manager'
            );

            $taggedServices = $container->findTaggedServiceIds(
                'isti.image.relation_provider'
            );

            foreach ($taggedServices as $id => $tags) {
                $definition->addMethodCall(
                    'addRelationProvider',
                    array(new Reference($id))
                );
            }
        }
        if ($container->hasDefinition('isti.image.factory.imageinfo_manager')) {
            $definition = $container->getDefinition(
                'isti.image.factory.imageinfo_manager'
            );

            $taggedServices = $container->findTaggedServiceIds(
                'isti.image.factory'
            );

            foreach ($taggedServices as $id => $tags) {
                $definition->addMethodCall(
                    'addFactory',
                    array(new Reference($id))
                );
            }
        }

        if ($container->hasDefinition('isti.image.persist.persistence_manager')) {

            $definition = $container->getDefinition(
                'isti.image.persist.persistence_manager'
            );

            $taggedServices = $container->findTaggedServiceIds(
                'isti.image.path_repo'
            );

            foreach ($taggedServices as $id => $tags) {

                $definition->addMethodCall(
                    'addRepository',
                    array(new Reference($id))
                );
            }
        }

    }

} 