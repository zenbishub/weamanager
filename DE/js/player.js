var global = "";

function chartsOverviewPlayer(openlager,lagertype,index){
 //console.log(index);
    $.post("class/action.php",{playrecords:1,index:index},function(response){
     
       //console.log(response);
        var canvasDiv = document.getElementById("playercanvas"); 
        var start = $("#playercanvas");
        var stampoutput = $("#tstamp");
        var explodeResponseData = response.split("tstamp"); 
        var tstamp = explodeResponseData[1];
        
        var header = $("#exampleModalLongTitle");
        var explodeResponse = explodeResponseData[0].split(";");
        var overview;
        var screenX = $(window).width();
        var screenY = $(window).height()/1.4;
        var titleFontSize=26;
        var titlePadding = 0;
        var barFontSize=16;
        var labelFontSize=15;
        var textAdd="";
   
        var lagerArray = [];
        var firstValue = [];
        var secondValue = [];
        var thirdValue = [];
        var forethValue = [];
        var fifthValue = [];
        var sixtValue = [];
        
        if(lagerdetails!="" && lagerdetails!="Alle"){
          barFontSize=30;
          textAdd = " Lager "+lagerdetails; 
          labelFontSize=15;
          barWidth=20;
        }
        if(lagerdetails=="" || lagerdetails=="Alle"){
          barFontSize=16;
          labelFontSize=13;
          textAdd = "";
        }
        if(screenX<1030){
          titleFontSize=22;
          titlePadding = 5;
          barFontSize=10;
          labelFontSize=12;
        }
        if(screenX<500){
          titleFontSize=12;
          titlePadding = 0;
          barFontSize=8;
          labelFontSize=10;
        }

        // console.log(countRecord);
        // if(countRecord<1){
        //     canvasDiv.innerHTML="Keine Daten vorhanden";
        //     return;
        // }
        if(stampoutput.length==0){
            header.append("<span id='tstamp'></span>");
        }
    
        stampoutput.html(" "+tstamp);
        canvasDiv.innerHTML="<canvas id='myPlayerChart'>lade...</canvas>";
         for(x=0;x<explodeResponse.length;x++){
           var explodeExplodeResponse = explodeResponse[x].split("=>");
           var explodeValues = explodeExplodeResponse[1].split("~");
           lagerArray.push(explodeExplodeResponse[0]);
             
           firstValue.push(explodeValues[0]);
           secondValue.push(explodeValues[1]);
           thirdValue.push(explodeValues[2]);
           forethValue.push(explodeValues[3]);
           fifthValue.push(explodeValues[4]);
           sixtValue.push(explodeValues[5]);
         }
 
 
         Chart.pluginService.register({
          beforeDraw: function (chart, easing) {
            if (chart.config.options.chartArea && chart.config.options.chartArea.backgroundColor) {
              var helpers = Chart.helpers;
              var ctx = chart.chart.ctx;
              var chartArea = chart.chartArea;
    
              ctx.save();
              ctx.fillStyle = chart.config.options.chartArea.backgroundColor;
              ctx.fillRect(chartArea.left, chartArea.top, chartArea.right - chartArea.left, chartArea.bottom - chartArea.top);
              ctx.restore();
            }
          }
          });

       switch(openlager){
     
       case "export_diagramm":
             //if(lagertype=="Automatiklager"){
             var summeAutomatikLager = firstValue.reduce(function (a, b) {
                 return parseInt(a)+parseInt(b);}, 0);
              //  overview =[{
              //    barThickness: 90,
              //    label: "Positionen auf dem Wagen",
              //    type: "bar",
              //    stack: "Sensitivity",
              //    backgroundColor: "#ffcf00",
              //    data: firstValue,
              //    fontColor:'black',
              //    borderColor: "#a50e00",
              //  },{
              //   barThickness: 90,
              //   label: "Auslagerung Offen",
              //   type: "bar",
              //   stack: "Sensitivity",
              //   backgroundColor: "#008eff",
              //   data: secondValue,
              //   fontColor:'black',
              //   borderColor: "#000000",
              //  }
              // ];
 
        //}else{
         overview =[{
          minBarLength: 0,
            label: "Einlagerung Alt",
            type: "bar",
            stack: "Sensitivity",
            backgroundColor: "red",
            data: fifthValue,
            fontColor:'black',
            borderColor: "#a50e00",
            maxBarThickness:100,
            tension: -1
          },{
            minBarLength: 0,
            label: "Einlagerung Offen",
            type: "bar",
            stack: "Sensitivity",
            backgroundColor: "#ffcf00",
            data: firstValue,
            fontColor:'black',
            borderColor: "#a50e00",
            //borderWidth: 2,
            maxBarThickness:100,
            tension: -1,
            },{
            minBarLength: 0,
            label: "Einlagerung Nachschub",
            type: "bar",
            stack: "Sensitivity",
            backgroundColor: "#db1ab4",
            backgroundColorHover: "#00ff84",
            data: secondValue,
            fontColor:'black',
            borderColor: "#007000",
            //borderWidth: 1,
            maxBarThickness:100,
            tension: -1,
            },{
            minBarLength: 0,
            label: "Auslagerung Alt",
            type: "bar",
            stack: "Base",
            backgroundColor: "#ff4f4f",
            data: sixtValue,
            fontColor:'black',
            borderColor: "#000000",
            maxBarThickness:100,
            tension: -1,
            },{
              minBarLength: 0,
          label: "Auslagerung Offen",
          type: "bar",
          stack: "Base",
          backgroundColor: "#008eff",
          data: thirdValue,
          fontColor:'black',
          borderColor: "#000000",
          maxBarThickness:100,
          tension: -1,
            }

        //    minBarLength: 0,
        //    label: "Einlagerung Offen",
        //    type: "bar",
        //    stack: "Sensitivity",
        //    backgroundColor: "#ffcf00",
        //    data: firstValue,
        //    fontColor:'black',
        //    borderColor: "#a50e00",
        //    //borderWidth: 2,
        //  },{
        //    minBarLength: 0,
        //    label: "Einlagerung 1-Stufe",
        //    type: "bar",
        //    stack: "Sensitivity",
        //    backgroundColor: "#56f1ff",
        //    backgroundColorHover: "#00ff84",
        //    data: secondValue,
        //    fontColor:'black',
        //    borderColor: "#007000",
        //    //borderWidth: 1,
        //  },{
        //    minBarLength: 0,
        //    label: "Auslagerung Offen",
        //    type: "bar",
        //    stack: "Base",
        //    backgroundColor: "#ff8787",
        //    data: thirdValue,
        //    fontColor:'black',
        //    borderColor: "#000000",
        //    //borderWidth: 2,
        //  },{
        //    minBarLength: 0,
        //    label: "Auslagerung 1-Stufe",
        //    type: "bar",
        //    //stack: "Sensitivity",
        //    stack: "Base",
        //    backgroundColor: "#56ff56",
        //    backgroundColorHover: "#3e95cd",
        //    data: forethValue,
        //    fontColor:'black',
        //    borderColor: "#000000",
        //  // borderWidth: 1,
          //}
        ];
      //   }
       
       
       break;
       }
 
       const ctx = document.getElementById('myPlayerChart');
           ctx.style.height=screenY+"px";
      new Chart(ctx, {
       
         type: 'bar',
         backgroundColor: "#000000",
         data: {
           labels: lagerArray,
           datasets: overview
         },
       
         options: {
           
           legend: {
             labels: {
                 fontSize:labelFontSize,
                 padding:20                
             }
            },
           responsive: true,
           maintainAspectRatio: false,
           title:{
             display:true,
             text:"Transportaufträge "+lagertype+" "+textAdd,
             fontSize:titleFontSize,
             padding:titlePadding,
             fontColor:'black',
             fontStyle: "normal",
           },
           scales: {
             xAxes: [{
               stacked: true,
               ticks: {
                 fontColor:'black',
                 beginAtZero: true,
                 maxRotation: 0,
                 minRotation: 0
               }
             }],
             yAxes: [{
               stacked: true,
               ticks: {
                 fontSize: 11,
                 fontColor:'grey',
                 beginAtZero: true
             }
             }],
             xAxes: [{
               ticks: {
                 fontSize: 16,
                 fontColor:'black',
                 fontStyle:'bold'
             }
             }],
             
           },
           chartArea: {
             backgroundColor: '#f8f9fa'
           },
           animation: {

                duration:0,
                onComplete: function () {
                  var chartInstance = this.chart,
                  ctx = chartInstance.ctx;
                  ctx.font = Chart.helpers.fontString(barFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                  ctx.textAlign = 'center';
                  ctx.textBaseline = 'top';
                  this.data.datasets.forEach(function (dataset, i)
                  {
                      var meta = chartInstance.controller.getDatasetMeta(i);
                      meta.data.forEach(function (bar, index) {
                      var data = dataset.data[index];

                       if(lagerdetails=="" && data>1){
                        if(i==0 || i==2){
                            if(data<6){
                              ctx.font = barFontSize-5+"px Arial";
                              ctx.fillText(data, bar._model.x, bar._model.y + 1);
                            }else{
                              ctx.font = barFontSize+"px Arial";
                              // if(lagertype=="Automatiklager"){
                              //   ctx.font = "2rem Arial";
                              // }
                              ctx.fillText(data, bar._model.x, bar._model.y + 1);
                            }
                        }else{
                            if(data<6){
                              ctx.font = barFontSize-5+"px Arial";
                              ctx.fillText(data, bar._model.x, bar._model.y - 0);
                            }else{
                              ctx.font = barFontSize+"px Arial";
                              // if(lagertype=="Automatiklager"){
                              //   ctx.font = "2rem Arial";
                              // }
                            ctx.fillText(data, bar._model.x, bar._model.y - 0);
                          }
                        }
                        }

                        if(lagerdetails!="" && data>1){
                          
                          if(i==1 || i==3){

                              if(data>6){
                                ctx.font =barFontSize+"px Arial";
                                ctx.fillText(data, bar._model.x, bar._model.y + 1);
                                
                              }else{
                                ctx.font = barFontSize+"px Arial";
                                // if(lagertype=="Automatiklager"){
                                //   ctx.font = "2rem Arial";
                                // }
                                ctx.fillText(data, bar._model.x, bar._model.y + 1);
                              
                              }
                            }else{
                              if(data<6){
                                ctx.font = barFontSize-6+"px Arial";
                                ctx.fillText(data, bar._model.x, bar._model.y - 0);
                              }else{
                                ctx.font = barFontSize+"px Arial";
                                // if(lagertype=="Automatiklager"){
                                //   ctx.font = "2rem Arial";
                                // }
                              ctx.fillText(data, bar._model.x, bar._model.y - 0);
                            }
                            }
                              
                          
                         
                          }

                      });
                  });
                  // if(lagertype=="Automatiklager"){
                  //   ctx.font = "3.2rem Arial black";
                  //   ctx.fillStyle = '#666';
                  //   ctx.fillText(summeAutomatikLager,55,40);
                  // }
                }
             }
         }
       });
    });
   //https://stacktuts.com/chartjs-multiple-datasets-labels-in-line-chart 
}



function playStop(openlager,lagertype, range){
    var modalContent = $("#modal-content");
    var footer = $("#modal-footer");
    var close = $(".close");
    $.post("class/action.php",{checkRecords:lagertype},function(data){
        
        if(data<1){
            modalContent.html("keine Daten vorhanden");
            //return;
        }
      
        if(range>0){
          index=range;
        }else{
          index=(data-2);
        }
        
    
    var progressbar = $(".progress-bar");
    var calc = 0;
    var elemPlay = $("#startplayer");
    var elemStop = $("#stopplayer");
    var framebackward = $("#framebackward");
    var frameforward = $("#frameforward");
    footer.html("<span class='small float-left' id='countFrames'>abspielbar letzte "+data+" Frames</span>");
    //<span class='pointer' id='deleteHistory' title='Verlauf löschen'><i class='fas fa-redo'></i></span>
    deleteHistory();
    chartsOverviewPlayer(openlager,lagertype,index);
        

              elemPlay.click(function(){
                $(this).addClass("alert-success");
                elemStop.removeClass("alert-success");
                framebackward.removeClass("alert-success").removeClass("change-opacity");;
                frameforward.removeClass("alert-success").removeClass("change-opacity");;

                var interval = setInterval(function(){
                 // console.log(index);
                
                if(index<2){
                    clearInterval(interval);
                    index=data-2;
                    elemPlay.removeClass("alert-success");
                    elemStop.removeClass("alert-success");
                    framebackward.removeClass("alert-success");
                    frameforward.removeClass("alert-success");
                }
                chartsOverviewPlayer(openlager,lagertype,index);
                
                calc = 100-(100*index/data);
               
                progressbar.css({"width":Math.ceil(calc)+"%"});
                index--;

                elemStop.click(function(){
                    
                    $(this).addClass("alert-success");
                    elemPlay.removeClass("alert-success");
                    framebackward.removeClass("alert-success");
                    frameforward.removeClass("alert-success");
                    index=index;
                    clearInterval(interval);
                });
            },250);

            close.click(function(){
              elemPlay.removeClass("alert-success");
              elemStop.removeClass("alert-success");
              framebackward.removeClass("alert-success");
              frameforward.removeClass("alert-success");
              clearInterval(interval);
              index=data-2;
            });
            framebackward.click(function(){
              $(this).addClass("alert-success");
              elemPlay.removeClass("alert-success");
              elemStop.removeClass("alert-success");
              frameforward.removeClass("alert-success");
              index++;
              clearInterval(interval);
              //console.log(index);
              if(index<=(data-2)){
                calc = 100-(100*index/data);
                progressbar.css({"width":Math.ceil(calc)+"%"});
                chartsOverviewPlayer(openlager,lagertype,index);
              }else{
                index=data-2;
              }
            });

             frameforward.click(function(){
              $(this).addClass("alert-success");
              elemPlay.removeClass("alert-success");
              elemStop.removeClass("alert-success");
              framebackward.removeClass("alert-success");
              index--;
              clearInterval(interval);
              //console.log(index);
              if(index>=0){
                calc = 100-(100*index/data);
                progressbar.css({"width":Math.ceil(calc)+"%"});
                chartsOverviewPlayer(openlager,lagertype,index);
                //index=data-2;
              }else{
                index=0;
              }
            });
            
            $("#progressbar-background").click(function(event){
              var totalWidth = $(this).width();
              var progressbar = $(".progress-bar");
              var widthclicked = event.pageX - $(this).offset().left;
              clearInterval(interval);

              elemPlay.removeClass("alert-success");
              elemStop.removeClass("alert-success");
              framebackward.removeClass("alert-success");
              frameforward.removeClass("alert-success");

              calcPercent = ((widthclicked*100)/totalWidth+"%");
              calc = (widthclicked*100)/totalWidth;
              progressbar.width(calcPercent);
              indexnew= Math.floor((calc*data)/100);
              //console.log(data-indexnew);
              index = parseInt(data-indexnew);
             chartsOverviewPlayer(openlager,lagertype,index);
             //console.log(index);
            });
          
            });
            
            $("#progressbar-background").click(function(event){
              var totalWidth = $(this).width();
              var progressbar = $(".progress-bar");
              var widthclicked = event.pageX - $(this).offset().left;
             

              elemPlay.removeClass("alert-success");
              elemStop.removeClass("alert-success");
              framebackward.removeClass("alert-success");
              frameforward.removeClass("alert-success");

              calcPercent = ((widthclicked*100)/totalWidth+"%");
              calc = (widthclicked*100)/totalWidth;
              progressbar.width(calcPercent);
              indexnew= Math.floor((calc*data)/100);
              //console.log(data-indexnew);
              index = parseInt(data-indexnew);
             chartsOverviewPlayer(openlager,lagertype,index);
             //console.log(index);
            });

            //opacityPlayer();
           
    });
    
 }
function deleteHistory(){
  $("#deleteHistory").click(function(){
    var footer = $("#modal-footer");
    if(confirm("Frames in Verlauf löschen?")){
      $.post("class/action.php",{verlaufreset:1},function(){
        footer.html("<span class='small'>Verlauf zurückgesetzt</span>");
      });
      
    }
  });
}
function opacityPlayer(){
  $("#exampleModalLongTitle").hover(function(){
    var changeopacity = $(".change-opacity");
    changeopacity.animate({opacity:1});
  },
  function(){
    var changeopacity = $(".change-opacity");
    setTimeout(function(){
      changeopacity.animate({opacity:.5});
    },30000);
    
  });
}

function searchPlay(interval,index,data){

  $("#progressbar-background").click(function(event){
    var totalWidth = $(this).width();
    var progressbar = $(".progress-bar");
    var widthclicked = event.pageX - $(this).offset().left;
    clearInterval(interval);
    calcPercent = ((widthclicked*100)/totalWidth+"%");
    calc = (widthclicked*100)/totalWidth;
    progressbar.width(calcPercent);
    indexnew= Math.floor((calc*data)/100);
    //console.log(data-indexnew);
    index = parseInt(data-indexnew);
   chartsOverviewPlayer(openlager,lagertype,index);
    global=index;

  });
  //console.log(global);
}
 $(function(){
    playStop(openlager,lagertype); 
    
});