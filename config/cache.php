        <?php
        return [
            // 驱动方式
            'type'   => 'redis',
            'host' => '127.0.0.1',
            'port' => 6379,
            'password'   => '',
            // 缓存保存目录
            'path'   => '../runtime/cache/',
            // 缓存前缀
            'prefix' => 'hm:',
            // 缓存有效期 0表示永久缓存
            'expire' => 600,
        ];