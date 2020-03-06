<script>
    var info=<?= json_encode($data); ?>;
    var reportType=info['reportType'];

    var reportDisplayContent=$(".reportDisplayContent");

    var reportTitle="<h4>"+reportType+"</h4>";
    var data=info['reportInformation'];

    if(reportType=="transactions"){
        reportDisplayContent.append(reportTitle);
        reportDisplayContent.append("<div id='display_bar_graph_general'></div>");

        $("#display_bar_graph_general").css("height","200");

        var transactionResultsGeneral=data['transactionResultsGeneral'];

        drawGraphAnyType(transactionResultsGeneral,'Line','display_bar_graph_general');
    }


    function drawGraphAnyType(data,type, div) {
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var morrisOptions={
            element: div,
            data: data,
            xkey: 'month',
            ykeys: ['orders', 'partners','items'],
            xLabels:['MONTH'],
            xLabelFormat: function (x) { var m=x.src.date.split("-")[1]; return months[m-1];},
            labels:['Orders','Partners',"Items for Rent"],
            pointSize: 2,
            hideHover: 'false',
            resize: true,
            fillOpacity: 0.4,
            pointFillColors:['#fff'],
            pointStrokeColors: ['#dc134c'],
            lineColors:['#1489ab','#dc134c', '#FF0000',"#1d1d1d"],
            grid:'false'};

        if(type=='Line'){
            Morris.Line(morrisOptions);
        }else if(type=='Area'){
            Morris.Area(morrisOptions);
        }else if(type=='Bar'){
            Morris.Bar(morrisOptions);
        }else if(type=='Donut'){
            Morris.Donut(morrisOptions);
        }

    }
</script>
