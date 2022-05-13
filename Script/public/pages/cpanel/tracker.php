<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro:wght@200;300;400;500&display=swap" rel="stylesheet">
    <title>Upload Process Tracker</title>
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing: border-box;
        }
        #app{
            width: 100%;
            height: 100vh;
            background: #222f3e;
            font-family: 'Source Code Pro', monospace;
            overflow: hidden;
            color:#fff;
            font-size: 14px;
            font-weight: 300;
        }
        .top {
            background: #b7b7b7;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color:#3b4651;
        }
        /*#console{*/
        #tracking-tbl-wrap{
            height: calc( 100% - 114px );;
            overflow-y: auto;
        }
        .tracking-tbl {
            width: 100%;
            white-space: nowrap;
            /*border-collapse: collapse;*/
            /*border-spacing: 0;*/
            text-align:center;
        }
        .tracking-tbl  thead tr:nth-child(1) th{
            position: sticky;
            top: 0;
            z-index: 10;
            background: #222f3e;
        }
        .tracking-tbl th,
        .tracking-tbl td {
            padding: 3px 6px;
            border: 1px solid #fff;
        }
        /*#009432*/
        .tracking-tbl.highlight tbody tr[data-status="failed"]{
            background: #b71540;
        }
        .tracking-tbl.highlight tbody tr[data-status="active"]{
            background: #006266;
        }
        .tracking-tbl.highlight tbody tr[data-status="process"]{
            background: #cd6133;
        }
        #tracking-tbl tbody tr:hover{
            background: #e0e0e0;
            color:#000000;
        }
        .tracking-tbl body{
        }
        .tracking-tbl .progress-wrap{
            padding:0 ;
            min-width: 150px;
            background: black;
        }
        .progress-bar{
            padding: 3px;
            background: #e0e0e0;
            height: 28px;
            color:#000000;
            transition: width 0.6s ease;
        }
        .process-summary {
            display: flex;
            padding: 8px;
            justify-content: space-between;
        }
        .ps-status{
            display: flex;
        }
        .ps-item {
            margin: 0 15px;
        }
        .ps-badge{
            padding: 0 4px;
            border-radius: 2px;
            font-weight: bold;
        }
        .ps-badge-total {
            background: #0a6aa1;
        }
        .ps-badge-completed {
            background: #1e7e34;
        }
        .ps-badge-processing {
            background: #e58e26;
        }
        .ps-badge-waiting {
            background: #57606f;
        }
        .ps-badge-failed{
            background: #d63031;
        }
        .success-rate{
            color: #00b894;
            font-weight: bold;
        }
        .controllers{
            display: flex;
            justify-content: space-between;
            padding: 8px;
            border-bottom: 1px solid #fff;
        }
        .c-btn{
            cursor: pointer;
            padding: 0 15px;
        }
    </style>
    <link href="<?php buildResourceURI('assets/backend/css/contextMenu.min.css'); ?>" rel="stylesheet"/>
</head>
<body>
<div id="app">
    <div class="top">
        <h3 class="title"><?php echo $this->pageTitle; ?> <sup>v1.0</sup>  </h3>
        <div class="logo">
            <a href="#"><img src="<?php buildResourceURI('assets/cpanel/img/logo-dark.png'); ?>" height="25" alt=""></a>
        </div>
    </div>
    <div class="controllers">
        <div>
            <span>Cloud Monster has been woken up.</span>
        </div>
        <div>
            <input type="checkbox" id="highlight-results" style="vertical-align: middle">
            <label for="highlight-results">Highlight results</label>
        </div>
    </div>
    <div class="process-summary">
        <div class="ps-status">
            <span>Process Summary: </span>
            <div class="ps-item">
                <span class="total-process ps-badge ps-badge-total">0</span> Total
            </div>
            |
            <div class="ps-item">
                <span class="active-process ps-badge ps-badge-completed">0</span> Completed
            </div>
            |
            <div class="ps-item">
                <span class="processing-process ps-badge ps-badge-processing">0</span> Processing
            </div>
            |
            <div class="ps-item">
                <span class="waiting-process ps-badge ps-badge-waiting">0</span> Waiting
            </div>
            |
            <div class="ps-item">
                <span class="failed-process ps-badge ps-badge-failed">0</span> Failed
            </div>
        </div>
        <div class="ps-success-rate">
            Success rate(%) : <span class="success-rate">0</span>
        </div>
    </div>
    <div id="tracking-tbl-wrap">
        <table class="tracking-tbl" id="tracking-tbl">
            <thead>
            <tr>
                <th>PID</th>
                <th>Started</th>
                <th>File Id</th>
                <th>Dest.</th>
                <th>PTime</th>
                <th>Avg Speed</th>
                <th>Cur. Speed</th>
                <th>Progress (%)</th>
                <th>RTime</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<script src="<?php buildResourceURI('assets/cpanel/libs/jquery/dist/jquery.min.js'); ?>"></script>
