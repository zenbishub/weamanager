function uhrzeit() {
   
    var showzeit = document.getElementById("uhrzeit");
    if(document.body.contains(showzeit)){
         Heute = new Date();
         Stunde  = Heute.getHours();
         Minute  = Heute.getMinutes();
         Sekunde = Heute.getSeconds();

         //showzeit.innerHTML=Stunde+":"+((Minute<=9)?"0"+Minute:Minute)+":"+((Sekunde<=9)?"0"+Sekunde:Sekunde);
         showzeit.innerHTML=Stunde+":"+((Minute<=9)?"0"+Minute:Minute);
    }
   
 }
    function GetDay(intDay){
       var DayArray = new Array("So. ", "Mo. ", "Di. ", "Mi. ", "Do. ", "Fr. ", "Sa. ");
       return DayArray[intDay];
    }
    function GetMonth(intMonth){
       var MonthArray = new Array("Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
       return MonthArray[intMonth];
    }
    function getDateStrWithDOW(){
      var  showdatum = document.getElementById("showdatum");
      if(document.body.contains(showdatum)){
       var today = new Date();
       var year = today.getYear();
       if(year<1000) year+=1900
       var todayStr = "";
       todayStr += today.getDate() + ". " + GetMonth(today.getMonth())+" "+GetDay(today.getDay());
       //todayStr += ", " + year;

       showdatum.innerHTML=todayStr;
      }
       
    }
    $(function(){
      setInterval(uhrzeit,1000);
    });
