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

	public static $schedulePagePostViewState = '__EVENTTARGET=xqd&__EVENTARGUMENT=&__VIEWSTATE=dDwtMTY0MzA1MTY5MTt0PDtsPGk8MT47PjtsPHQ8O2w8aTwxPjtpPDM%2BO2k8Nz47aTwxMD47aTwxMj47aTwxND47aTwxNj47aTwxOD47aTwyMD47aTwyMj47aTwyND47aTwyNj47aTwzMD47PjtsPHQ8cDxwPGw8VGV4dDs%2BO2w8MDIwMTMtMjAxNDE7Pj47Pjs7Pjt0PHQ8cDxwPGw8RGF0YVRleHRGaWVsZDtEYXRhVmFsdWVGaWVsZDs%2BO2w8eG47eG47Pj47Pjt0PGk8MTM%2BO0A8MjAxNi0yMDE3OzIwMTUtMjAxNjsyMDE0LTIwMTU7MjAxMy0yMDE0OzIwMTItMjAxMzsyMDExLTIwMTI7MjAxMC0yMDExOzIwMDktMjAxMDsyMDA4LTIwMDk7MjAwNy0yMDA4OzIwMDYtMjAwNzsyMDA1LTIwMDY7XGU7PjtAPDIwMTYtMjAxNzsyMDE1LTIwMTY7MjAxNC0yMDE1OzIwMTMtMjAxNDsyMDEyLTIwMTM7MjAxMS0yMDEyOzIwMTAtMjAxMTsyMDA5LTIwMTA7MjAwOC0yMDA5OzIwMDctMjAwODsyMDA2LTIwMDc7MjAwNS0yMDA2O1xlOz4%2BO2w8aTwxPjs%2BPjs7Pjt0PHQ8OztsPGk8MD47Pj47Oz47dDxwPHA8bDxUZXh0Oz47bDzlrablj7fvvJoxMTIwMTQwMzY2Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlp5PlkI3vvJrmvZjlvJjlroc7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOWtpumZou%2B8muacuueUteWtpumZojs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85LiT5Lia77ya5py65qKw55S15a2Q5bel56iLOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzooYzmlL%2Fnj63vvJowMjMxMTQwMjs%2BPjs%2BOzs%2BO3Q8QDA8cDxwPGw8UGFnZUNvdW50O18hSXRlbUNvdW50O18hRGF0YVNvdXJjZUl0ZW1Db3VudDtEYXRhS2V5czs%2BO2w8aTwxPjtpPDQ%2BO2k8ND47bDw%2BOz4%2BOz47Ozs7Ozs7Ozs7PjtsPGk8MD47PjtsPHQ8O2w8aTwxPjtpPDI%2BO2k8Mz47aTw0Pjs%2BO2w8dDw7bDxpPDA%2BO2k8MT47aTwyPjtpPDM%2BO2k8ND47aTw1Pjs%2BO2w8dDxwPHA8bDxUZXh0Oz47bDznlLXlrZDlrp7kuaA7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOadjuaZk%2BWzsDs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8MS4wOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwwMS0wMzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BOz4%2BO3Q8O2w8aTwwPjtpPDE%2BO2k8Mj47aTwzPjtpPDQ%2BO2k8NT47PjtsPHQ8cDxwPGw8VGV4dDs%2BO2w85bel56iL5a6e6Le1SS3mnLrnlLU7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOaWveWutuagizs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Mjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8MDEtMDM7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCZuYnNwXDs7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCZuYnNwXDs7Pj47Pjs7Pjs%2BPjt0PDtsPGk8MD47aTwxPjtpPDI%2BO2k8Mz47aTw0PjtpPDU%2BOz47bDx0PHA8cDxsPFRleHQ7PjtsPOWkp%2BWtpueUn%2BiBjOS4mueUn%2Ba2r%2BinhOWIkjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85pa56JW%2BOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwyOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwwMS0wMzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BOz4%2BO3Q8O2w8aTwwPjtpPDE%2BO2k8Mj47aTwzPjtpPDQ%2BO2k8NT47PjtsPHQ8cDxwPGw8VGV4dDs%2BO2w855S16Lev5YiG5p6Q5a6e6aqMQTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85byg5bOwL%2BW8oOWLh%2BW8ui%2Fmlrnoirg7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPDEuMDs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8MDAtMDA7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCZuYnNwXDs7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCZuYnNwXDs7Pj47Pjs7Pjs%2BPjs%2BPjs%2BPjt0PEAwPHA8cDxsPFBhZ2VDb3VudDtfIUl0ZW1Db3VudDtfIURhdGFTb3VyY2VJdGVtQ291bnQ7RGF0YUtleXM7PjtsPGk8MT47aTwwPjtpPDA%2BO2w8Pjs%2BPjs%2BOzs7Ozs7Ozs7Oz47Oz47dDxAMDxwPHA8bDxQYWdlQ291bnQ7XyFJdGVtQ291bnQ7XyFEYXRhU291cmNlSXRlbUNvdW50O0RhdGFLZXlzOz47bDxpPDE%2BO2k8NT47aTw1PjtsPD47Pj47Pjs7Ozs7Ozs7Ozs%2BO2w8aTwwPjs%2BO2w8dDw7bDxpPDE%2BO2k8Mj47aTwzPjtpPDQ%2BO2k8NT47PjtsPHQ8O2w8aTwwPjtpPDE%2BO2k8Mj47aTwzPjtpPDQ%2BOz47bDx0PHA8cDxsPFRleHQ7PjtsPDIwMTUtMjAxNjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8MTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w855S15a2Q5a6e5LmgOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzmnY7mmZPls7A7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPDEuMDs%2BPjs%2BOzs%2BOz4%2BO3Q8O2w8aTwwPjtpPDE%2BO2k8Mj47aTwzPjtpPDQ%2BOz47bDx0PHA8cDxsPFRleHQ7PjtsPDIwMTUtMjAxNjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8MTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85bel56iL5a6e6Le1SS3mnLrnlLU7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOaWveWutuagizs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Mjs%2BPjs%2BOzs%2BOz4%2BO3Q8O2w8aTwwPjtpPDE%2BO2k8Mj47aTwzPjtpPDQ%2BOz47bDx0PHA8cDxsPFRleHQ7PjtsPDIwMTUtMjAxNjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8MTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85aSn5a2m55Sf6IGM5Lia55Sf5rav6KeE5YiSOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzmlrnolb47Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPDI7Pj47Pjs7Pjs%2BPjt0PDtsPGk8MD47aTwxPjtpPDI%2BO2k8Mz47aTw0Pjs%2BO2w8dDxwPHA8bDxUZXh0Oz47bDwyMDE1LTIwMTY7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPDE7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOeUtei3r%2BWIhuaekOWunumqjEE7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW8oOWzsC%2FlvKDli4flvLov5pa56Iq4Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwxLjA7Pj47Pjs7Pjs%2BPjt0PDtsPGk8MD47aTwxPjtpPDI%2BO2k8Mz47aTw0Pjs%2BO2w8dDxwPHA8bDxUZXh0Oz47bDwyMDE1LTIwMTY7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPDE7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOaWh%2BeMruajgOe0ojs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85bq35qGC6IuxOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwxOz4%2BOz47Oz47Pj47Pj47Pj47dDxAMDxwPHA8bDxQYWdlQ291bnQ7XyFJdGVtQ291bnQ7XyFEYXRhU291cmNlSXRlbUNvdW50O0RhdGFLZXlzOz47bDxpPDE%2BO2k8MTY%2BO2k8MTY%2BO2w8Pjs%2BPjs%2BOzs7Ozs7Ozs7Oz47bDxpPDA%2BOz47bDx0PDtsPGk8MT47aTwyPjtpPDM%2BO2k8ND47aTw1PjtpPDY%2BO2k8Nz47aTw4PjtpPDk%2BO2k8MTA%2BO2k8MTE%2BO2k8MTI%2BO2k8MTM%2BO2k8MTQ%2BO2k8MTU%2BO2k8MTY%2BOz47bDx0PDtsPGk8MD47aTwxPjtpPDI%2BO2k8Mz47aTw0PjtpPDU%2BO2k8Nj47aTw3PjtpPDg%2BOz47bDx0PHA8cDxsPFRleHQ7PjtsPOeUtei3r%2BWIhuaekOWunumqjEE7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPDE7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW%2FheS%2Fruivvjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85L%2Bh5oGv5LiO55S15a2Q5a2m6ZmiOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlv4Xkv67or747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW8oOWzsDs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85ZGo5LiJ56ysOCw5LDEw6IqCe%2BesrDgtOOWRqH1cO%2BWRqOS4ieesrDgsOSwxMOiKgnvnrKwxMC0xMOWRqH1cO%2BWRqOS4ieesrDgsOSwxMOiKgnvnrKwxMi0xMuWRqH1cO%2BWRqOS4ieesrDgsOSwxMOiKgnvnrKwxNC0xNOWRqH1cO%2BWRqOS4ieesrDgsOSwxMOiKgnvnrKwxNi0xNuWRqH07Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOiJr%2BS5oeeQhuWtpualvELmoIs0MDZcO%2BiJr%2BS5oeeQhuWtpualvELmoIs0MDZcO%2BiJr%2BS5oeeQhuWtpualvELmoIs0MDZcO%2BiJr%2BS5oeeQhuWtpualvELmoIs0MDZcO%2BiJr%2BS5oeeQhuWtpualvELmoIs0MDY7Pj47Pjs7Pjs%2BPjt0PDtsPGk8MD47aTwxPjtpPDI%2BO2k8Mz47aTw0PjtpPDU%2BO2k8Nj47aTw3PjtpPDg%2BOz47bDx0PHA8cDxsPFRleHQ7PjtsPOW3peeoi%2BWunui3tUkt5py655S1Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwyOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlv4Xkv67or747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCZuYnNwXDs7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOacuueUteWtpumZojs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85b%2BF5L%2Bu6K%2B%2BOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzmlr3lrrbmoIs7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCZuYnNwXDs7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCZuYnNwXDs7Pj47Pjs7Pjs%2BPjt0PDtsPGk8MD47aTwxPjtpPDI%2BO2k8Mz47aTw0PjtpPDU%2BO2k8Nj47aTw3PjtpPDg%2BOz47bDx0PHA8cDxsPFRleHQ7PjtsPOacuuWZqOS6uuamguiuujs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Mi4wOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzmoKHlhazpgInor747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOmAmuivhuaVmeiCsumAieS%2Fruivvjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w855Sf5ZG95a2m6ZmiOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzmoKHlhazpgInor747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOefs%2Beri%2BS8nzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85ZGo5Zub56ysMTEsMTIsMTPoioJ756ysNC0xNOWRqH07Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOiJr%2BS5oTEtMTA5Oz4%2BOz47Oz47Pj47dDw7bDxpPDA%2BO2k8MT47aTwyPjtpPDM%2BO2k8ND47aTw1PjtpPDY%2BO2k8Nz47aTw4Pjs%2BO2w8dDxwPHA8bDxUZXh0Oz47bDznlLXot6%2FliIbmnpDln7rnoYBBOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwzLjU7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW%2FheS%2Fruivvjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85L%2Bh5oGv5LiO55S15a2Q5a2m6ZmiOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlv4Xkv67or747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOmrmOa0quawkTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85ZGo5LiA56ysOCw56IqCe%2BesrDQtMTflkah9XDvlkajlm5vnrKw4LDnoioJ756ysNC0xN%2BWRqH07Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOiJr%2BS5oTItQTEwNVw76Imv5LmhMi1BNDAzOz4%2BOz47Oz47Pj47dDw7bDxpPDA%2BO2k8MT47aTwyPjtpPDM%2BO2k8ND47aTw1PjtpPDY%2BO2k8Nz47aTw4Pjs%2BO2w8dDxwPHA8bDxUZXh0Oz47bDznlLXot6%2FliIbmnpDlrp7pqoxBOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwxLjA7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW%2FheS%2Fruivvjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85L%2Bh5oGv5LiO55S15a2Q5a2m6ZmiOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlv4Xkv67or747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW8oOWzsC%2FlvKDli4flvLov5pa56Iq4Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwmbmJzcFw7Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwmbmJzcFw7Oz4%2BOz47Oz47Pj47dDw7bDxpPDA%2BO2k8MT47aTwyPjtpPDM%2BO2k8ND47aTw1PjtpPDY%2BO2k8Nz47aTw4Pjs%2BO2w8dDxwPHA8bDxUZXh0Oz47bDznkIborrrlipvlraZCOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDw1LjA7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW%2FheS%2Fruivvjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85a6H6Iiq5a2m6ZmiOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlv4Xkv67or747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOeZveiLpemYszs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85ZGo5LiJ56ysMyw0LDXoioJ756ysNC0xOeWRqH1cO%2BWRqOS6lOesrDEsMuiKgnvnrKw0LTE55ZGofTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w86Imv5LmhMS0yMDdcO%2BiJr%2BS5oTEtMjA3Oz4%2BOz47Oz47Pj47dDw7bDxpPDA%2BO2k8MT47aTwyPjtpPDM%2BO2k8ND47aTw1PjtpPDY%2BO2k8Nz47aTw4Pjs%2BO2w8dDxwPHA8bDxUZXh0Oz47bDzmpoLnjofkuI7mlbDnkIbnu5%2ForqE7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPDM7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW%2FheS%2Fruivvjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85pWw5a2m5a2m6ZmiOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlv4Xkv67or747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW8oOelluWujzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85ZGo5LiA56ysMSwy6IqCe%2BesrDQtMTXlkah9XDvlkajkuInnrKw2LDfoioJ756ysNC0xNeWRqH07Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOiJr%2BS5oTItQTIwNFw76Imv5LmhMi1BMjA0Oz4%2BOz47Oz47Pj47dDw7bDxpPDA%2BO2k8MT47aTwyPjtpPDM%2BO2k8ND47aTw1PjtpPDY%2BO2k8Nz47aTw4Pjs%2BO2w8dDxwPHA8bDxUZXh0Oz47bDzlpKflrabnlJ%2FogYzkuJrnlJ%2Fmtq%2Fop4TliJI7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPDI7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW%2FheS%2Fruivvjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85py655S15a2m6ZmiOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlv4Xkv67or747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOaWueiVvjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BOz4%2BO3Q8O2w8aTwwPjtpPDE%2BO2k8Mj47aTwzPjtpPDQ%2BO2k8NT47aTw2PjtpPDc%2BO2k8OD47PjtsPHQ8cDxwPGw8VGV4dDs%2BO2w85aSn5a2m54mp55CG4oWhOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDw0Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlv4Xkv67or747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCZuYnNwXDs7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOeJqeeQhuWtpumZojs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85b%2BF5L%2Bu6K%2B%2BOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzpgqLnh5XpnJ47Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOWRqOS6jOesrDMsNOiKgnvnrKw0LTE55ZGofVw75ZGo5Zub56ysMyw06IqCe%2BesrDQtMTnlkah9Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzoia%2FkuaExLTIwM1w76Imv5LmhMS0yMDM7Pj47Pjs7Pjs%2BPjt0PDtsPGk8MD47aTwxPjtpPDI%2BO2k8Mz47aTw0PjtpPDU%2BO2k8Nj47aTw3PjtpPDg%2BOz47bDx0PHA8cDxsPFRleHQ7PjtsPOavm%2BazveS4nOaAneaDs%2BS4juS4reWbveeJueiJsuekvuS8muS4u%2BS5ieeQhuiuuuS9k%2Bezu%2Bamguiuujs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8NDs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85b%2BF5L%2Bu6K%2B%2BOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwmbmJzcFw7Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzkurrmlofkuI7npL7kvJrnp5HlrablrabpmaI7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW%2FheS%2Fruivvjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85byg6Zu3Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlkajkuInnrKwxMSwxMiwxM%2BiKgnvnrKw0LTE55ZGofTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w86Imv5LmhMS0xMDM7Pj47Pjs7Pjs%2BPjt0PDtsPGk8MD47aTwxPjtpPDI%2BO2k8Mz47aTw0PjtpPDU%2BO2k8Nj47aTw3PjtpPDg%2BOz47bDx0PHA8cDxsPFRleHQ7PjtsPOaVsOWtl%2BWbvuWDj%2BWkhOeQhuaKgOacr%2BS4juWunui3tTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8My4wOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzmoKHlhazpgInor747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOWunumqjOmAieS%2Fruivvjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85L%2Bh5oGv5LiO55S15a2Q5a2m6ZmiOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzmoKHlhazpgInor747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOa9mOS4veaVjy%2Fpq5jlubM7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOWRqOS4gOesrDExLDEyLDEz6IqCe%2BesrDQtMTnlkah9Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzoia%2FkuaExLTEwODs%2BPjs%2BOzs%2BOz4%2BO3Q8O2w8aTwwPjtpPDE%2BO2k8Mj47aTwzPjtpPDQ%2BO2k8NT47aTw2PjtpPDc%2BO2k8OD47PjtsPHQ8cDxwPGw8VGV4dDs%2BO2w86Iux6K%2Bt5Y%2Bj6K%2Bt77yI5aSW5pWZ77yJOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwyOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzmoKHlhazpgInor747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOS4k%2BmhueiLseivrTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85aSW5Zu96K%2Bt5a2m6ZmiOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzmoKHlhazpgInor747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPERhbiBSb2xmZTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85ZGo5LiA56ysMyw06IqCe%2BesrDQtMTnlkah9Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzoia%2FkuaEyLUIyMDU7Pj47Pjs7Pjs%2BPjt0PDtsPGk8MD47aTwxPjtpPDI%2BO2k8Mz47aTw0PjtpPDU%2BO2k8Nj47aTw3PjtpPDg%2BOz47bDx0PHA8cDxsPFRleHQ7PjtsPOeUteWtkOWunuS5oDs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8MS4wOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlv4Xkv67or747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCZuYnNwXDs7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOacuueUteWtpumZojs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85b%2BF5L%2Bu6K%2B%2BOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzmnY7mmZPls7A7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCZuYnNwXDs7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCZuYnNwXDs7Pj47Pjs7Pjs%2BPjt0PDtsPGk8MD47aTwxPjtpPDI%2BO2k8Mz47aTw0PjtpPDU%2BO2k8Nj47aTw3PjtpPDg%2BOz47bDx0PHA8cDxsPFRleHQ7PjtsPOeJqeeQhuWunumqjELvvIjihaHvvIk7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPDI7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW%2FheS%2Fruivvjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w854mp55CG5a2m6ZmiOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlv4Xkv67or747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOWGr%2BeSkDs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85ZGo5LqM56ysMTEsMTIsMTPoioJ756ysNC0xNeWRqH07Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOiJr%2BS5oeeJqeeQhuWunumqjOS4reW%2Fg%2BalvDs%2BPjs%2BOzs%2BOz4%2BO3Q8O2w8aTwwPjtpPDE%2BO2k8Mj47aTwzPjtpPDQ%2BO2k8NT47aTw2PjtpPDc%2BO2k8OD47PjtsPHQ8cDxwPGw8VGV4dDs%2BO2w85L2T6IKyL%2Be9keeQgyjnlLflpbMpOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwxOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlv4Xkv67or747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCZuYnNwXDs7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOS9k%2BiCsumDqDs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85b%2BF5L%2Bu6K%2B%2BOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzmnY7kuqznlJ87Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOWRqOS6lOesrDMsNOiKgnvnrKw0LTE55ZGofTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8572R55CD5Zy6Oz4%2BOz47Oz47Pj47dDw7bDxpPDA%2BO2k8MT47aTwyPjtpPDM%2BO2k8ND47aTw1PjtpPDY%2BO2k8Nz47aTw4Pjs%2BO2w8dDxwPHA8bDxUZXh0Oz47bDzmlofnjK7mo4DntKI7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPDE7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW%2FheS%2Fruivvjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w85Zu%2B5Lmm6aaGOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlv4Xkv67or747Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOW6t%2BahguiLsTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8Jm5ic3BcOzs%2BPjs%2BOzs%2BOz4%2BOz4%2BOz4%2BO3Q8QDA8cDxwPGw8UGFnZUNvdW50O18hSXRlbUNvdW50O18hRGF0YVNvdXJjZUl0ZW1Db3VudDtEYXRhS2V5czs%2BO2w8aTwxPjtpPDc%2BO2k8Nz47bDw%2BOz4%2BOz47Ozs7Ozs7Ozs7PjtsPGk8MD47PjtsPHQ8O2w8aTwxPjtpPDI%2BO2k8Mz47aTw0PjtpPDU%2BO2k8Nj47aTw3Pjs%2BO2w8dDw7bDxpPDA%2BO2k8MT47aTwyPjtpPDM%2BO2k8ND47aTw1PjtpPDY%2BO2k8Nz47PjtsPHQ8cDxwPGw8VGV4dDs%2BO2w8dDAyOTczOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDx0MDI5NzM7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOihpTAwNTE7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCgyMDE1LTIwMTYtMSktTUVDMDEwNzUtdDAyOTczLTE7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOeQhuiuuuWKm%2BWtpkI7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOeZveiLpemYsyjlrofoiKrlrabpmaIpJm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w855m96Iul6ZizKOWuh%2BiIquWtpumZoinlkagy56ysMTHoioLov57nu60z6IqCe%2BesrDE4LTE45ZGo5Y%2BM5ZGofS%2Foia%2FkuaExLTIwNjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8MjAxNS0xMi0yNC0wOS0xNTs%2BPjs%2BOzs%2BOz4%2BO3Q8O2w8aTwwPjtpPDE%2BO2k8Mj47aTwzPjtpPDQ%2BO2k8NT47aTw2PjtpPDc%2BOz47bDx0PHA8cDxsPFRleHQ7PjtsPHQwMjk3Mzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8dDAyOTczOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzooaUwMDUyOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwoMjAxNS0yMDE2LTEpLU1FQzAxMDc1LXQwMjk3My0xOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDznkIborrrlipvlraZCOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDznmb3oi6XpmLMo5a6H6Iiq5a2m6ZmiKSZuYnNwXDs7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOeZveiLpemYsyjlrofoiKrlrabpmaIp5ZGoNOesrDEx6IqC6L%2Be57utM%2BiKgnvnrKwxOC0xOOWRqOWPjOWRqH0v6Imv5LmhMS0yMDY7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPDIwMTUtMTItMjQtMDktMTc7Pj47Pjs7Pjs%2BPjt0PDtsPGk8MD47aTwxPjtpPDI%2BO2k8Mz47aTw0PjtpPDU%2BO2k8Nj47aTw3Pjs%2BO2w8dDxwPHA8bDxUZXh0Oz47bDx0MDI5NzM7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPHQwMjk3Mzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w86KGlMDA1Mzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8KDIwMTUtMjAxNi0xKS1NRUMwMTA3NS10MDI5NzMtMTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w855CG6K665Yqb5a2mQjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w855m96Iul6ZizKOWuh%2BiIquWtpumZoikmbmJzcFw7Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDznmb3oi6XpmLMo5a6H6Iiq5a2m6ZmiKeWRqDLnrKwxMeiKgui%2Fnue7rTPoioJ756ysMTktMTnlkajljZXlkah9L%2BiJr%2BS5oTEtMjA2Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwyMDE1LTEyLTI0LTA5LTE4Oz4%2BOz47Oz47Pj47dDw7bDxpPDA%2BO2k8MT47aTwyPjtpPDM%2BO2k8ND47aTw1PjtpPDY%2BO2k8Nz47PjtsPHQ8cDxwPGw8VGV4dDs%2BO2w8dDAyOTczOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDx0MDI5NzM7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOihpTAwNTQ7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCgyMDE1LTIwMTYtMSktTUVDMDEwNzUtdDAyOTczLTE7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOeQhuiuuuWKm%2BWtpkI7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOeZveiLpemYsyjlrofoiKrlrabpmaIpJm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w855m96Iul6ZizKOWuh%2BiIquWtpumZoinlkag056ysMTHoioLov57nu60z6IqCe%2BesrDE5LTE55ZGo5Y2V5ZGofS%2Foia%2FkuaExLTIwNjs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8MjAxNS0xMi0yNC0wOS0xODs%2BPjs%2BOzs%2BOz4%2BO3Q8O2w8aTwwPjtpPDE%2BO2k8Mj47aTwzPjtpPDQ%2BO2k8NT47aTw2PjtpPDc%2BOz47bDx0PHA8cDxsPFRleHQ7PjtsPHQwMzkzOTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8dDAzOTM5Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzooaUwMDU3Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwoMjAxNS0yMDE2LTEpLUVMQzA1MDA5LXQwMzkzOS0xOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDznlLXot6%2FliIbmnpDln7rnoYBBOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzpq5jmtKrmsJEo5L%2Bh5oGv5LiO55S15a2Q5a2m6ZmiKSZuYnNwXDs7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOmrmOa0quawkSjkv6Hmga%2FkuI7nlLXlrZDlrabpmaIp5ZGoMeesrDjoioLov57nu60y6IqCe%2BesrDE4LTE45ZGofS%2Foia%2FkuaEyLUExMDU7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPDIwMTUtMTItMjQtMTAtMDI7Pj47Pjs7Pjs%2BPjt0PDtsPGk8MD47aTwxPjtpPDI%2BO2k8Mz47aTw0PjtpPDU%2BO2k8Nj47aTw3Pjs%2BO2w8dDxwPHA8bDxUZXh0Oz47bDx0MDM5Mzk7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPHQwMzkzOTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w86KGlMDA1ODs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8KDIwMTUtMjAxNi0xKS1FTEMwNTAwOS10MDM5MzktMTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w855S16Lev5YiG5p6Q5Z%2B656GAQTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w86auY5rSq5rCRKOS%2FoeaBr%2BS4jueUteWtkOWtpumZoikmbmJzcFw7Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDzpq5jmtKrmsJEo5L%2Bh5oGv5LiO55S15a2Q5a2m6ZmiKeWRqDTnrKw46IqC6L%2Be57utMuiKgnvnrKwxOC0xOOWRqH0v6Imv5LmhMi1BNDAzOz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDwyMDE1LTEyLTI0LTEwLTAyOz4%2BOz47Oz47Pj47dDw7bDxpPDA%2BO2k8MT47aTwyPjtpPDM%2BO2k8ND47aTw1PjtpPDY%2BO2k8Nz47PjtsPHQ8cDxwPGw8VGV4dDs%2BO2w8dDAzOTM5Oz4%2BOz47Oz47dDxwPHA8bDxUZXh0Oz47bDx0MDM5Mzk7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOihpTAwNjQ7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPCgyMDE1LTIwMTYtMSktRUxDMDUwMDktdDAzOTM5LTE7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOeUtei3r%2BWIhuaekOWfuuehgEE7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOmrmOa0quawkSjkv6Hmga%2FkuI7nlLXlrZDlrabpmaIpJm5ic3BcOzs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w86auY5rSq5rCRKOS%2FoeaBr%2BS4jueUteWtkOWtpumZoinlkagx56ysOOiKgui%2Fnue7rTPoioJ756ysMTktMTnlkah9L%2BiJr%2BS5oTItQTMwNTs%2BPjs%2BOzs%2BO3Q8cDxwPGw8VGV4dDs%2BO2w8MjAxNi0wMS0wNS0wOS01MTs%2BPjs%2BOzs%2BOz4%2BOz4%2BOz4%2BOz4%2BOz4%2BOz6bLfDAoBmdcqTd9a%2FI%2FOIQLxoCwQ%3D%3D';

	function __construct($username=null, $password=null) {
		$this->username = $username;
		$this->password = $password;
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1 bPlanner" );
		// curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookie );
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt($this->ch, CURLOPT_ENCODING, "" );
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($this->ch, CURLOPT_AUTOREFERER, true );
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 3 );
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 5 );
		curl_setopt($this->ch, CURLOPT_MAXREDIRS, 3 );
		// curl_setopt($this->ch, CURLOPT_PROXY, 'http://127.0.0.1:8888');
	}

	function __destruct(){
		curl_close($this->ch);
	}

	static function getWeekLangArr($key = 0){
		return $key?array_flip(self::$weekLangArr):self::$weekLangArr;
	}

	private function setSessionPath($path){
		$this->sessionPath = $path;
		curl_setopt($this->ch, CURLOPT_REFERER, $path.'/xs_main.aspx');
	}

	private function getCH(){
		return curl_copy_handle($this->ch);
	}

	function login(){
		$ch = $this->getCH();
		curl_setopt($ch, CURLOPT_URL, "http://10.5.2.80/default2.aspx");
		curl_exec($ch);
		$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "__VIEWSTATE=dDwtMjEzNzcwMzMxNTs7Pj9pP88cTsuxYpAH69XV04GPpkse&TextBox1=".$this->username."&TextBox2=".urlencode($this->password)."&RadioButtonList1=%D1%A7%C9%FA&Button1=+%B5%C7+%C2%BC");
		$result = curl_exec($ch);
		$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
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
			}else if(curl_errno($ch) == 28){
				$this->last_error = 'Itimeout_error';
				return false;
			}else{
				$this->last_error = 'Iparse_error';
				var_dump($result);
				var_dump(curl_error($ch));
			}
			return false;
		}
	}

	function getSchedulePage($year=null, $term=null){
		$ch = $this->getCH();
		curl_setopt($ch, CURLOPT_URL, $this->sessionPath."/xskbcx.aspx?xh=".$this->username."&xm=&gnmkdm=N121603");
		if($year && $term){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, self::$schedulePagePostViewState."&xnd=".$year."&xqd=".$term);
		}
		$html = curl_exec($ch);
		$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

		if(strpos($url, 'xskbcx.aspx')){
			if(preg_match("/alert\(\'(.+)'\);/", $html, $alertMatches)){
				$this->last_error = iconv('GB2312', 'UTF-8//IGNORE', $alertMatches[1]);
				return false;
			}

			$doc = new DOMDocument();
			@$doc->loadHTML($html);

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
			}else if(curl_errno($ch) == 28){
				$this->last_error = 'Itimeout_error';
				return false;
			}else if($html === false){
				var_dump(curl_errno($ch), curl_error($ch));
				$this->last_error = 'Iservice_error';
				return false;
			}else{
				var_dump($html);
				$this->last_error = 'Iparse_error';
				return false;
			}
		}else{
			var_dump($url);
			$this->last_error = 'Iservice_error';
			return false;
		}
	}

	function parseScheduleTableMain(DOMElement $xD){
		$return = [];
		foreach($xD->getElementsByTagName('tr') as $i=>$tr){
			if($i == 0) continue;//thead

			$tds = $tr->getElementsByTagName('td');//nodeValue

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
							if($l->changesID == '调0133') var_dump($lsHash, $lcs->originalHashPerWeek, $lcs->getWeekText());
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

	function parseScheduleTableChanges(DOMElement $xD){
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

			if(strpos($changesID, '换') === 0 || strpos($changesID, '调') === 0){
				$ld = $this->_parseScheduleTableChangesLessonText($tds->item(2)->nodeValue, 'LessonBITDeletion');
				$ld->changesID = $changesID;
				$ld->changesTime = $tds->item(4)->nodeValue;
				$ld->name = $tds->item(1)->nodeValue;

				$l->schedule[0]->originalHashPerWeek = $ld->schedule[0]->getHashPerWeek();

				$returnl[] = $ld;
				//$returnd = $this->mergeChangedLesson($returnd, $ld);
			}

			$returnl[] = $l;
			//$returnl = $this->mergeChangedLesson($returnl, $l);
		}

		// Sort it
		return array_interlace($returnl, $returnd);
	}

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
	function getSchoolCalendarFetch($year, $term){
		if(isset($this->calendarCache[$year.'-'.$term][0])){
			$result = $this->calendarCache[$year.'-'.$term][0];
		}else{
			$ch = $this->getCH();
			curl_setopt($ch, CURLOPT_URL, 'http://weixin.info.bit.edu.cn/schoolCalendar/wechatQuery?code='.$year.'-'.$term);
			$result = curl_exec($ch);
			$this->calendarCache[$year.'-'.$term][0] = $result;
		}

		return $result;
	}

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

		$id = $week*7-8+$weekday;
		$orig = 1;
		$day = (int) $result->item($id)->nodeValue;

		$month = $day;
		while(strpos($month, '月') == false && $id){
			$id--;
			$orig = 0;
			$month = $result->item($id)->nodeValue;
		}
		if(!$id){
			$id++;
			while(strpos($month, '月') == false && $id){
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
}

Class LessonBIT{
	public $name = '';
	public $credit = 0;
	public $category = '';
	public $tutor = '';
	public $department = '';
	public $changesID = null;
	public $changesTime = null;
	public $schedule = array();
	function __construct($array=array()) {
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
	}
}

class LessonBITDeletion extends LessonBIT{

}
Class LessonScheduleBIT{
	public $week = [];
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