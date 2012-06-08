<?php

    /* This file is part of Wisdom.
     *
     * (c) 2012 Kevin Herrera
     *
     * For the full copyright and license information, please
     * view the LICENSE file that was distributed with this
     * source code.
     */

    namespace CODEAlchemy\Wisdom\Silex;

    use CODEAlchemy\Wisdom\Config\FileLocator,
        CODEAlchemy\Wisdom\Wisdom,
        Silex\Application,
        Silex\ServiceProviderInterface,
        Symfony\Component\Config\Loader\LoaderResolver;

    /**
     * A Silex service provider for Wisdom.
     *
     * @author Kevin Herrera <kherrera@codealchemy.com>
     */
    class Provider implements ServiceProviderInterface
    {
        /** {@inheritDoc} */
        public function boot(Application $app)
        {
        }

        /** {@inheritDoc} */
        public function register(Application $app)
        {
            $app['wisdom.options'] = array_merge(
                array(
                    'cache_path' => '',
                    'debug' => $app['debug'],
                    'loader' => array(
                        'CODEAlchemy\Wisdom\Loader\INI',
                        'CODEAlchemy\Wisdom\Loader\JSON',
                        'CODEAlchemy\Wisdom\Loader\YAML'
                    )
                ),
                isset($app['wisdom.options'])
                    ? $app['wisdom.options']
                    : array()
            );

            $app['wisdom'] = $app->share(function() use ($app)
            {
                $wisdom = new Wisdom($app['wisdom.path']);

                foreach ((array) $app['wisdom.options']['loader'] as $class)
                {
                    $wisdom->addLoader(new $class ($wisdom->getLocator()));
                }

                $wisdom->setCachePath($app['wisdom.options']['cache_path']);
                $wisdom->setDebug($app['wisdom.options']['debug']);

                return $wisdom;
            });
        }
    }