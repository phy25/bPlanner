<?php
/*
Thanks to onChart
*/
class schoolBIT{
	protected $username = '';
	protected $password = '';
	protected $ch;
	protected $sessionPath = '';
	public $last_error = '';
	protected $calendarCache = array();
	protected static $weekLangArr = array('','一','二','三','四','五','六','日');
	protected $schedulePagePostViewState = '';
	protected $gradePagePostViewState = '';
	protected $examSchedulePagePostViewState = '';

	function __construct($username=null, $password=null){
		$this->setLoginInfo($username, $password);

		// Init curl handle
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/5.0 (rv:43.0) Gecko/20100101 Firefox/43.0 bPlanner@phy25.com" );
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt($this->ch, CURLOPT_ENCODING, "" );
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($this->ch, CURLOPT_AUTOREFERER, true );
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 3 );
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 7 );
		curl_setopt($this->ch, CURLOPT_MAXREDIRS, 3 );
		/*
		It seems that by copying $ch instance, cookies can be preserved in the page session level,
		thus there is no need to save cookies to file.
		*/
		// $temp = tempnam(sys_get_temp_dir(), 'jwc');
		// curl_setopt($this->ch, CURLOPT_COOKIEJAR, $temp);
		// curl_setopt($this->ch, CURLOPT_COOKIEFILE, $temp);

		// curl_setopt($this->ch, CURLOPT_PROXY, 'http://127.0.0.1:8888');
	}

	function __destruct(){
		curl_close($this->ch);
	}

	function setLoginInfo($username = null, $password = null) {
		if($username) $this->username = $username;
		if($password) $this->password = $password;
	}

	// @param $key 0: return 1=>Mon; 1: return Mon=>1
	static function getWeekLangArr($key = 0){
		return $key?array_flip(self::$weekLangArr):self::$weekLangArr;
	}

	protected function setSessionPath($path){
		$this->sessionPath = $path;
		curl_setopt($this->ch, CURLOPT_REFERER, $path.'/xs_main.aspx');
	}

	// Get new curl handle object with default settings
	protected function getCH(){
		return curl_copy_handle($this->ch);
	}

	function login(){
		if(!$this->username || !$this->password){
			$this->last_error = 'Ineeds_login_info';
			return false;
		}

		$ch = $this->getCH();
		curl_setopt($ch, CURLOPT_URL, "http://10.5.2.80/default2.aspx");
		curl_exec($ch);
		$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "__VIEWSTATE=dDwtMjEzNzcwMzMxNTs7Pj9pP88cTsuxYpAH69XV04GPpkse&TextBox1=".$this->username."&TextBox2=".urlencode($this->password)."&RadioButtonList1=%D1%A7%C9%FA&Button1=+%B5%C7+%C2%BC");
		$result = curl_exec($ch);
		$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$errno = curl_errno($ch);
		curl_close($ch);

		if(strlen($url) < 42){
			// Error
			$this->last_error = 'Inetwork_error';
			return false;
		}else if(strpos($url, 'xs_main.aspx')){
			$this->setSessionPath(pathinfo($url, PATHINFO_DIRNAME));
			return true;
		}else{
			if(preg_match("/alert\(\'(.+)'\);/", $result, $matches)){
				$this->last_error = iconv('GB2312', 'UTF-8//IGNORE', $matches[1]);
			}else if(strpos($result, iconv('UTF-8', 'GB2312//IGNORE', '<input type="submit" name="Button1" value=" 登 录 "'))){
				$this->last_error = 'Iservice_error';
			}else if($errno == 28){
				$this->last_error = 'Itimeout_error';
				return false;
			}else if($httpcode && $httpcode != 200){
				$this->last_error = 'Error '.$httpcode;
				return false;
			}else{
				$this->last_error = 'Iparse_error';
				//var_dump($result);
				//var_dump(curl_error($ch));
			}
			return false;
		}
	}

	function getSchedulePageFetch($year=null, $term=null){
		$ch = $this->getCH();
		curl_setopt($ch, CURLOPT_URL, $this->sessionPath."/xskbcx.aspx?xh=".$this->username."&xm=&gnmkdm=N121603");
		if($year && $term){
			if(!$this->schedulePagePostViewState){
				$this->last_error = 'Ineeds_viewstate';
				curl_close($ch);
				return false;
			}
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, '__EVENTTARGET=xqd&__EVENTARGUMENT=&__VIEWSTATE='.$this->schedulePagePostViewState."&xnd=".$year."&xqd=".$term);
		}
		$html = curl_exec($ch);
		$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$errno = curl_errno($ch);
		curl_close($ch);

		if(strpos($url, 'xskbcx.aspx')){
			if(preg_match("/alert\(\'(.+)'\);/", $html, $alertMatches)){
				$this->last_error = iconv('GB2312', 'UTF-8//IGNORE', $alertMatches[1]);
				return false;
			}
			if($errno == 28){
				$this->last_error = 'Itimeout_error';
				return false;
			}
			if($httpcode && $httpcode != 200){
				$this->last_error = 'Error '.$httpcode;
				return false;
			}
			if($html === false){
				//var_dump(curl_errno($ch), curl_error($ch));
				$this->last_error = 'Iservice_error';
				return false;
			}
			// else
			$doc = new DOMDocument();
			@$doc->loadHTML(str_replace('gb2312"', 'UTF-8"', iconv('GB2312', 'UTF-8//IGNORE', $html)));
			return array($html, $url, $httpcode, $doc);
		}else{
			//var_dump($url);
			$this->last_error = 'Iservice_error';
			return false;
		}
	}

	/*
	 * @return Array [Array $info, Array $table1, Array $table2]
	 *         - $info basic info (term, major...)
	 *         - $table1 main lesson schedule
	 *         - $table2 lesson schedule changes
	 */
	function getSchedulePage($year=null, $term=null){
		if($year && $term && !$this->schedulePagePostViewState){
			$r = $this->getSchedulePageFetch();
			if(!$r){
				return false;
			}else{
				if(preg_match('/<input type="hidden" name="__VIEWSTATE" value="(.+?)" \/>/', $r[0], $vs_matches)){
					$this->schedulePagePostViewState = urlencode($vs_matches[1]);
					$info = array();
					$xnd = $r[3]->getElementById('xnd');
					if($xnd){
						$xnd = $xnd->getElementsByTagName('option');
						foreach($xnd as $i=>$option){
							if($option->getAttribute('selected') === 'selected'){
								$info['year'] = $option->nodeValue;
							}
						}
					}
					$xnd = $r[3]->getElementById('xqd');
					if($xnd){
						$xnd = $xnd->getElementsByTagName('option');
						foreach($xnd as $i=>$option){
							if($option->getAttribute('selected') === 'selected'){
								$info['term'] = $option->nodeValue;
							}
						}
					}
					if($info['year'] == $year && $info['term'] == $term){
						// Don't do duplicate request
					}else{
						$r = $this->getSchedulePageFetch($year, $term);
					}
				}else{
					$this->last_error = 'Iparse_error';
					return false;
				}
			}
		}else{
			$r = $this->getSchedulePageFetch($year, $term);
		}

		if(!$r){
			return false;
		}
		// else
		list($html, $url, $httpcode, $doc) = $r;

		$table1 = $doc->getElementById('dgrdKb');
		$table2 = $doc->getElementById('DBGrid');

		if($table1 && $table2 && $table1->tagName == 'table' && $table2->tagName == 'table'){
			// Get additional info
			$info = array();
			$xnd = $doc->getElementById('xnd');
			if($xnd){
				$xnd = $xnd->getElementsByTagName('option');
				foreach($xnd as $i=>$option){
					if($option->getAttribute('selected') === 'selected'){
						$info['year'] = $option->nodeValue;
					}
				}
			}
			$xnd = $doc->getElementById('xqd');
			if($xnd){
				$xnd = $xnd->getElementsByTagName('option');
				foreach($xnd as $i=>$option){
					if($option->getAttribute('selected') === 'selected'){
						$info['term'] = $option->nodeValue;
					}
				}
			}
			$label = $doc->getElementById('Label5');
			if($label){
				$info['stuno'] = preg_replace("/^.+?：/", '', $label->nodeValue);
			}
			$label = $doc->getElementById('Label6');
			if($label){
				$info['stuname'] = preg_replace("/^.+?：/", '', $label->nodeValue);
			}
			$label = $doc->getElementById('Label7');
			if($label){
				$info['department'] = preg_replace("/^.+?：/", '', $label->nodeValue);
			}
			$label = $doc->getElementById('Label8');
			if($label){
				$info['major'] = preg_replace("/^.+?：/", '', $label->nodeValue);
			}
			$label = $doc->getElementById('Label9');
			if($label){
				$info['class'] = preg_replace("/^.+?：/", '', $label->nodeValue);
			}
			return array($info, $table1, $table2);
		}else{
			$this->last_error = 'Iparse_error';
			//var_dump($html);
			return false;
		}
	}

	// @return Array
	function parseScheduleTableMain($xD){
		$return = array();
		foreach($xD->getElementsByTagName('tr') as $i=>$tr){
			$tds = $tr->getElementsByTagName('td');

			if($i == 0 || preg_replace("/[\x{00a0}\x{200b}\s]+/u", '', $tds->item(0)->nodeValue) == '') continue;
			// thead or blank line

			$l = new LessonBIT(array(
				'name'=>$tds->item(0)->nodeValue,
				'credit'=>$tds->item(1)->nodeValue*1,
				'category'=>$tds->item(2)->nodeValue.(preg_replace("/[\x{00a0}\x{200b}\s]+/u", '', $tds->item(3)->nodeValue)?('/'.$tds->item(3)->nodeValue):''),
				'tutor'=>$tds->item(6)->nodeValue,
				'department'=>$tds->item(4)->nodeValue
			));

			$tdTime = explode(';', $tds->item(7)->nodeValue);
			$tdLoc = explode(';', $tds->item(8)->nodeValue);
			$weekArr = self::getWeekLangArr(1);

			foreach($tdTime as $time_i=>$time){
				$lsarray = array('week'=>array());
				if(preg_match("/周([一|二|三|四|五|六|日])/u", $time, $weekdayMatch)){
					$lsarray['day'] = $weekArr[$weekdayMatch[1]];
				}
				if(preg_match("/第((\d+),.*?(\d+))(小)?节/", $time, $timeMatch)){
					if(substr_count($timeMatch[1], ',') != ($timeMatch[3]-$timeMatch[2])){
						// Error
						throw new Exception("Lesson Count not equal! ".$timeMatch[1], 1);
					}else{
						$lsarray['startTime'] = $timeMatch[2]*1;
						$lsarray['durationTime'] = $timeMatch[3]-$timeMatch[2]+1;
					}
				}
				if(preg_match("/第(\d+)-(\d+)周([双周|单周])?/u", $time, $weekMatch)){
					$weekMatch[1] = (int) $weekMatch[1];
					$weekMatch[2] = (int) $weekMatch[2];

					if(!isset($weekMatch[3])) $weekMatch[3] = 0;
					if($weekMatch[3] === '单周'){
						$weekMatch[3] = 2;
					}else if($weekMatch[3] === '双周'){
						$weekMatch[3] = 1;
					}

					while($weekMatch[1] <= $weekMatch[2]){
						if($weekMatch[3] == 0 || ($weekMatch[1]%$weekMatch[3])){
							$lsarray['week'][] = $weekMatch[1];
						}
						$weekMatch[1]++;
					}
				}
				$lsarray['location'] = preg_replace("/[\x{00a0}\x{200b}\s]+/u", '', $tdLoc[$time_i]);

				if(empty($lsarray['week']) || !isset($lsarray['day']) || !isset($lsarray['startTime']) || !isset($lsarray['durationTime'])){

				}else{
					$l->addSchedule(new LessonScheduleBIT($lsarray));
				}
			}

			//$return[] = $l;
			$return = $this->mergeChangedLesson($return, $l);
		}

		return $return;
	}

	// @param $cInfo strings being parsed
	// @return LessonBIT
	function _parseScheduleTableChangesLessonText($cInfo, $class = 'LessonBIT'){
		if(preg_match("/^(.+)\((.+)\)周(\d)第(\d+)节连续(\d)节.*?(\/(.*))?$/u", $cInfo, $cInfoMatch)){
			$l = new $class(array(
				'tutor'=>$cInfoMatch[1],
				'department'=>$cInfoMatch[2],
			));

			$lsarray = array('week'=>array(), 'day'=>$cInfoMatch[3], 'startTime'=>$cInfoMatch[4], 'durationTime'=>$cInfoMatch[5]);

			if(preg_match("/第(\d+)-(\d+)周([双周|单周])?/u", $cInfo, $weekMatch)){
				$weekMatch[1] = (int) $weekMatch[1];
				$weekMatch[2] = (int) $weekMatch[2];

				if(!isset($weekMatch[3])) $weekMatch[3] = 0;
				if($weekMatch[3] === '单周'){
					$weekMatch[3] = 2;
				}else if($weekMatch[3] === '双周'){
					$weekMatch[3] = 1;
				}

				while($weekMatch[1] <= $weekMatch[2]){
					if($weekMatch[3] == 0 || ($weekMatch[1]%$weekMatch[3])){
						$lsarray['week'][] = $weekMatch[1];
					}
					$weekMatch[1]++;
				}
			}

			$lsarray['location'] = preg_replace("/[\x{00a0}\x{200b}\s]+/u", '', $cInfoMatch[7]);

			if(empty($lsarray['week']) || !isset($lsarray['day']) || !isset($lsarray['startTime']) || !isset($lsarray['durationTime'])){

			}else{
				$l->addSchedule(new LessonScheduleBIT($lsarray));
			}
		}
		return $l;
	}

	// @param $forceChanges 1: Don't redirect $new of LessonBITDeletion to mergeDeletedLesson
	// @return Array
	function mergeChangedLesson(Array $orig, $new, $forceChanges = 0){
		if($new instanceof LessonBITDeletion && !$forceChanges){
			return $this->mergeDeletedLesson($orig, $new);
		}

		$newHash = $new->getHash(1);
		foreach($orig as $i=>$l) {
			if($newHash == $l->getHash(1)){
				$newHash = false;
				$orig[$i]->changesID = $orig[$i]->changesID.','.$new->changesID;
				if(strpos($new->tutor, $l->tutor)){// l is exact

				}else if(strpos($l->tutor, $new->tutor)){// new is exact
					$l->tutor = $new->tutor;
				}

				foreach($new->schedule as $s) {
					$l->addSchedule($s);
				}

				break;
			}else if($new->name == $l->name){
				// Copy info to changed lesson
				$new->fillInfoFromLesson($l);
			}
		}
		if($newHash) $orig[] = $new;
		return $orig;
	}

	function mergeNearSchedule(Array $orig){
		foreach($orig as $l){
			$l->mergeNearSchedule();
		}
	}

	// @return Array
	function mergeDeletedLesson(Array $orig, $l = null){
		if(!$l) return $this->mergeDeletedLessonOld($orig);

		$lhash = $l->getHash(1);
		foreach($orig as $ic=>$lc){
			if($lc instanceof LessonBIT){
				// Compare
				if($lc->getHash(1) == $lhash){
					// Lesson matches
					// Foreach schedule
					foreach($l->schedule as $ils=>$ls){
						$lsHash = $ls->getHashPerWeek();
						foreach($lc->schedule as $ilcs=>$lcs){
							if($lsHash == $lcs->originalHashPerWeek){//getHashPerWeek()
								// Use original hash to cope with system's output
								// Delete week
								foreach($ls->week as $lsw){
									$lcwkey = array_search($lsw, $lcs->week);
									if($lcwkey !== false){
										unset($lcs->week[$lcwkey]);
									}
								}
								// Rearrange keys
								$lcs->week = array_values($lcs->week);
								if(!count($lcs->week)){
									unset($lc->schedule[$ilcs]);
								}
							}
						}
						$lc->schedule = array_values($lc->schedule);
					}
					if(!count($lc->schedule)){
						unset($orig[$ic]);
					}
				}
			}// Lesson
		}

		// Arrange array
		$orig = array_values($orig);
		return $orig;
	}

	// @return Array
	function mergeDeletedLessonOld(Array $orig){
		foreach($orig as $i=>$l){
			if($l instanceof LessonBITDeletion){
				$lhash = $l->getHash(1);
				foreach($orig as $ic=>$lc){
					if($lc instanceof LessonBIT){
						// Compare
						if($lc->getHash(1) == $lhash){
							// Lesson matches
							// Foreach schedule
							foreach($l->schedule as $ils=>$ls){
								$lsHash = $ls->getHashPerWeek();
								foreach($lc->schedule as $ilcs=>$lcs){
									if($lsHash == $lcs->getHashPerWeek()){
										// Delete week
										foreach($ls->week as $lsw){
											$lcwkey = array_search($lsw, $lcs->week);
											if($lcwkey !== false){
												unset($lcs->week[$lcwkey]);
											}
										}
										// Rearrange keys
										$lcs->week = array_values($lcs->week);
										if(!count($lcs->week)){
											unset($lc->schedule[$ilcs]);
										}
									}
								}
								$lc->schedule = array_values($lc->schedule);
							}
							if(!count($lc->schedule)){
								unset($orig[$ic]);
							}
						}
					}// Lesson
				}
				// Arrange array
				$orig = array_values($orig);
			}// if LessonBITDeletion
		} // End $orig
		return $orig;
	}

	// @return Array
	function parseScheduleTableChanges($xD){
		$returnl = array();
		$returnd = array();

		$trs = array();
		foreach($xD->getElementsByTagName('tr') as $i=>$tr){
			if($i == 0) continue;//thead

			$tds = $tr->getElementsByTagName('td');//nodeValue
			$trs[$tds->item(4)->nodeValue.$tds->item(0)->nodeValue] = $tr;
		}
		ksort($trs);

		foreach(array_values($trs) as $tr){
			$tds = $tr->getElementsByTagName('td');//nodeValue
			$changesID = $tds->item(0)->nodeValue;

			if(strpos($changesID, '补') === 0 || strpos($changesID, '换') === 0 || strpos($changesID, '调') === 0){
				$cInfo = $tds->item(3)->nodeValue;
				$l = $this->_parseScheduleTableChangesLessonText($cInfo);
				$l->changesID = $changesID;
				$l->changesTime = $tds->item(4)->nodeValue;
				$l->name = $tds->item(1)->nodeValue;
			}

			if(strpos($changesID, '换') === 0 || strpos($changesID, '调') === 0 || strpos($changesID, '停') === 0){
				$ld = $this->_parseScheduleTableChangesLessonText($tds->item(2)->nodeValue, 'LessonBITDeletion');
				$ld->changesID = $changesID;
				$ld->changesTime = $tds->item(4)->nodeValue;
				$ld->name = $tds->item(1)->nodeValue;

				if(isset($l)) $l->schedule[0]->originalHashPerWeek = $ld->schedule[0]->getHashPerWeek();

				$returnl[] = $ld;
				//$returnd = $this->mergeChangedLesson($returnd, $ld);
			}

			if(isset($l)) $returnl[] = $l;
			//$returnl = $this->mergeChangedLesson($returnl, $l);
		}

		// Sort it
		return array_interlace($returnl, $returnd);
	}

	// @return void
	function changesFillInfoFromMain($t2, $t1){
		foreach ($t2 as $v) {
			if($v instanceof LessonBITDeletion){
				$vHash = $v->getHash(1);
				foreach ($t1 as $vgetInfo) {
					if($vgetInfo->getHash(1) == $vHash){
						$v->fillInfoFromLesson($vgetInfo);
						break;
					}
				}
			}
		}
	}

	// @return String
	function getSchoolCalendarFetch($year, $term){
		if(isset($this->calendarCache[$year.'-'.$term][0])){
			$result = $this->calendarCache[$year.'-'.$term][0];
		}else{
			$ch = $this->getCH();
			curl_setopt($ch, CURLOPT_URL, 'http://weixin.info.bit.edu.cn/schoolCalendar/wechatQuery?code='.$year.'-'.$term);
			curl_setopt($ch, CURLOPT_REFERER, '');
			$result = curl_exec($ch);
			curl_close($ch);
			$this->calendarCache[$year.'-'.$term][0] = $result;
		}

		return $result;
	}

	// @return Array [$year, $month, $day]
	function getSchoolCalendar2Date($year, $term, $week, $weekday){
		if(isset($this->calendarCache[$year.'-'.$term][1])){
			$result = $this->calendarCache[$year.'-'.$term][1];
		}else{
			$html = $this->getSchoolCalendarFetch($year, $term);
			$doc = new DOMDocument();
			@$doc->loadHTML('<?xml version="1.0" encoding="UTF-8"?>'.$html);

			$table = $doc->getElementsByTagName('table');
			if(count($table)){
				$result = $table->item(0)->getElementsByTagName('td');
			}else{
				$result = array();
			}
			$this->calendarCache[$year.'-'.$term][1] = $result;
		}

		$year = substr($year, 0, 4)-1+$term;

		$oldid = $week*7-8+$weekday;
		$id = $week*7-8+$weekday;
		if($id<0) return false;
		$orig = 1;
		$day = $result->item($id)->nodeValue;

		$month = $day;
		$day = (int) $day;
		while(strpos($month, '月') == false && $id){
			$id--;
			$orig = 0;
			$month = $result->item($id)->nodeValue;
		}

		if(!$id){
			while(strpos($month, '月') == false){
				$id++;
				$orig = 0;
				$month = $result->item($id)->nodeValue;
			}
			$month = (int) str_replace('月', '', $month);
			$month--;
		}else{
			$month = (int) str_replace('月', '', $month);
		}

		$day = $orig?1:$day;

		if($term == 1 && $month < 7){
			$year++;
		}
		return array($year, $month, $day);
	}

	/*
	 * @param $year '2014-2015'
	 * @param $term Number 1/2
	 * @return Number
	 */
	function getCurrentWeekFetch($year, $term){
		$result = $this->getSchoolCalendarFetch($year, $term);

		if(preg_match('/<th>(\d+)<\/th>\s+(<td class=".*">.+<\/td>\s+){0,6}?<td class=".*today/', $result, $matches)){
			// /<th>(\d+)<\/th>(?:(?!tr)[\s\S])*<td class=".*today/
			// This is slower; don't use this!
			return (int) $matches[1];
		}else{
			return 0;
		}
	}

	// @return Number
	function getCurrentWeek(){
		$year = date('Y');
		$month = date('m');
		if($month < 4){
			$attempt = array($this->getCurrentWeekFetch(($year-1).'-'.$year, 1), ($year-1).'-'.$year, 1);
			if(!$attempt[0]){
				$attempt = array($this->getCurrentWeekFetch(($year-1).'-'.$year, 2), ($year-1).'-'.$year, 2);
			}
			return $attempt;
		}

		if($month < 8){
			$attempt = array($this->getCurrentWeekFetch(($year-1).'-'.$year, 2), ($year-1).'-'.$year, 2);
			return $attempt;
		}

		$attempt = array($this->getCurrentWeekFetch($year.'-'.($year+1), 1), $year.'-'.($year+1), 1);
		if(!$attempt[0]){
			$attempt = array($this->getCurrentWeekFetch(($year-1).'-'.$year, 2), ($year-1).'-'.$year, 2);
		}
		return $attempt;
	}

	function getGradePageFetch($year=null, $term=null, $getviewstate = false){
		$ch = $this->getCH();
		curl_setopt($ch, CURLOPT_URL, $this->sessionPath."/xscjcx.aspx?xh=".$this->username."&xm=&gnmkdm=N121605");
		if(!$getviewstate){
			if(!$this->gradePagePostViewState){
				$this->last_error = 'Ineeds_viewstate';
				curl_close($ch);
				return false;
			}

			if($year && $term){
				$param_mode = '&btn_xq=%D1%A7%C6%DA%B3%C9%BC%A8';
			}else if($year){
				$param_mode = '&btn_xn=%D1%A7%C6%DA%B3%C9%BC%A8';
			}else{
				$param_mode = '&btn_zcj=%D1%A7%C6%DA%B3%C9%BC%A8';
			}
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, '__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE='.$this->gradePagePostViewState."&ddl_kcxz=&ddlXN=".$year."&ddlXQ=".$term.$param_mode);
		}

		$html = curl_exec($ch);
		$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$errno = curl_errno($ch);
		curl_close($ch);

		if(strpos($url, 'xscjcx.aspx')){
			if(preg_match("/alert\(\'(.+)'\);/", $html, $alertMatches)){
				$this->last_error = iconv('GB2312', 'UTF-8//IGNORE', $alertMatches[1]);
				return false;
			}
			if($errno == 28){
				$this->last_error = 'Itimeout_error';
				return false;
			}
			if($httpcode && $httpcode != 200){
				$this->last_error = 'Error '.$httpcode;
				return false;
			}
			if($html === false){
				//var_dump(curl_errno($ch), curl_error($ch));
				$this->last_error = 'Iservice_error';
				return false;
			}
			// else
			$doc = new DOMDocument();
			@$doc->loadHTML($html);
			return array($html, $url, $httpcode, $doc);
		}else{
			//var_dump($url);
			$this->last_error = 'Iservice_error';
			return false;
		}
	}

	function getGradePage($year=null, $term=null){
		if(!$this->gradePagePostViewState){
			$r = $this->getGradePageFetch(null, null, true);
			if(!$r){
				return false;
			}else{
				if(preg_match('/<input type="hidden" name="__VIEWSTATE" value="(.+?)" \/>/', $r[0], $vs_matches)){
					$this->gradePagePostViewState = urlencode($vs_matches[1]);
				}else{
					$this->last_error = 'Iparse_error';
					return false;
				}
			}
		}

		$r = $this->getGradePageFetch($year, $term);

		if(!$r){
			return false;
		}
		// else
		list($html, $url, $httpcode, $doc) = $r;

		// Get additional info
		$info = array();
		$titleDOM = $doc->getElementById('lbl_bt');

		if($titleDOM && preg_match('/(([0-9\-]+)学年(第(\d)学期)?|在校)学习成绩/', $titleDOM->nodeValue, $title_matches)){
			$info['year'] = isset($title_matches[2])?$title_matches[2]:NULL;
			$info['term'] = isset($title_matches[4])?$title_matches[4]:NULL;
		}else{
			$this->last_error = 'Iservice_error';
			// var_dump($html);
			return false;
		}

		$table1 = $doc->getElementById('Datagrid1');

		if($table1 && $table1->tagName == 'table'){
			$label = $doc->getElementById('lbl_xh');
			if($label){
				$info['stuno'] = preg_replace("/^.+?：/", '', $label->nodeValue);
			}
			$label = $doc->getElementById('lbl_xm');
			if($label){
				$info['stuname'] = preg_replace("/^.+?：/", '', $label->nodeValue);
			}
			$label = $doc->getElementById('lbl_xy');
			if($label){
				$info['department'] = preg_replace("/^.+?：/", '', $label->nodeValue);
			}
			$label = $doc->getElementById('lbl_zymc');
			if($label){
				$info['major'] = $label->nodeValue;
			}
			$label = $doc->getElementById('lbl_xzb');
			if($label){
				$info['class'] = preg_replace("/^.+?：/", '', $label->nodeValue);
			}
			return array($info, $table1);
		}else{
			$this->last_error = 'Iparse_error';
			//var_dump($html);
			return false;
		}
	}

	// @return Array
	function parseGradeTableMain($xD){
		$return = array();
		foreach($xD->getElementsByTagName('tr') as $i=>$tr){
			if($i == 0) continue;//thead

			$tds = $tr->getElementsByTagName('td');//nodeValue

			$l = new GradeBIT(array(
				'name'=>$tds->item(3)->nodeValue,
				'credit'=>$tds->item(6)->nodeValue*1,
				'category'=>$tds->item(4)->nodeValue.(preg_replace("/[\x{00a0}\x{200b}\s]+/u", '', $tds->item(5)->nodeValue)?('/'.$tds->item(5)->nodeValue):''),
				'grade'=>$tds->item(7)->nodeValue,
				'reTest'=>$tds->item(9)->nodeValue,
				'year'=>$tds->item(0)->nodeValue,
				'term'=>$tds->item(1)->nodeValue,
				'id'=>$tds->item(2)->nodeValue,
				'paperScore'=>$tds->item(8)->nodeValue
			));

			$return[] = $l;
		}

		return $return;
	}

	function mergeLowerGrade(Array $array){
		return $array;
	}

	function getExamSchedulePageFetch($year=null, $term=null){
		$ch = $this->getCH();
		curl_setopt($ch, CURLOPT_URL, $this->sessionPath."/xskscx.aspx?xh=".$this->username."&xm=&gnmkdm=N121604");
		if($year && $term){
			if(!$this->examSchedulePagePostViewState){
				$this->last_error = 'Ineeds_viewstate';
				curl_close($ch);
				return false;
			}
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, '__EVENTTARGET=xqd&__EVENTARGUMENT=&__VIEWSTATE='.$this->examSchedulePagePostViewState."&xnd=".$year."&xqd=".$term);
		}
		$html = curl_exec($ch);
		$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$errno = curl_errno($ch);
		curl_close($ch);

		if(strpos($url, 'xskscx.aspx')){
			if(preg_match("/alert\(\'(.+)'\);/", $html, $alertMatches)){
				$this->last_error = iconv('GB2312', 'UTF-8//IGNORE', $alertMatches[1]);
				return false;
			}
			if($errno == 28){
				$this->last_error = 'Itimeout_error';
				return false;
			}
			if($httpcode && $httpcode != 200){
				$this->last_error = 'Error '.$httpcode;
				return false;
			}
			if($html === false){
				//var_dump(curl_errno($ch), curl_error($ch));
				$this->last_error = 'Iservice_error';
				return false;
			}
			// else
			$doc = new DOMDocument();
			@$doc->loadHTML(str_replace('gb2312"', 'UTF-8"', iconv('GB2312', 'UTF-8//IGNORE', $html)));
			return array($html, $url, $httpcode, $doc);
		}else{
			//var_dump($url);
			$this->last_error = 'Iservice_error';
			return false;
		}
	}

	/*
	 * @return Array [Array $info, Array $table1, Array $table2]
	 *         - $info basic info (term, major...)
	 *         - $table1 main lesson schedule
	 *         - $table2 lesson schedule changes
	 */
	function getExamSchedulePage($year=null, $term=null){
		if($year && $term && !$this->examSchedulePagePostViewState){
			$r = $this->getExamSchedulePageFetch();
			if(!$r){
				return false;
			}else{
				if(preg_match('/<input type="hidden" name="__VIEWSTATE" value="(.+?)" \/>/', $r[0], $vs_matches)){
					$this->examSchedulePagePostViewState = urlencode($vs_matches[1]);
					$info = array();
					$xnd = $r[3]->getElementById('xnd');
					if($xnd){
						$xnd = $xnd->getElementsByTagName('option');
						foreach($xnd as $i=>$option){
							if($option->getAttribute('selected') === 'selected'){
								$info['year'] = $option->nodeValue;
							}
						}
					}
					$xnd = $r[3]->getElementById('xqd');
					if($xnd){
						$xnd = $xnd->getElementsByTagName('option');
						foreach($xnd as $i=>$option){
							if($option->getAttribute('selected') === 'selected'){
								$info['term'] = $option->nodeValue;
							}
						}
					}
					if($info['year'] == $year && $info['term'] == $term){
						// Don't do duplicate request
					}else{
						$r = $this->getExamSchedulePageFetch($year, $term);
					}
				}else{
					$this->last_error = 'Iparse_error';
					return false;
				}
			}
		}else{
			$r = $this->getSchedulePageFetch($year, $term);
		}

		if(!$r){
			return false;
		}
		// else
		list($html, $url, $httpcode, $doc) = $r;

		$table1 = $doc->getElementById('DataGrid1');

		if($table1 && $table1->tagName == 'table'){
			$info = array();
			$xnd = $doc->getElementById('xnd');
			if($xnd){
				$xnd = $xnd->getElementsByTagName('option');
				foreach($xnd as $i=>$option){
					if($option->getAttribute('selected') === 'selected'){
						$info['year'] = $option->nodeValue;
					}
				}
			}
			$xnd = $doc->getElementById('xqd');
			if($xnd){
				$xnd = $xnd->getElementsByTagName('option');
				foreach($xnd as $i=>$option){
					if($option->getAttribute('selected') === 'selected'){
						$info['term'] = $option->nodeValue;
					}
				}
			}
			return array($info, $table1);
		}else{
			$this->last_error = 'Iparse_error';
			//var_dump($html);
			return false;
		}
	}

	// @return Array
	function parseExamScheduleTableMain($xD){
		$return = array();
		foreach($xD->getElementsByTagName('tr') as $i=>$tr){
			$tds = $tr->getElementsByTagName('td');

			if($i == 0 || preg_replace("/[\x{00a0}\x{200b}\s]+/u", '', $tds->item(3)->textContent) == '') continue;
			// thead or blank line

			$dateOrig = $tds->item(3)->textContent;
			if(preg_match("/(\d+)年(\d+)月(\d+)日\(([0-9:]+)-([0-9:]+)\)/u", $dateOrig, $dateMatch)){
				$date = $dateMatch[1].'-'.$dateMatch[2].'-'.$dateMatch[3];
				$startTime = $dateMatch[4];
				$endTime = $dateMatch[5];
			}else if(preg_match("/第(\d+)周周(\d+)\(([0-9-]+)\) ([0-9:]+)-([0-9:]+)/u", $dateOrig, $dateMatch)){
				$date = $dateMatch[3];
				$startTime = $dateMatch[4];
				$endTime = $dateMatch[5];
			}

			$l = new ExamScheduleBIT(array(
				'id'=>$tds->item(0)->textContent,
				'name'=>$tds->item(1)->textContent,
				'dateOrig'=>$dateOrig,
				'date'=>$date,
				'startTime'=>$startTime,
				'endTime'=>$endTime,
				'location'=>$tds->item(4)->textContent,
				'seat'=>$tds->item(6)->textContent
			));

			$return[] = $l;
		}

		return $return;
	}
}

