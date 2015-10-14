function exec (intra_autolog, google_autolog){
var page = require('webpage').create();
page.viewportSize = {
  width: 1080,
  height: 800
};

var d = new Date();
var month = (d.getMonth() + 1).toString();
var date = d.getDate().toString();
if (d.getDate() < 10)
	date = '0' + date;
var year = d.getFullYear().toString();

var d2 = new Date();
d2.setDate((d.getDate() + 7));
var month2 = (d2.getMonth() + 1).toString();
var date2 = d2.getDate().toString();
if (d2.getDate() < 10)
	date2 = '0' + date2;
var year2 = d2.getFullYear().toString();

page.open('https://intra.epitech.eu/auth-' + intra_autolog + '/planning/#!/?start=' + year + '-' + month + '-' + date + '&end='+ year2 +'-'+ month2 +'-'+ date2 +'&onlymyevent=true&perso=2838:1|2841:1', function(status) {
  console.log("Status: " + status);
  if(status === "success") {
	window.setTimeout(function () {
            page.render('example.png');
            page.includeJs("http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js", function() {
			var ret = page.evaluate(function() {
						
				var result = '';
				var iday = 0;
				var day = new Array();
				$("table.rdv").children().children().children().each(function( index ) {
					//chaque jour
					day[iday] = new Array();
					var ievent = 0;
					$(this).children().children().each(function( index ) {
							var desc = $(this).children().children().children().children().children("a").text();
							if (desc != "")
							{
								day[iday][ievent] = new Array();
								var start = $(this).children("h4").text().split(" – ")[0];
								var starth = start.split(":")[0];
								var startm = start.split(":")[1];
								var end = $(this).children("h4").text().split(" – ")[1];
								var endh = end.split(":")[0];
								var endm = end.split(":")[1];
								result = endh;
								day[iday][ievent][0] = starth;
								day[iday][ievent][1] = startm;
								day[iday][ievent][2] = endh;
								day[iday][ievent][3] = endm;
								day[iday][ievent][4] = desc.replace(/[^a-zA-Z0-9 é]/g,'').replace("é","e");
								var d = new Date();
								d.setDate((d.getDate() + iday));
								var month = (d.getMonth() + 1).toString();
								var date = d.getDate().toString();
								if (d.getDate() < 10)
									date = '0' + date;
								var year = d.getFullYear().toString();
								day[iday][ievent][5] = year;
								day[iday][ievent][6] = month;
								day[iday][ievent][7] = date;
								ievent += 1;
							}
					});
					iday += 1;
				});
				result = JSON.stringify(day, null, 2);
				return result;
				});
				tret = JSON.parse(ret);
				console.log(ret);
				exec_request(tret, google_autolog);
			});
        }, 5000);
  }
});	
}



function exec_request(event, google_autolog)
{
	var i = 0;
	while (i != event.length)
	{
		var j = 0;
		while (j != event[i].length)
		{
			var page = require('webpage').create();
			page.open('http://www.snaze.me/g/calsync.php?year=' + event[i][j][5] + '&month=' + event[i][j][6] + '&day=' + event[i][j][7] + '&starth=' + event[i][j][0] + '&startm=' + event[i][j][1] + '&endh=' + event[i][j][2] + '&endm=' + event[i][j][3] + '&desc=' + event[i][j][4] + '&auth=' + google_autolog + '', function(status) {
				console.log("Status: " + status);
				if(status === "success") {
					window.setTimeout(function () {
						page.render("page.png");
						},5000);
				}
			});	
			j++;
		}
		i++;
	}
}
 
 
 function get_google_auth()
 {
	 var system = require('system');
	var args = system.args;
	if (args.length == 2)
	{
		system.stdout.writeLine('please enter google api key: ');
		var line = system.stdin.readLine();
		localStorage.setItem("google", line);
	}
	 var google_api = localStorage.getItem("google");
	 return (google_api);
 }
 
 function get_epitech_auth()
 {
	 var system = require('system');
	var args = system.args;
	if (args.length == 2)
	{
		system.stdout.writeLine('please enter epitech autolog key: ');
		var line = system.stdin.readLine();
		localStorage.setItem("epitech", line);
	}
	 var epitech_autolog = localStorage.getItem("epitech");
	 return (epitech_autolog);
 }
 
 console.log("update at " + new Date());
 exec(get_epitech_auth(), get_google_auth());
 setInterval(function(){ exec(get_epitech_auth(), get_google_auth());console.log("update at " + new Date()); }, 60000 * 60);
