<?php
$BOT_TOKEN=getenv("BOT_TOKEN");
$ADMIN_ID=getenv("ADMIN_ID");

$u=json_decode(file_get_contents("php://input"),true);
$msg=$u['message']??null;
if(!$msg) exit;

$id=$msg['chat']['id'];
$text=trim($msg['text']);

function send($i,$t){
 global $BOT_TOKEN;
 file_get_contents("https://api.telegram.org/bot$BOT_TOKEN/sendMessage?chat_id=$i&text=".urlencode($t));
}

if($id!=$ADMIN_ID){send($id,"Admin only");exit;}

$db="vip_users.json";
$data=json_decode(file_get_contents($db),true);

/* addvip */
if(preg_match('/\/addvip\s+@(\w+)\s+(\d+)/',$text,$m)){
 $data[$m[1]]=["expire"=>time()+$m[2]*86400,"device"=>""];
 file_put_contents($db,json_encode($data));
 send($id,"✅ VIP Added @$m[1]");
}
/* remove */
elseif(preg_match('/\/remove\s+@(\w+)/',$text,$m)){
 unset($data[$m[1]]);
 file_put_contents($db,json_encode($data));
 send($id,"🗑️ Removed @$m[1]");
}
/* viplist */
elseif($text=="/viplist"){
 $out="👑 VIP LIST\n\n";
 foreach($data as $u=>$v){
  if($v['expire']>time())
   $out.="@$u — ".ceil(($v['expire']-time())/86400)." days\n";
 }
 send($id,$out);
}