<script src="https://www.jquery-az.com/jquery/js/contextMenu/contextMenu.min.js"></script>
<script>
    const ROOT = '<?php _e(siteurl()) ?>';

    $(document).ready(function(){

        let data = [
            [
                {
                    text: "Open Bucket",
                    action: function () {
                        if(Tracker.selectedRow !== null){

                            let currentId = Tracker.selectedRow.find('.fileId').text();
                            let url = ROOT + '/cpanel/buckets/view?file=' + currentId;
                            window.open(url, '_blank').focus();

                        }
                    }
                }
            ]
        ];


        $("#tracking-tbl").contextMenu(data);

        Tracker.init();

    });





    const Tracker = {

        node : null,
        tbl: null,
        lastId: 0,
        activeIds: [],
        offset: 0,
        selectedRow: null,
        processSummary: {

            total: 0,
            active: 0,
            processing: 0,
            waiting: 0,
            failed: 0

        },



        init: function(){

            let self = this;

            self.node = $('#app');
            self.tbl = self.node.find('#tracking-tbl');
            self.update();

            self.bind('#highlight-results', 'highlightResults', 'change');
            self.bind('#tracking-tbl tbody tr', 'rowSelected', 'contextmenu');




        },
        rowSelected: function (){
            this.selectedRow = this.activeNode;
        },
        loadLastId: function(){
            let firstRow = this.tbl.find('tr[data-tracking]').first();
            if(firstRow.length > 0){
                this.lastId = firstRow.attr('id');
            }
            return 0;
        },
        loadActiveIds: function (){

            let activeIds = [];
            let self = this;

            self.tbl.find('tr[data-tracking="1"]').each(function() {
                activeIds.push($(this).attr('id'));
            });

            self.activeIds = activeIds;
        },
        update: function (){


            let self = this;

            self.loadLastId();
            self.loadActiveIds();

            $.ajax({

                url: ROOT + '/cpanel/tracker/data',
                data: {
                    last: self.lastId,
                    active: JSON.stringify(self.activeIds),
                    withSummary: 1
                },
                type: 'GET',
                success: function(response) {

                    if(response.success){

                        let responseData = response.data;

                        if('data' in  responseData){
                            let data = responseData.data;

                            if(data.length !== undefined && data.length > 0){

                                self.offset += data.length;
                                self.append(data);

                            }
                        }

                        if('summary' in responseData){

                            let summaryData = responseData.summary;

                            self.processSummary.waiting = summaryData.waiting;
                            self.processSummary.active = summaryData.active;
                            self.processSummary.processing = summaryData.processing;
                            self.processSummary.failed = summaryData.failed;
                            self.processSummary.total = summaryData.total;

                            self.updateSummary();

                        }

                        setTimeout(function(){ self.update(  ) }, 1000);
                    }

                },
                complete: function() {

                }
            });

        },
        append: function (data){

            let self = this;

            data.forEach(function (item, index) {
                let row = self.tbl.find('tr[id="'+item.id+'"]');
                self.processSummary.total += 1;
                if(item.pstatus === 'active'){
                    item.progress = 100;
                }
                if(row.length === 0){



                    row = '<tr id="' + item.id + '" data-tracking="' + item.isTracking + '" data-status="' + item.pstatus +'">';
                    row += '<td class="id"> ' + item.id + ' </td>';
                    row += '<td> ' + item.createdAt + ' </td>';
                    row += '<td class="fileId"> ' + item.fileId + ' </td>';
                    row += '<td> ' + item.type + ' </td>';
                    row += '<td class="processTime"> ' + item.processTime + ' </td>';
                    row += '<td class="avgSpeed"> ' + item.avgSpeed + ' </td>';
                    row += '<td class="currentSpeed"> ' + item.currentSpeed + ' </td>';

                    let progressBar = '<div class="progress-bar" style="width: '+ item.progress +'%"> ' + item.progress + ' </div>';

                    row += '<td class="progress-wrap"> ' + progressBar + ' </td>';
                    row += '<td class="remainingTime"> ' + item.remainingTime + ' </td>';
                    row += '<td class="status"> ' + item.pstatus + ' </td>';

                    self.processSummary[item.pstatus] += 1;

                    self.tbl.prepend(row);

                }else{

                    //update
                    row.find('.processTime').text( item.processTime );
                    row.find('.avgSpeed').text( item.avgSpeed );
                    row.find('.currentSpeed').text( item.currentSpeed );
                    row.find('.progress-bar').text( item.progress );
                    row.find('.progress-bar').css('width', item.progress + '%');
                    row.find('.remainingTime').text(item.remainingTime);
                    row.find('.status').text(item.pstatus);

                    row.attr('data-status',item.pstatus);

                    self.processSummary[item.pstatus] += 1;

                    if(item.isTracking !== 1){
                        row.attr('data-tracking',item.remainingTime);
                    }


                }



            });




        },
        highlightResults: function (){

            this.tbl.toggleClass('highlight');

        },
        updateSummary: function (){

            let summary = this.node.find('.process-summary');
            let summaryInfo = this.processSummary;

            summary.find('.total-process').text(summaryInfo.total);
            summary.find('.active-process').text(summaryInfo.active);
            summary.find('.processing-process').text(summaryInfo.processing);
            summary.find('.failed-process').text(summaryInfo.failed);
            summary.find('.waiting-process').text(summaryInfo.waiting);

            let t = summaryInfo.active + summaryInfo.failed;
            if(t > 0){
                let successRate = Math.round(summaryInfo.active / t * 100);
                summary.find('.success-rate').text(successRate);
            }



        },
        bind: function(selector, action, event = 'click'){
            $(document).on( event, selector, function(self){
                return function (e){
                    self.activeNode = $(this);
                    return self[action](e);
                }
            }(this));
        }

    }


</script>
</body>
</html>