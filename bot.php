<?php
$data=json_decode(file_get_contents("php://input"),true);
$user=$data['user']??'';
$device=$data['device']??'';

$db="vip_users.json";
$list=json_decode(file_get_contents($db),true);

if(!isset($list[$user])){
  echo json_encode(["status"=>"error","msg"=>"❌ No VIP"]);
  exit;
}
if($list[$user]['expire']<time()){
  echo json_encode(["status"=>"error","msg"=>"⏳ VIP Expired"]);
  exit;
}
if($list[$user]['device']===""){
  $list[$user]['device']=$device;
  file_put_contents($db,json_encode($list));
}
if($list[$user]['device']!==$device){
  echo json_encode(["status"=>"error","msg"=>"🚫 Another device detected"]);
  exit;
}
echo json_encode(["status"=>"ok"]);
