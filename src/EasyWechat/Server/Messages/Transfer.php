<?php

/**
 * Transfer.php.
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

namespace EasyWeChat\Server\Messages;

use EasyWeChat\Message\Transfer as BaseTransfer;

/**
 * Class Transfer.
 *
 * @property string $to
 * @property string $account
 */
class Transfer extends BaseTransfer implements MessageInterface
{
    /**
     * 生成主动消息数组.
     */
    public function toStaff()
    {
        throw new Exception('转发类型不允许主动发送');
    }

    /**
     * 生成回复消息数组.
     */
    public function toReply()
    {
        $response = ['MsgType' => 'transfer_customer_service'];

        // 指定客服
        if (!empty($this->account) || !empty($this->to)) {
            $response['TransInfo'] = [
                                      'KfAccount' => $this->account ?: $this->to,
                                     ];
        }

        return $response;
    }
}//end class
