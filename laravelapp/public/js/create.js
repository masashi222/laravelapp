const weeks = ['日', '月', '火', '水', '木', '金', '土'];
const date = new Date();
let year = date.getFullYear();
let month = date.getMonth() + 2;

function showCalendar(year, month){
        const calendarHtml = createCalendar(year,month);
        document.getElementById('calendar').innerHTML = calendarHtml;
}

function createCalendar(year,month){
    const startDate = new Date(year,month-1,1);//月の最初の日の取得
    const endDate = new Date(year,month,0);//月の最後の日の取得
    const endDayCount = endDate.getDate();//月の日数
    const lastMonthEndDate = new Date(year,month-1,0);//前月の最後の日を取得
    const lastMonthendDayCount = lastMonthEndDate.getDate();//前月の日数
    const startDay = startDate.getDay();//月の最初の日の曜日の獲得
    let dayCount = 1;//日にちのカウント
    let calendarHtml = '';//HTMLを組み立てる変数

    let periodHtml = year + '/' + month ;
    document.getElementById('period').textContent = periodHtml;

    calendarHtml += '<thead>' + '<tr>';

    //曜日の行の作成
    for(let i=0;i<weeks.length;i++){
        calendarHtml += '<th>' + weeks[i] + '</th>';
        }

    calendarHtml += '</tr>' + '</thead>' + '<tbody>';

    for(let w=0;w<6;w++){
        calendarHtml += '<tr>'

        for(let d=0;d<7;d++){
         if (w == 0 && d < startDay) {
                // 1行目で1日の曜日の前
                let num = lastMonthendDayCount - startDay + d + 1;
                calendarHtml += '<td class="text-light border">' + num + '</td>';
            } else if (dayCount > endDayCount) {
                // 末尾の日数を超えた
                let num = dayCount - endDayCount;
                calendarHtml += '<td class="text-light border">' + num + '</td>';
                dayCount++;
            } else {
                calendarHtml += '<td class="border" onclick="showDialog();" data-id=\'{"year":' +year+ ',"month":' +month+ ',"date":' +dayCount+ '}\'>'
                + dayCount + '<br>' + '</td>';
                dayCount++;
            }
        }
        calendarHtml += '</tr>'
    }
    calendarHtml += '</tbody>';

    return calendarHtml;
}

function prevCalendar() {
    document.getElementById('calendar').innerHTML = '';

        month--;

        if (month < 1) {
            year--;
            month = 12;
        }
        showCalendar(year, month);
}

function nextCalendar() {
    document.getElementById('calendar').innerHTML = '';

        month++;

        if (month > 12) {
            year++;
            month = 1;
        }
        showCalendar(year, month);
}

showCalendar(year, month);

//シフト一覧画面へ(< 戻る)
function back(){
	$(document).on('click','.back',function(){
		window.location.href="/staff/shift-record";
	});
}

//シフト作成詳細ダイアログ
function showDialog(){
	$(document).on('click','td',function(){
		//モーダルダイアログ
		$('#dialog1').modal();
		//シフト詳細の日付
		$('.modal-title').text('');
		var dataYear = $(this).data('id').year;
		var dataMonth = $(this).data('id').month;
		var dataDate = $(this).data('id').date;
		var dateFull = new Date(dataYear,dataMonth-1,dataDate);
		var dataDay = weeks[dateFull.getDay()];
		var dateText = dataMonth+'/'+dataDate+'('+dataDay+')';
		var dataMonth_edit = ('0' + dataMonth).slice(-2);
		var dataDate_edit = ('0' + dataDate).slice(-2);
		var dateInfo = dataYear+'-'+dataMonth_edit+'-'+dataDate_edit;
		$('.modal-title').text(dateText);
		document.getElementById("date-info").setAttribute("value",dateInfo);
	});
}
