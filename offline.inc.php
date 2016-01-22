<?php
// Copied from below
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

//$t2Merged = array_interlace($t2Merged, $t2MergedDeletion);

foreach($t2MergedDeletion as $o){
	//if($o instanceof LessonBITDeletion){
		$s = '';
		foreach($o->schedule as $os){
			$s .= $os->getWeekText().'周'.$os->getTimePerWeek().'/'.$os->location.'; ';
		}
		$s = substr($s, 0, -2);
		$ld[] = array('name'=>$o->name, 'credit'=>$o->credit, 'tutor'=>$o->tutor, 'schedule'=>$s);
	//}
}

foreach($t1 as $o){
	$odata = array('name'=>$o->name, 'credit'=>$o->credit, 'tutor'=>$o->tutor, 'schedule'=>$o->schedule);
	if(count($o->schedule)){
		$ll[] = $odata;
		foreach($o->schedule as $os){
			$os->setLesson($o);
			$schedArr[$os->day][$os->startTime][] = $os;
		}
	}else{
		$llns[] = $odata;
	}
}
//$ll = array_interlace($ll, $llns);
ksort($schedArr);
foreach($schedArr as $i=>$sAs){
	ksort($schedArr[$i]);
}
?>

<?php if(!empty($week)){ ?>
<div class="mdl-card-style-alert mdl-card mdl-shadow--8dp">
	<div class="mdl-card__title">
		<h2 class="mdl-card__title-text">第 <?php echo $week; ?> 周</h2>
	</div>
</div>
<?php } ?>

<?php $weekArr = SchoolBIT::getWeekLangArr();$lessonShown = array();foreach ($schedArr as $weekday=>$wdA) { ?>
<div class="mdl-card-style-alert mdl-card mdl-shadow--4dp">
	<div class="mdl-card__title">
		<h2 class="mdl-card__title-text"><?php echo '星期',$weekArr[$weekday]; ?></h2>
	</div>
</div>
<div class="mdl-grid">
<?php foreach ($wdA as $startTime=>$wlA) { ?>
<?php foreach ($wlA as $sched) { $li = $sched->getLesson(); ?>
<div class="card-sm mdl-card mdl-shadow--2dp mdl-cell mdl-cell--4-col">
	<div class="mdl-card__title">
		<h2 class="mdl-card__title-text"><?php echo $li->name; ?></h2>
	</div>
	<?php if(empty($lessonShown[$li->getHash(1)])) { ?>
	<div class="mdl-card__supporting-text">
		<?php echo $li->credit; ?> 学分 &nbsp;&nbsp;<?php echo $li->tutor; ?>
	</div>
	<div class="mdl-card__supporting-text mdl-card--border">
		<?php $schLength = count($li->schedule)-1;foreach ($li->schedule as $i=>$ls) { if($i){echo '<br />';} if(!$i && $schLength){echo '<strong>';}echo $ls->getTimePerWeek(),'(第',$ls->getWeekText(),'周) ',$ls->location;if(!$i){echo '</strong>';} } ?>
	</div>
	<?php }else{ ?>
	<div class="mdl-card__supporting-text">
		<?php echo $sched->getTimePerWeek(),'(第',$sched->getWeekText(),'周) ',$sched->location; ?>
	</div>
	<?php } ?>
	
</div>
<?php $lessonShown[$li->getHash(1)] = 1;}/* End $sched foreach */ ?>
<?php } ?>
</div>
<?php } ?>

<?php if(count($llns) && empty($week)){ ?>
<div class="mdl-card-style-alert mdl-card mdl-shadow--4dp">
	<div class="mdl-card__title">
		<h2 class="mdl-card__title-text">实践类课程</h2>
	</div>
</div>

<div class="mdl-grid">

<?php foreach ($llns as $li) { ?>
<div class="card-sm mdl-card mdl-shadow--2dp mdl-cell mdl-cell--4-col">
	<div class="mdl-card__title">
		<h2 class="mdl-card__title-text"><?php echo $li['name']; ?></h2>
	</div>
	<div class="mdl-card__supporting-text">
		<?php echo $li['credit']; ?> 学分 &nbsp;&nbsp;<?php echo $li['tutor']; ?>
	</div>
	<?php if(count($li['schedule'])){ ?>
	<div class="mdl-card__supporting-text mdl-card--border">
		<?php foreach ($li['schedule'] as $i=>$ls) { if($i) echo '<br />'; echo $ls->getTimePerWeek(),'(第',$sched->getWeekText(),'周) ',$ls->location; ?>
		<?php } ?>
	</div>
	<?php } ?>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if(count($ld)){ ?>
				<table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp mdl-data-table--nselectable">
					<thead>
						<tr>
							<th>学分</th>
							<th class="mdl-data-table__cell--non-numeric">课程</th>
							<th class="mdl-data-table__cell--non-numeric">教师</th>
							<th class="mdl-data-table__cell--non-numeric">取消课时</th>
						</tr>
					</thead>
					<tbody>
<?php foreach ($ld as $li) { ?>
						<tr>
							<td><?php echo $li['credit']; ?></td>
							<td class="mdl-data-table__cell--non-numeric"><?php echo $li['name']; ?></td>
							<td class="mdl-data-table__cell--non-numeric"><?php echo $li['tutor']; ?></td>
							<td class="mdl-data-table__cell--non-numeric"><?php echo $li['schedule']; ?></td>
						</tr>
<?php } ?>
					</tbody>
				</table>
<?php } ?>
			<!--</div>-->