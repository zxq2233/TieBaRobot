<?php
if (!defined('IS_GIUEM')) exit();
function talk($content,$name,$api,$apikey='') {
	$content = preg_replace("/@{$name}\s*?|回复(\s|@)*?{$name}\s*?(:|：)/i",'',$content);
	$content = urlencode($content);
	$re = '';
	switch ($api){
		case 'xiaoji':
			$re = xiaoji($content);
			break;
		case 'xiaoi3':
			$re = xiaoi3($content);
			break;
		case 'tuling':
			$re = tuling($content,$apikey);
			break;
		case 'simsimi3':
			$re = simsimi3($content);
			break;
		case 'simsimi':
			$re = simsimi($content);
			break;
		case 'baidurobot':
			$re = baidurobot($content,$apikey);
			break;
		default:
			$re = xiaoji($content);
	}
	return $re;
}
function baidurobot($content,$bduss){
	$cookies = "BDUSS={$bduss};";
	$content=urlencode($content);
	$re = fetch('https://sp0.baidu.com/yLsHczq6KgQFm2e88IuM_a/s?sample_name=bear_brain&request_query='.$content .'&bear_type=2',$cookies);
	$re = json_decode($re);
	
	
$content=$re->result_list[0]->result_content;
$content=json_decode($content);
$content=$content->answer;
$content=str_replace("小度","滑小稽",$content);
$content=str_replace("<AGE>","21",$content);
return $content;
}
function xiaoji($content){
	if(preg_match('/\[问：(.*?) 答：(.*?)\]/',$content,$teach) == 1){
		$re = teach($teach[1],$teach[2]);
		return $re;
	}else {
		$re = json_decode(fetch('http://lover.zhenyao.net/chat-g.json','','type=3&msg='.$content.'&score=true&botid=12056'),true);
		return $re['messages'][0]['message'];
	}
}
function teach($q,$a){
	$re = json_decode(HTTPclient('http://lover.zhenyao.net/robot/teach.json','','answer='.urlencode($a).'&botid=12056&question='.urlencode($q)),true);
	if($re['status'] == 1){
		return '调教成功！';
	}else {
		return '调教失败！';
	}
}
function tuling($content,$key){
	$re = json_decode(fetch('http://www.tuling123.com/openapi/api?key='.$key.'&info='.$content),true);
	$code = $re['code'];
	switch ($code){
		case 100000:
			$content = $re['text'];
			break;
		case 200000:
			$content = $re['text'].$re['url'];
			break;
		case 302000:
			$list = $re['list'];
			$i = rand(0,count($list)-1);
			$list = $list[$i];
			$content = $re['text'].'：'.$list['article'];
			break;
		case 305000:
			$list = $re['list'];
			$i = rand(0,count($list)-1);
			$list = $list[$i];
			$content = '起始站：'.$list['start'].'，到达站：'.$list['terminal'].'，开车时间：'.$list['starttime'].'，到达时间：'.$list['endtime'].'。亲，更多信息请上网查询哦！';
			break;
		case 306000:
			$list = $re['list'];
			$i = rand(0,count($list)-1);
			$list = $list[$i];
			$content = '航班：'.$list['flight'].'，航班路线：'.$list['route'].'，起飞时间：'.$list['starttime'].'，到达时间：'.$list['endtime'].'。亲，更多信息请上网查询哦！';
			break;
		case 310000:
			$list = $re['list'];
			$i = rand(0,count($list)-1);
			$list = $list[$i];
			$content = $list['info'].'中奖号码：'.$list['number'].'。亲，小赌怡情，大赌伤身哦！';
			break;
		case 40004:
			$content = '今天累了，明天再聊吧';
			break;
		default:
			//$content = xiaoji($content);
			$content = $re['text'];
	}
	return $content;
}
function simsimi3($content){
	$re = fetch('http://api.mrtimo.com/Simsimi.ashx?parm='.$content);
	return $re;
}
function simsimi($content){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,'http://www.simsimi.com/talk/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    $rs = curl_exec($ch);
    preg_match_all('/Set-Cookie: (.+)=(.+)$/m', $rs, $regs);
    foreach($regs[1] as $i=>$k);
    $cc=str_replace(' Path','' ,$k);
    $uid = rand();
    $cc='simsimi_uid='.$uid.';'.$cc;
    $re = fetch('http://www.simsimi.com/requestChat?lc=ch&ft=0.0&req='.$content.'&uid='.$uid,$cc);
    $re = json_decode($re,true);
    $re = $re['res'];
    if(stristr($re,'I HAVE NO RESPONSE'))
        $re = '你在说啥，我听不懂';
    return $re;
}
/*PHP END*/
