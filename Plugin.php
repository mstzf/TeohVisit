<?php
/**
 * TeohVisit Plugin
 *
 * @package TeohVisit
 * @version 1.0.0
 * @link https://blog.teohzy.com
 */

class TeohVisit_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->singleHandle = array('TeohVisit_Plugin', 'recordVisit');
        self::createTable();
        return _t('插件已经激活');
    }

    public static function deactivate()
    {
        return _t('插件已经禁用');
    }

    public static function config(Typecho_Widget_Helper_Form $form)
    {
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    public static function render()
    {
    }

    public static function recordVisit($archive)
    {
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        $today = date('Y-m-d');

        $ip = $_SERVER['REMOTE_ADDR'];
        $todayVisit = $db->fetchRow($db->select()->from($prefix . 'stat')->where('date = ?', $today));

        if ($todayVisit) {
            $views = $todayVisit['views'] + 1;
            $unique_visitors = $todayVisit['unique_visitors'];

            $visitor_count = $db->fetchRow($db->select(['COUNT(DISTINCT ip)' => 'unique_visitor_count'])->from($prefix . 'visitor')->where('date = ?', $today));
            
            $unique_visitors = $visitor_count['unique_visitor_count'] + 1;
            $db->query($db->update($prefix . 'stat')->rows(array('views' => $views, 'unique_visitors' => $unique_visitors))->where('date = ?', $today));
        } else {
            $db->query($db->insert($prefix . 'stat')->rows(array('date' => $today, 'views' => 1, 'unique_visitors' => 1)));
        }

        $db->query($db->insert($prefix . 'visitor')->rows(array('date' => $today, 'ip' => $ip)));
    }

    public static function createTable()
    {
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        $scripts = [
            "CREATE TABLE IF NOT EXISTS `{$prefix}stat` (
                `id` int(10) NOT NULL AUTO_INCREMENT,
                `date` date NOT NULL,
                `views` int(10) NOT NULL DEFAULT '1',
                `unique_visitors` int(10) NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`),
                UNIQUE KEY `date` (`date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
            "CREATE TABLE IF NOT EXISTS `{$prefix}visitor` (
                `id` int(10) NOT NULL AUTO_INCREMENT,
                `date` date NOT NULL,
                `ip` varchar(45) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        ];

        foreach ($scripts as $script) {
            $db->query($script);
        }
    }

    public static function getStat($period)
    {
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();

        switch ($period) {
            case 'today':
                $date = date('Y-m-d');
                break;
            case 'yesterday':
                $date = date('Y-m-d', strtotime('-1 day'));
                break;
            case 'month':
                $date = date('Y-m-01');
                $next_date = date('Y-m-01', strtotime('+1 month'));
                break;
            case 'total':
                break;
            default:
                return [];
        }

        if ($period == 'month') {
            $row = $db->fetchRow($db->select(['SUM(views)' => 'total_views'])->from($prefix . 'stat')->where('date >= ? AND date < ?', $date, $next_date));
        } elseif ($period == 'total') {
            $row = $db->fetchRow($db->select(['SUM(views)' => 'total_views'])->from($prefix . 'stat'));
        } else {
            $row = $db->fetchRow($db->select()->from($prefix . 'stat')->where('date = ?', $date));
        }

        if (!$row) {
            $row = ['views' => 0, 'unique_visitors' => 0];
        }

        return $row;
    }

    public static function getAllStats()
    {
        return [
            'today' => self::getStat('today'),
            'yesterday' => self::getStat('yesterday'),
            'month' => self::getStat('month'),
            'total' => self::getStat('total')
        ];
    }

    public static function isPluginEnabled()
    {
        // 检查插件是否启用的逻辑，可以根据具体情况调整
        return true; // 假设插件已启用
    }
}