Class LessonBIT{
	public $id = '';
	public $name = '';
	public $credit = 0;
	public $category = '';
	public $tutor = '';
	public $department = '';
	public $changesID = null;
	public $changesTime = null;
	public $schedule = array();
	function __construct($array=array()) {
		if(isset($array['id'])) $this->id = $array['id'];
		if(isset($array['name'])) $this->name = $array['name'];
		if(isset($array['credit'])) $this->credit = $array['credit'];
		if(isset($array['category'])) $this->category = $array['category'];
		if(isset($array['tutor'])) $this->tutor = $array['tutor'];
		if(isset($array['department'])) $this->department = $array['department'];
		if(isset($array['changesID'])) $this->changesID = $array['changesID'];
		if(isset($array['changesTime'])) $this->changesTime = $array['changesTime'];
	}
	function getHash($tutor = 0){
		return $this->name.'|'.($tutor?$this->tutor:$this->department);
		// Tutor is removed by default
	}
	function addSchedule(LessonScheduleBIT $s){
		// Merge duplicate
		$sHash = $s->getHashPerWeek();
		$sHashCheckNear = $s->getHashNearInADay();
		foreach($this->schedule as $sc) {
			if($sHash == $sc->getHashPerWeek()){
				$sc->addWeek($s->week);
				$sHash = false;
			}
			if(!$sHash) break;
		}
		if($sHash){
			$this->schedule[] = $s;
		}

		$this->sortSchedule();
	}
	function mergeNearSchedule(){
		$sLength = count($this->schedule);
		foreach($this->schedule as $si=>$s){
			$sHashCheckNear = $s->getHashNearInADay();
			for($sci = $si+1;$sci<$sLength;$sci++){
				if(empty($this->schedule[$sci])){
					continue;
				}

				$sc = $this->schedule[$sci];
				if($sHashCheckNear == $sc->getHashNearInADay()){
					if($sc->startTime + $sc->durationTime == $s->startTime){
						$sc->durationTime = $sc->durationTime + $s->durationTime;
						$sHashCheckNear = false;
					}
					if($s->startTime + $s->durationTime == $sc->startTime){
						$sc->startTime = $s->startTime;
						$sc->durationTime = $sc->durationTime + $s->durationTime;
						$sHashCheckNear = false;
					}
					unset($this->schedule[$si]);
				}
				if(!$sHashCheckNear) break;
			}
		}
		$this->sortSchedule();
	}
	function sortSchedule(){
		$sortArr = array();
		foreach($this->schedule as $s){
			$sortArr[$s->getHashSort()] = $s;
		}
		ksort($sortArr);
		$this->schedule = array_values($sortArr);
	}
	function fillInfoFromLesson(LessonBIT $l){
		$this->credit = $l->credit;
		$this->category = $l->category;
		$this->id = $l->id;
	}
}

