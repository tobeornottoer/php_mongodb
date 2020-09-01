# php_mongodb
php mongodb操作类
找了很久，找不到合适用的（有的都是旧版mongo的,不是mongoDb的，吐血~），硬着头皮在2G的网络翻遍了不太好看的PHP官方文档，写了个简单的操作类，测试也简单地测试了一下，其他更多的操作和功能，还是得去翻官方文档...
~~~
$db = MongoDB::getInstance([
   "type" => "mongodb",
   "host" => "127.0.0.1",
   "port" => "27017",
   "db" => "db",
   "user" => "",
   "password" => ""
]);
//查询
$result = $db->collection("message")->Where(["time"=>['$lte'=>1598864449]])->sort("time",1)->field("from,type,time",false)->find();
//写入
$result = $db->collection("message")->insert([
   "from" => "a",
   "type" => "write",
   "content" => "哈哈",
   "time" => time(),
]);
//更新
$result = $db->collection("message")->where(["from"=>"a"])->update(["type"=>"ssd"]);
//删除
$result = $db->collection("message")->where(["from"=>"a"])->delete();
//aggregate 聚合
$result = $db->collection("message")->aggregate([
   ['$match'=>['time'=>['$gte'=>1598955498]]],
   ['$group' => ["_id"=>'$time', "total" => ['$sum' => 1]]]
]);
~~~
