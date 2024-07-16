<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 引用插件主文件
require_once __DIR__ . '/Plugin.php';

class TeohVisit_Manage extends Typecho_Widget implements Widget_Interface_Do
{
    public function execute()
    {
        // 获取统计信息
        $stats = TeohVisit_Plugin::getAllStats();
        $this->displayStats($stats);
    }

    public function displayStats($stats)
    {
        echo '<h2>访客统计</h2>';
        echo '<table>';
        echo '<tr><th>统计周期</th><th>浏览次数</th><th>独立访客</th></tr>';
        foreach ($stats as $period => $data) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($period) . '</td>';
            echo '<td>' . htmlspecialchars($data['views']) . '</td>';
            echo '<td>' . htmlspecialchars($data['unique_visitors']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
}

// 执行管理页面
$manage = new TeohVisit_Manage();
$manage->execute();