class LessonBITDeletion extends LessonBIT{

}
Class LessonScheduleBIT{
	public $week = array();
	public $day = 0;
	public $location = '';
	public $startTime = 1;
	public $durationTime = 2;
	public $originalHashPerWeek = null;
	protected $lesson = null;
	function __construct($array=array()) {
		if(is_array($array['week'])) $this->week = $array['week'];
		if(isset($array['day'])) $this->day = $array['day'];
		if(isset($array['location'])) $this->location = $array['location'];
		if(isset($array['startTime'])) $this->startTime = $array['startTime'];
		if(isset($array['durationTime'])) $this->durationTime = $array['durationTime'];

		$this->originalHashPerWeek = $this->getHashPerWeek();
	}
	function getHashPerWeek(){
		return $this->day.'|'.$this->startTime.'|'.$this->durationTime.'|'.$this->location;
	}
	function getHashNearInADay(){
		return implode(',', $this->week).'|'.$this->day.'|'.$this->location;
	}
	function getHashSort(){
		return $this->day.'|'.$this->startTime.'|'.sprintf("%'.02d\n", $this->week[0]).'|'.$this->durationTime.'|'.$this->location;
	}
	function getTimePerWeek(){
		$weekday = schoolBIT::getWeekLangArr();
		return ($weekday[$this->day]?('周'.$weekday[$this->day]):'').'第'.$this->startTime.'-'.($this->startTime+$this->durationTime-1).'节';
	}
	/*
	 * @param $returnArray
	 *        - 0: @return String
	 *        - 1: @return Array
	 */
	function getWeekText($returnArray = 0){
		$start = 0;
		$duration = 0;
		$text = '';
		$length = count($this->week)-1;
		$return = array();
		foreach ($this->week as $i=>$week) {
			if(!$start){
				$start = $week;
			}
			if($start != $week || !$length){
				if($start + $duration + 1 == $week){
					$duration++;
				}else{
					if($duration == 0){
						if($returnArray){
							$return[] = array($start);
						}else{
							$text .= $start.',';
						}
					}else if($duration == 1){
						if($returnArray){
							$return[] = array($start, $start+1);
						}else{
							$text .= $start.'-'.($start+1).',';
						}
					}else{
						if($returnArray){
							$return[] = array($start, $start+$duration);
						}else{
							$text .= $start.'-'.($start+$duration).',';
						}
					}
					$start = $week;
					$duration = 0;
				}
				if($length == $i && $length){
					if($duration == 0){
						if($returnArray){
							$return[] = array($start);
						}else{
							$text .= $start.',';
						}
					}else if($duration == 1){
						if($returnArray){
							$return[] = array($start, $start+1);
						}else{
							$text .= $start.'-'.($start+1).',';
						}
					}else{
						if($returnArray){
							$return[] = array($start, $start+$duration);
						}else{
							$text .= $start.'-'.($start+$duration).',';
						}
					}
				}
			}
		}
		if($returnArray){
			return $return;
		}else{
			$text = substr($text, 0, -1);
			return $text;
		}
	}
	function addWeek(Array $array){
		$this->week = array_unique(array_merge($this->week, $array));
		sort($this->week);
	}
	function setLesson($lesson){
		$this->lesson = $lesson;
	}
	function getLesson(){
		return $this->lesson;
	}
}

