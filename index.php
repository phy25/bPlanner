<?php
header('Content-type: text/html; charset=utf8');
date_default_timezone_set('Asia/Shanghai');
require_once('./includes/functions.php');
require_once('./includes/schoolBITclass.php');
require_once('./includes/schoolBITAccountclass.php');

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'login'){
	if(empty($_POST['username']) || empty($_POST['password'])){
		$pagemsg = '请输入学号和密码。';
		$pagemsg_type = 'error';
	}else{
		$school = new schoolBIT((int) $_POST['username'], $_POST['password']);
		if(!$school->login()){
			if(strpos($school->last_error, 'I')===0){
				if($school->last_error == 'Inetwork_error'){
					$pagemsg = '无法连接教务系统。';
				}else if($school->last_error == 'Iservice_error'){
					$pagemsg = '教务系统错误，请重试。';
				}else if($school->last_error == 'Itimeout_error'){
					$pagemsg = '请求超时，请重试。';
				}else if($school->last_error == 'Iparse_error'){
					$pagemsg = '错误信息解析错误。';
				}else{
					$pagemsg = $school->last_error;
				}
			}else{
				$pagemsg = $school->last_error;
			}
			$pagemsg_type = 'error';
		}else{
			$tables = $school->getSchedulePage($_REQUEST['year'], $_REQUEST['term']); // '2014-2015', 2
			
			if($tables){
				$preData = '$tables_s = \''.serialize($tables). "';\n\n";
				$t1 = $school->parseScheduleTableMain($tables[1]);
				$t2 = $school->parseScheduleTableChanges($tables[2]);
				
				$school->changesFillInfoFromMain($t2, $t1);
				

				$preData .= '$t_s = \''.json_encode(array(serialize($t1),serialize($t2)))."';";

				include('header.inc.php');
				include('showdata.inc.php');
				
				include('offline.inc.php');
				
				include('footer.inc.php');
				exit();
			}else{
				if(strpos($school->last_error, 'I')===0){
					if($school->last_error == 'Inetwork_error'){
						$pagemsg = '连接错误。';
					}else if($school->last_error == 'Iparse_error'){
						$pagemsg = '解析错误。';
					}else if($school->last_error == 'Itimeout_error'){
						$pagemsg = '请求超时，请重试。';
					}else if($school->last_error == 'Iservice_error'){
						$pagemsg = '教务系统错误。';
					}else{
						$pagemsg = $school->last_error;
					}
				}else{
					$pagemsg = $school->last_error;
				}
				$pagemsg_type = 'error';
			}
		}
	}
}

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'offline'){
	$school = new schoolBIT();
	include('header.inc.php');

	require_once('data.php');
	$tables = unserialize($tables_s);
	$t = json_decode($t_s);
	$t1 = unserialize($t[0]);
	$t2 = unserialize($t[1]);
	
	$week = 0;
	if(isset($_REQUEST['week'])){
		$week = (int) $_REQUEST['week'];
		if(!$week){
			$week = $school->getCurrentWeek()[0];
		}
	}

	include('offline.inc.php');
	include('footer.inc.php');
	exit();
}

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'ical'){
	require_once('vendor/iCalcreator.php');
	$school = new schoolBIT();
	include('header.inc.php');

	require_once('data.php');
	$tables = unserialize($tables_s);
	$t = json_decode($t_s);
	$t1 = unserialize($t[0]);
	$t2 = unserialize($t[1]);

	$ll = array();
	$llns = array();
	$ld = array();
	$schedArr = array();
	//$t2Merged = array();
	$t2MergedDeletion = array();

	foreach ($t2 as $v) {
	  // Merge itself
	  if($v instanceof LessonBITDeletion){
		$t2MergedDeletion = $school->mergeChangedLesson($t2MergedDeletion, $v, 1);
	  }
	  /*else{
		$t2Merged = $school->mergeChangedLesson($t2Merged, $v);
	  }*/

	  // Merge changes
	  $t1 = $school->mergeChangedLesson($t1, $v);
	}

	$school->mergeNearSchedule($t1);

	$config = array( "unique_id" => "bPlanner.phy25.com", "TZID" => "Asia/Shanghai");
	$v = new vcalendar( $config );                             // create a new calendar object instance

	$v->setProperty( "method", "PUBLISH" );                    // required of some calendar software
	$v->setProperty( "x-wr-calname", $tables[0]['stuname']."的课表 ".$tables[0]['year'].'-'.$tables[0]['term'] );      // required of some calendar software
	$v->setProperty( "X-WR-CALDESC", "" ); // required of some calendar software
	$v->setProperty( "X-WR-TIMEZONE", "Asia/Shanghai" );       // required of some calendar software
	foreach($t1 as $o){
	  $odata = array('name'=>$o->name, 'credit'=>$o->credit, 'tutor'=>$o->tutor, 'schedule'=>$o->schedule);
	  if(count($o->schedule)){
		foreach($o->schedule as $os){
			$startTimeTrans = array([],['8','00'],['8','50'],['9','50'],['10','40'],['11','30'],['13','20'],['14','10'],['15','10'],['16','00'],['16','50'],['18','30'],['19','20'],['20','10']);
			$endTimeTrans = array([],['8','45'],['9','35'],['10','35'],['11','25'],['12','15'],['14','05'],['14','55'],['15','55'],['16','45'],['17','35'],['19','15'],['20','05'],['20','55']);
			$dateArr = $school->getSchoolCalendar2Date($tables[0]['year'], $tables[0]['term'], $os->week[0], $os->day);
			
			$vevent = $v->newComponent( "vevent" );                    // create an event calendar component
			$vevent->setProperty( "dtstart", array( "year"  => $dateArr[0]
												  , "month" => $dateArr[1]
												  , "day"   => $dateArr[2]
												  , "hour"  => $startTimeTrans[$os->startTime][0]
												  , "min"   => $startTimeTrans[$os->startTime][1]
												  , "sec"   => 0 ));
			$vevent->setProperty( "dtend",   array( "year"  => $dateArr[0]
												  , "month" => $dateArr[1]
												  , "day"   => $dateArr[2]
												  , "hour"  => $endTimeTrans[$os->startTime][0]
												  , "min"   => $endTimeTrans[$os->startTime+$os->durationTime-1][1]
												  , "sec"   => 0 ));
			$vevent->setProperty( "LOCATION", $os->location );       // property name - case independent
			$vevent->setProperty( "summary", $o->name );
			$vevent->setProperty( "description", $o->tutor );

			if(count($os->week)>1){
				// Set recurrence rule
				$endWeek = $os->week[count($os->week)-1];
				$iWeek = $os->week[0];
				$weekdayTrans = array('', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU');
				$vevent->setProperty( "RRULE" , array(
					"FREQ" => "WEEKLY" , "COUNT" => $endWeek-$iWeek+1 , "INTERVAL" => 1 , "WKST" => $weekdayTrans[$os->day]
				));
				
				for(;$iWeek<$endWeek;$iWeek++){
					if(!in_array($iWeek, $os->week)){
						$dateArr = $school->getSchoolCalendar2Date($tables[0]['year'], $tables[0]['term'], $iWeek, $os->day);
						$vevent->setProperty( "EXDATE", array( array( $dateArr[0], $dateArr[1], $dateArr[2], $startTimeTrans[$os->startTime][0], $startTimeTrans[$os->startTime][1], 0 )));
					}
				}
			}

			//$schedArr[$os->day][$os->startTime][] = $os;
		}
	  }
	}
	
	$preData = $v->createCalendar();
	include('showdata.inc.php');
	include('footer.inc.php');
	exit();
}

include('header.inc.php');
include('login.inc.php');
include('footer.inc.php');