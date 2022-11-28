<?php

/*
 * This file is part of the GearmanUI package.
 *
 * (c) Rodolfo Ripado <ggaspaio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GearmanUI;

use Silex\Application,
    Silex\ServiceProviderInterface,
    Net_Gearman_Manager;

class GearmanFacadeProvider implements ServiceProviderInterface
{
    public function register(Application $app) {
        $app['gearman.manager'] = $app->protect(function ($server_adress) {
            return new \Net_Gearman_Manager($server_adress);
        });

        $app['gearman.serverInfo'] = $app->share(function() use ($app) {
            return new GearmanFacade(
                $this->getServersConfig($app),
                $app['gearman.manager'],
                $app['monolog']);
        });
    }

    /**
     * @param \GearmanUI\GearmanUIApplication $app
     * @return array
     */
    private function getServersConfig($app)
    {
        $envServers = getenv('GEARMAN_SERVERS');
        if (!$envServers) {
            return $app['gearmanui.servers'];
        }

        $servers = [];
        foreach (explode(',', $envServers) as $item) {
            $exploded = explode(':', $item);
            $servers[] = [
                'name' => $exploded[0],
                'addr' => $exploded[1] . ':' . $exploded[2],
            ];
        }

       return $servers;
    }

    public function boot(Application $app) {}
}
