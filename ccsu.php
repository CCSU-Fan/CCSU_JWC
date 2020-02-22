<?php
error_reporting(0);
$u = $_GET["u"];
$p = $_GET["p"];
$kksj=$_GET["term"];
$type=$_GET["type"];
if($u == null)exit;
if($p == null)exit;
if($kksj == null)exit;
if($type == null)exit;
function login($url, $post, $headers, $cookie){ 
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	//curl_setopt($ch, CURLOPT_COOKIE, $cookie);//$cookie格式 x=1;y=2
	curl_setopt($ch, CURLOPT_POST, 1);//post方式提交 
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));//要提交的信息 
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie); //设置Cookie信息保存在指定的文件中 
    $rs = curl_exec($ch);
    curl_close($ch); 
    return $rs; 
} 
function get_cj($url, $post, $cookie){ 
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //读取cookie 
	curl_setopt($ch, CURLOPT_POST, 1);//post方式提交 
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));//要提交的信息 
    $rs = curl_exec($ch);
    curl_close($ch); 
    return $rs; 
} 
function get_kb($url, $cookie){ 
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //读取cookie 
	//curl_setopt($ch, CURLOPT_POST, 1);//post方式提交 
   // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));//要提交的信息 
    $rs = curl_exec($ch);
    curl_close($ch); 
    return $rs; 
}

$headers = array(
	'Cache-Control: max-age=0',
	'Content-Type: application/x-www-form-urlencoded',
	'Connection: Keep-Alive',
	'User-Agent: android-async-http/1.4.1 (http://loopj.com/android-async-http)',
	'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36',
	'Cookie2: $Version=1',
	'Accept-Encoding: gzip',
);


$post_login = array(
    'USERNAME'=> $u, 
    'PASSWORD'=> $p,
	'useDogCode' => '',
	'x'=> 34,
	'y'=> 8
);


$cookie = dirname(__FILE__) . '/cookie_ccsu.txt'; 
$url = "http://jwcxxcx.ccsu.cn/jwxt/Logon.do?method=logon";    
$login = login($url, $post_login, $headers, $cookie);
if(strpos($login,'该帐号不存在或密码错误') !==false){//登陆判断
	$marks = array("msg"=>"该帐号不存在或密码错误");
	echo json_encode($marks,JSON_UNESCAPED_UNICODE);
	exit;
}
else{
	
	$msg="登陆成功"; 
}



if($type=='cj'){
$post_kb = array(
    'kksj'=> $kksj,
    'kcxz'=> '',
	'kcmc' => '',
	'xsfs'=> '',
	'zhcj'=> '',
	'kcdl'=> '',
	'kssj'=> '',
	'ok'=> '',
);
//kksj=2019-2020-1&kcxz=&kcmc=&xsfs=zhcj&kcdl=&kssj=&ok=
$get_cj = get_cj('http://jwcxxcx.ccsu.cn/jwxt/xszqcjglAction.do?method=queryxscj', $post_kb, $cookie);

$kclb = strstr($get_cj, 'tblHeadDiv');
$kclb=strstr($kclb, 'printHQL', true);
$kclb = strstr($kclb, '向下移动记录');
$kclb = strstr($kclb, 'table-layout');
$kclb=mb_substr($kclb, 21, null);//截取最终列表

$course_arr = array(
	
);
$cj_arr = array(
	
);

$lb_arr = explode("</tr>",$kclb);
$count_lb = count($lb_arr);//计算课程数量
$count_lb=$count_lb-1;
//echo $count_lb;

$xm_arr = explode("</td>",$lb_arr[0]);
$xm = strstr($xm_arr[3], '>');//截取姓名
$xm=mb_substr($xm, 1, null);



for ($i = 0; $i < $count_lb; $i++){
$data_arr = explode("</td>",$lb_arr[$i]);
$kcmc = strstr($data_arr[5], '>');//截取课程名称
$kcmc=mb_substr($kcmc, 1, null);
$course_arr[$i]=$kcmc;

$score = strstr($data_arr[6], '>');//截取课程成绩
$score=mb_substr($score, 1, null);
$cj_arr[$i]=$score;//成绩
}

$marks = array("xm"=>$xm,"kksj"=>$kksj,"msg"=>$msg,"type"=>$type,"name"=>$course_arr,"score"=>$cj_arr);
echo json_encode($marks,JSON_UNESCAPED_UNICODE);
}

elseif($type=='kb'){
$kb_url="http://jwcxxcx.ccsu.cn/jwxt/tkglAction.do?method=goListKbByXs&istsxx=no&xnxqh=$kksj&zc=&xs0101id=$u";
$get_kb=get_kb($kb_url, $cookie);
echo $get_kb;
}
else{
	$marks = array("msg"=>"无该接口");
	echo json_encode($marks,JSON_UNESCAPED_UNICODE);
	exit;
}

