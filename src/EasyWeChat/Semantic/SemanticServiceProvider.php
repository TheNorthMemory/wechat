<?php

/**
 * SemanticServiceProvider.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Semantic;

use EasyWeChat\Support\ServiceProvider;
use EasyWeChat\Core\Application;

/**
 * Class SemanticServiceProvider.
 */
class SemanticServiceProvider extends ServiceProvider
{
    /**
     * Register Server.
     *
     * @param Application $app
     *
     * @return mixed|void
     */
    public function register(Application $app)
    {
        $app->bind('semantic', function ($app) {
            return new Semantic($app->config['app_id'], $app['http']);
        });
    }
}