Class GradeBIT{
	public $name = '';
	public $credit = 0;
	public $category = '';
	public $grade = '';// May be more than 100, or 0, or Chinese
	public $id = '';
	public $reTest = false;
	public $year = null;
	public $term = null;
	public $paperScore = null;
	function __construct($array=array()) {
		if(!empty($array['name'])) $this->name = $array['name'];
		if(!empty($array['credit'])) $this->credit = $array['credit'];
		if(!empty($array['category'])) $this->category = $array['category'];
		if(!empty($array['grade'])){
			if(preg_replace("/[0-9]/", '', $array['grade']) === ''){
				// There are numbers only
				$this->grade = (int) $array['grade'];
			}else{
				$this->grade = $array['grade'];
			}
		}
		if(!empty($array['id'])) $this->id = $array['id'];
		if(!empty($array['reTest'])) $this->reTest = $array['reTest'];
		if(!empty($array['year'])) $this->year = $array['year'];
		if(!empty($array['term'])) $this->term = $array['term'];
		if(!empty($array['paperScore'])) $this->paperScore = $array['paperScore'];
	}
	function getLessonHash(){
		return $this->credit.'|'.$this->id.'|'.$this->name;
	}
}

Class ExamScheduleBIT{
	public $id = '';
	public $name = '';
	public $dateOriginal = '';
	public $date = '';
	public $startTime = '';
	public $endTime = '';
	public $location = '';
	public $seat = '';
	function __construct($array=array()) {
		if(isset($array['id'])) $this->id = $array['id'];
		if(isset($array['name'])) $this->name = $array['name'];
		if(isset($array['dateOrig'])) $this->dateOriginal = $array['dateOrig'];
		if(isset($array['location'])) $this->location = $array['location'];
		if(isset($array['seat'])) $this->seat = $array['seat'];
		if(isset($array['date'])) $this->date = $array['date'];
		if(isset($array['startTime'])) $this->startTime = $array['startTime'];
		if(isset($array['endTime'])) $this->endTime = $array['endTime'];
	}
}