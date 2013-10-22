<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),


            new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
            new JordiLlonch\Bundle\CrudGeneratorBundle\JordiLlonchCrudGeneratorBundle(),
            new WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(),

            new RaulFraile\Bundle\LadybugBundle\RaulFraileLadybugBundle(),
            new Khepin\YamlFixturesBundle\KhepinYamlFixturesBundle(),
            new Avalanche\Bundle\ImagineBundle\AvalancheImagineBundle(),
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),

            new Lar\UtilsBundle\LarUtilsBundle(),
            new Lar\LarParametroBundle\LarParametroBundle(),
            new Lar\UsuarioBundle\UsuarioBundle(),

            new Caja\SistemaCajaBundle\SistemaCajaBundle(),
            new Common\AuditorBundle\CommonAuditorBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {

            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
