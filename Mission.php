<?php
/**
 * @author Administrator
 * @date 2020/9/9 19:24
 * @desciption:
 */

namespace app\index\controller;


use think\Controller;

use think\MongoDb;
class Mission extends Controller
{

    public $heartbeat = 60;

    public $default_sort = ["sync_time"=>-1,"primary"=>-1];

    public function getMissionList(){

        $page = intval($this->request->get("page"))?:1;
        $limit = intval($this->request->get("limit"))?:10;
        //获取任务列表
        $list = MongoDb::table("mission")->order($this->default_sort)->limit(($page-1)*$limit,$limit)->select();
        $count = MongoDb::table("mission")->count();
        //获取服务状态
        $server = $this->getServiceStatus();
        return $this->echoJson(["list"=>$list?:[],"count"=>intval($count),"server"=>$server]);
    }

    public function report($addr=null){
        if(empty($addr)){
            return $this->echoJson("",500,"addr is empty");
        }
        $result = MongoDb::table('service')->where(["type"=>"status"])->update([$addr=>time()+$this->heartbeat]);
        if($result){
            return $this->echoJson("",0,"数据库更新成功");
        }else{
            return $this->echoJson("",500,"数据库更新失败");
        }
    }


    public function upSort($id=null){
        $sort_field = "primary";
        if(empty($id)){
            return $this->echoJson("",500,"id is empty");
        }
        $max_document = MongoDb::table("mission")->order($sort_field,"desc")->find()?:[];
        if(empty($max_document)){
            return $this->echoJson("",500,"max_document is empty");
        }
        if($max_document["id"] != $id){
            $result = MongoDb::table("mission")->where(["id"=>$id])->update([$sort_field=>$max_document[$sort_field]+1]);
        }else{
            $result = true;
        }
        if($result){
            return $this->echoJson("",0,"数据库更新成功");
        }else{
            return $this->echoJson("",500,"数据库更新失败");
        }
    }

    public function getServiceStatus(){
        $server["shenzhen"] = false;
        $server["xiamen"] = false;
        $service = MongoDb::table("service")->where(["type"=>"status"])->field("shenzhen,xiamen")->find();
        if(!empty($service)){
            $current = time();
            $server["shenzhen"] = ($current - $service["shenzhen"] > $this->heartbeat) ? false : true;
            $server["xiamen"] = ($current - $service["xiamen"] > $this->heartbeat) ? false : true;
        }
        return $server;
    }

    public function echoJson($data=[],$code=0,$msg=""){
        return json([
            "data" => $data,
            "code" => intval($code),
            "msg"  => $msg,
        ]);
    }

}