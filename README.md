插件实现的功能非常简单，就只是记录了访问数据，和在需要的界面提供数据

## 安装

将代码克隆到typecho插件目录，在管理后台找到 `TeohVisit` 之间开启使用即可

```shell
git clone https://github.com/mstzf/TeohVisit.git
```

## 使用

具体使用需要修改主题源码，或者自定义独立界面模板

插件提供了两种读取数据的方法

`getStat`：返回所需要的单个值，具体值为：`today`,`yesterday`,`month`,`total`

`getAllStats`：返回全部数据

**示例：**

```php+HTML
if (TeohVisit_Plugin::isPluginEnabled()) {
    $stats = TeohVisit_Plugin::getAllStats();
    $data = [
        'today_unique_visitors' => $stats['today']['unique_visitors'],
        'today_views' => $stats['today']['views'],
        'yesterday_unique_visitors' => $stats['yesterday']['unique_visitors'],
        'yesterday_views' => $stats['yesterday']['views'],
        'month_total_views' => $stats['month']['total_views'],
        'total_total_views' => $stats['total']['total_views']
    ];
}
```

