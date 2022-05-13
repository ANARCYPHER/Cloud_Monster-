
"use strict";

const App = {

    name: 'Cloud Monster PHP Application',
    version: '1.0',
    author: 'John Antonio',
    isInit: false,
    page: null,

    init: function(){

        if(!this.isInit){
            this.isInit = true;
            this.router.parent = this;
            this.router.route();
            this.sayHello();
        }




    },
    router: {
        url: null,
        action: null,
        parent: null,
        route: function(){

            let self = this;

            self.url = window.location.href;
            self.parent.services.search.init();

            let path = self.url.replace(ROOT + '/cpanel/', '');
            path = path.split('?')[0];
            path = path.split('#')[0];
            let pathList = path.split("/");

            if(pathList.length > 0){
                self.action = pathList.shift();
                if(method_exists(self, self.action)){

                    return self[self.action].apply(self, pathList);
                }
            }

        },
        files: function (){
            this.parent.services.files.init();
        },
        analytics: function (){
            this.parent.services.analytics.init();
        },
        dashboard: function (){
            this.parent.services.analytics.init();
            this.parent.services.storage.init();
        },
        process: function (){
            this.parent.services.process.init();
        },
        buckets: function(action = ''){

            switch(action){

                case 'list':

                    let bucketList = this.parent.services.bucketList.init();
                    this.parent.services.folderList.init([bucketList, 'move'], 'Move To', false);

                    break;

                case 'new':

                    Dropzone.autoDiscover = false;

                    let upload = this.parent.services.upload.init();
                    this.parent.services.folderList.init([upload, 'setSelectedFolder'], 'Upload To');

                    break;

                case 'view':

                    let bucketNode = $('#bucket');

                    if(bucketNode.length > 0){
                        let tracker = this.parent.services.tracker.init();
                        tracker.run();

                        let reUploader = this.parent.services.upload;
                        reUploader.isReUpload = true;
                        reUploader.init()

                        this.parent.services.bucketView.init();
                    }



                    break;

            }

        },
        drives: function (action = ''){

            switch(action){

                case 'new':
                case 'edit':

                    this.parent.services.drives.drive.init();

                    break;
                case 'list':
                    this.parent.services.drives.list.init();
                    break;

            }

        }
    },
    services: {
        files: {

            init: function (){
                $('#files-list').DataTable();
                this.bind('#select-drives-list', 'driveSelected', 'change');
            },
            driveSelected: function(){
                let source = this.activeNode.val();
                insertParam('drive', source);

            },
            bind: function(selector, action, event = 'click') {
                $(document).on(event, selector, function (self) {
                    return function (e) {
                        self.activeNode = $(this);
                        return self[action](e);
                    }
                }(this));
            }
        },
        storage: {

            activeNode: null,

            init: function (){

                this.bind('.clear', 'clear');

            },

            clear: function (){

                let self = this;

                let dataType = self.activeNode.attr('data-type');

                $.ajax({

                    url: ROOT + '/cpanel/storage/clear/' + dataType,
                    type: 'GET',
                    contentType: "application/json",
                    dataType: "json",
                    success: function(response) {

                        if(response.success){

                            self.activeNode.text('cleared');

                        }else{

                            alert('Something went wrong');

                        }

                    },
                    error: function (jqXHR, exception){
                        alert('error occurred');
                    },
                    complete: function() {



                    }
                });

            },
            bind: function(selector, action, event = 'click') {
                $(document).on(event, selector, function (self) {
                    return function (e) {
                        self.activeNode = $(this);
                        return self[action](e);
                    }
                }(this));
            }
        },
        process: {

            activeNode: null,

            init: function (){

                this.update();

            },
            update: function (){

                let url = ROOT + '/cpanel/process/data';
                let self = this;

                $.getJSON(url, function(response) {
                    self.appendData(response);
                    setTimeout(function (){
                        self.update();
                    }, 1000)
                });

            },
            appendData: function (data){

                if('threads' in data){

                    let threadData = data.threads;
                    let threadTbl = $('#threads-tbl');
                    let activeThreads = this.getActiveThreads();
                    let responseIds = new Array();

                    $('.active-threads').text(threadData.active);
                    $('.total-thread-memory-usage').text(threadData.memoryUsage);

                    Object.keys(threadData.data).forEach(function(key) {
                        let val = threadData.data[key];

                        responseIds.push(val.pid);

                        let rowHtml =
                            '<td >#'+val.pid+'</td>' +
                            '<td className="text-muted">'+val.memoryUsage+'</td>' +
                            '<td className="text-muted">'+val.creationDate+'</td>' +
                            '<td className="text-muted">'+val.runTime+'</td>';

                        if(activeThreads.includes(val.pid)){
                            let elmtId = "#" + val.pid;
                            threadTbl.find(elmtId).html(rowHtml);
                        }else{
                            threadTbl.append( '<tr id="'+val.pid+'"> ' +  rowHtml + '</tr>');
                        }




                    });

                    let threadsDiff = activeThreads.filter(function(obj) { return responseIds.indexOf(obj) == -1; });
                    if(threadsDiff.length > 0){
                        threadsDiff.forEach(function(item, index){
                            let elmtId = "#" + item;
                            $(elmtId).remove();
                        });

                    }

                }

                if('upload' in data){

                    $('.total-upload-process').text(data.upload.processing);

                }


            },
            getActiveThreads: function (){
                var ids = new Array();
                $('#threads-tbl tbody tr').each(function() { //Get elements that have an id=
                    ids.push($(this).attr("id")); //add id to array
                });
                return ids;
            },
            bind: function(selector, action, event = 'click'){
                $(document).on(event,selector,function(self) {
                    return function (e){
                        self.activeNode = $(this);
                        return self[action](e);
                    }
                }(this));
            }
        },
        analytics: {

            activeNode: null,
            fileId: 0,

            init: function (){

                let self = this;

                self.visitsByMonthly.init();
                self.liveVisitors.init();
                self.visitorsMap.init();

            },

            visitorsMap: {

                name: 'visitorsByCountry',
                activeNode: null,
                dateRangePicker: null,
                startDate: '',
                endDate: '',
                isUnique: false,
                map: null,
                data: {},
                fileId: 0,
                init: function (){

                    let self = this;

                    let visitorsMap = $('#visitors-map');

                    if(visitorsMap.length === 1){
                        self.map = visitorsMap;
                        self.loadFileId();
                        self.dateRangePicker = $('#visitsByCountry');
                        self.bindDataRangePicker();

                        self.bind('#visitors-map', 'showLabel', 'labelShow.jqvmap')
                        self.bind('#mapVisitsType', 'visitsTypeSelected', 'change')

                        self.updateMap();

                    }


                },
                showLabel: function (event, label, code){
                    if (this.data[code] > 0) {
                        label.append(': <strong>' + this.data[code] + '</strong>');
                    }
                },
                visitsTypeSelected: function (){

                    let val = this.activeNode.val();
                    this.isUnique = val === 'unique';

                    this.updateMap();


                },
                updateMap: function (){

                    let self = this;
                    let mapRegionNode = $('.jqvmap-region');

                    $.ajax({

                        url: ROOT + '/cpanel/analytics/json',
                        type: 'GET',
                        contentType: "application/json",
                        dataType: "json",
                        data: {
                            chart: self.name,
                            d1: self.startDate,
                            d2: self.endDate,
                            unique: self.isUnique,
                            file: self.fileId
                        },
                        beforeSend: function(){
                            self.load();
                        },
                        success: function(response) {

                            setTimeout(function (){
                                self.data = response;
                                mapRegionNode.attr('fill', '#78828c1a');
                                mapRegionNode.attr('original', '#78828c1a');
                                self.map.vectorMap('set', 'values', self.data );
                            }, 700)

                        },
                        error: function (jqXHR, exception){
                            alert('Map data loading failed');
                        },
                        complete: function() {

                            setTimeout(function (){
                                self.load();
                            }, 700)


                        }
                    });

                },
                loadFileId: function (){

                    let selectedFile = $('#selected-file');
                    if(selectedFile.length === 1){
                        this.fileId = selectedFile.attr('data-id');
                    }

                },
                bindDataRangePicker: function (){

                    let self = this;

                    self.dateRangePicker.daterangepicker({
                        showDropdowns: true,
                        opens : "left",
                        startDate : "1/12/2021",
                        minDate: '1/1/2021',
                        maxDate: moment().format('D/M/YYYY'),
                        autoUpdateInput: true,
                        locale: {
                            format: 'D/M/YYYY'
                        },
                        ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                        },
                        cancelClass: "btn-secondary"
                    });

                    self.dateRangePicker.on('apply.daterangepicker', function(e, picker) {
                        self.filtered(picker);
                    });


                },
                filtered: function (picker){

                    this.startDate = picker.startDate.format('DD-MM-YYYY');
                    this.endDate = picker.endDate.format('DD-MM-YYYY');

                    this.updateMap();


                },
                load: function (){
                    let cardElement = this.map.closest('.card');
                    if(cardElement.length > 0){
                        cardElement.toggleClass('card-loading');
                    }
                },
                bind: function(selector, action, event = 'click'){
                    $(document).on(event,selector,function(self) {
                        return function (e){
                            self.activeNode = $(this);
                            return self[action].apply(self, arguments);
                        }
                    }(this));
                }
            },
            liveVisitors: {

                name: 'liveVisitors',
                activeNode: null,
                chart: null,
                data: [],

                init: function (){

                    let self = this;

                    if(typeof VisitorsLive_BarChart != 'undefined'){
                        self.chart = VisitorsLive_BarChart;
                        self.updateChart();
                    }

                },

                updateChart: function (){

                    let self = this;


                    $.ajax({

                        url: ROOT + '/cpanel/analytics/json',
                        type: 'GET',
                        contentType: "application/json",
                        dataType: "json",
                        data: {
                            chart: self.name
                        },
                        success: function(response) {



                            let visits = response.visits;

                            let time = new Date();



                            self.data.push({
                                "x": time,
                                "y": visits
                            });


                            if(self.data.length > 15){
                                self.data.shift();
                            }

                            self.chart.updateSeries([{
                                name: 'visits',
                                data:  self.data
                            }]);

                            $('.num-of-live-visits').text(visits);

                            setTimeout(function (){
                                self.updateChart();
                            }, 1000)



                        },
                        error: function (jqXHR, exception){
                            alert('Live Visits chart data loading failed');
                        },
                        complete: function() {

                            setTimeout(function (){
                                self.noData();
                            }, 900)


                        }
                    });




                },
                loading: function (){
                    this.chart.updateSeries([]);
                    this.chart.updateOptions({
                        noData: {
                            text: 'loading...'
                        }
                    })
                },
                noData: function (){
                    this.chart.updateOptions({
                        noData: {
                            text: 'no data'
                        }
                    })
                },
                bind: function(selector, action, event = 'click'){
                    $(document).on(event,selector,function(self) {
                        return function (e){
                            self.activeNode = $(this);
                            return self[action](e);
                        }
                    }(this));
                }

            },
            visitsByMonthly: {

                name: 'visitorsByMonthly',
                activeNode: null,
                dateRangePicker: null,
                month: 0,
                year: 0,
                chart: null,
                fileId: 0,

                init: function (){

                    let self = this;

                    if(typeof VisitorsByMonthly_BarChart != 'undefined'){
                        self.chart = VisitorsByMonthly_BarChart;
                        self.dateRangePicker = $('#visitsMonthlyDatePicker');
                        self.bindDataRangePicker();
                        self.loadFileId();
                        self.updateChart();

                    }

                },
                loadFileId: function (){

                    let selectedFile = $('#selected-file');
                    if(selectedFile.length === 1){
                        this.fileId = selectedFile.attr('data-id');
                    }

                },
                bindDataRangePicker: function (){

                    let self = this;

                    self.dateRangePicker.daterangepicker({
                        singleDatePicker: true,
                        showDropdowns: true,
                        minDate: '1/2021',
                        maxDate: moment().format('M/YYYY'),
                        autoUpdateInput: true,
                        locale: {
                            format: 'M/YYYY'
                        },
                        cancelClass: "btn-secondary"
                    });

                    self.dateRangePicker.on('show.daterangepicker', function(e, picker) {
                        picker.container.find('.calendar-table').addClass('my-only');
                    });
                    self.dateRangePicker.on('hide.daterangepicker', function(e, picker) {
                        picker.container.find('.calendar-table').removeClass('my-only');
                    });
                    self.dateRangePicker.on('apply.daterangepicker', function(e, picker) {
                        self.filtered(picker);
                    });

                },
                updateChart: function (){

                    let self = this;


                    $.ajax({

                        url: ROOT + '/cpanel/analytics/json',
                        type: 'GET',
                        contentType: "application/json",
                        dataType: "json",
                        data: {
                            chart: self.name,
                            month: self.month,
                            year: self.year,
                            file: self.fileId
                        },
                        success: function(response) {

                            let data = response;
                            let visits = [];
                            let uniqVisits = [];

                            if(!$.isEmptyObject(data)){
                                Object.keys(data).forEach(function(key) {
                                    let val = data[key];
                                    visits.push({
                                        "x": val.date,
                                        "y": val.visits
                                    });
                                    uniqVisits.push({
                                        "x": val.date,
                                        "y": val.uniqVisits
                                    });
                                });

                            }

                            setTimeout(function (){
                                self.chart.updateSeries([{
                                    name: 'total visits',
                                    data: visits
                                },{
                                    name: 'unique visits',
                                    data: uniqVisits
                                }]);
                            }, 900)

                        },
                        error: function (jqXHR, exception){
                            alert('Visits chart data loading failed');
                        },
                        complete: function() {

                            setTimeout(function (){
                                self.noData();
                            }, 900)


                        }
                    });




                },
                loading: function (){
                    this.chart.updateSeries([]);
                    this.chart.updateOptions({
                        noData: {
                            text: 'loading...'
                        }
                    })
                },
                noData: function (){
                    this.chart.updateOptions({
                        noData: {
                            text: 'no data'
                        }
                    })
                },
                filtered: function (picker){

                    let self = this;

                    self.month = parseInt(picker.container.find('.monthselect').val()) + 1;
                    self.year = parseInt(picker.container.find('.yearselect').val());

                    picker.setStartDate(self.month + '/' + self.year);

                    //update chart
                    self.loading();
                    this.updateChart();

                },
                bind: function(selector, action, event = 'click'){
                    $(document).on(event,selector,function(self) {
                        return function (e){
                            self.activeNode = $(this);
                            return self[action](e);
                        }
                    }(this));
                }

            },
            bind: function(selector, action, event = 'click'){
                $(document).on(event,selector,function(self) {
                    return function (e){
                        self.activeNode = $(this);
                        return self[action](e);
                    }
                }(this));
            }

        },
        drives: {

            activeNode: null,

            list: {

                activeNode: null,
                activeModal: null,
                item: {
                    id: 0,
                    name: ''
                },
                init: function(){
                    let self = this;

                    self.bind('.view-more-info', 'viewMoreInfo');
                    self.bind('.delete-drive', 'delete');


                    $('#cloud-drive-list-tbl').DataTable();

                },
                viewMoreInfo: function(){

                    let self = this;

                    self.loadModal('drive-more-info-modal');

                    let loader = self.activeModal.find('.cm-loader');
                    let dataNode = self.activeModal.find('.data-json');
                    let alertWrap = 'drive-more-info-alert-wrap';
                    cleanAlerts(alertWrap);

                    $.ajax({

                        url: ROOT + '/cpanel/drives/more-info/' +  self.item.id,
                        type: 'POST',
                        data: {},
                        beforeSend: function(){
                            loader.show();
                            dataNode.hide();
                        },
                        success: function(response) {

                            if(response.success){

                                let data = response.data;

                                if('accInfo' in data){

                                    let accInfo = data.accInfo;

                                    if(!$.isEmptyObject(accInfo)){

                                        let jsonData =  JSON.stringify(accInfo,null, 2);
                                        dataNode.text(jsonData);
                                        dataNode.show();

                                    }else{

                                        addAlert('Data not found', 'warning',alertWrap);

                                    }



                                }




                            }



                        },
                        error: function (jqXHR, exception){

                        },
                        complete: function() {

                            loader.hide();

                        }
                    });


                    self.modal('show');



                },
                delete: function(){

                    let self = this;
                    self.loadModal('del-drive-modal');

                    let button = new Button(self.activeNode);

                    if(self.activeNode.hasAttr('data-confirm')){

                        self.modal('show');

                    }else{

                        button.loading();
                        redirect('/drives/delete/'+self.item.id);

                    }




                },
                loadItem: function(node){

                    //active node
                    this.activeNode = node;
                    let parentNode = node.closest('tr');

                    //item parent node
                    if(parentNode.length > 0){
                        this.item.id = parentNode.attr('id');
                        this.item.name = parentNode.find('.name').text();
                    }


                },
                loadModal: function(id){
                    let nodeId = '#' + id;
                    this.activeModal = $(nodeId);
                },
                modal: function(t){
                    this.activeModal.modal(t);
                },
                bind: function(selector, action, event = 'click'){
                    $(document).on(event,selector,function(self) {
                        return function (e){
                            self.loadItem($(this));
                            return self[action](e);
                        }
                    }(this));
                }
            },

            drive:{
                activeNode: null,
                isEdit: false,
                source: null,
                id: 0,

                init: function (){

                    let self = this;

                    self.bind('#select-drives-list', 'driveSelected', 'change');
                    // self.bind('#submit-drive', 'submit');

                    let formNode = $('#new-drive-form');
                    self.isEdit = formNode.attr('data-is-edit');
                    self.id = formNode.attr('data-id');

                    self.check();

                },
                check: function (){
                    if(this.isEdit){
                        if(!this.isActive()){
                            addAlert('Cloud drive authentication failed', 'danger');
                        }
                    }
                },
                isActive: function(){

                    let self  = this;
                    let success = false;

                    self.loadFormData();



                    $.ajax({

                        url: ROOT + '/cpanel/drives/check/' + self.id,
                        type: 'GET',
                        async: false,
                        success: function(response) {
                            if(response.success){
                                success = true;
                            }
                        }
                    });

                    return success;

                },
                driveSelected: function(){
                    let source = this.activeNode.val();
                    if(!isEmpty(source)){
                        redirect('/drives/new?source=' + source);
                    }
                },
                bind: function(selector, action, event = 'click'){
                    $(document).on(event,selector,function(self) {
                        return function (e){
                            self.activeNode = $(this);
                            return self[action](e);
                        }
                    }(this));

                }
            },



        },
        folderList: {

            node: null,
            activeNode: null,
            breadcrumb: null,
            callback: null,
            alertWrap: 'select-folder-alert-wrap',
            list: {
                node: null,
                init: function(parentNode){
                    this.node = parentNode.find('.folder-list');
                },
                add: function(item){
                    this.node.append(item);
                },
                clean: function(){

                    this.node.html('');

                },
                loading: function(){

                },
                loaded: function(){

                }

            },
            item: {
                id: 0,
                name: '',
                node: null,
                default: null
            },
            autoClose: true,
            location: '',
            init: function(callback = null, title = '', autoClose = true){

                this.node = $('#select-folder-modal');
                if(title !== ''){
                    this.node.find('.modal-title').text(title);
                }


                let self = this;

                //bind active actions
                self.bind('.open-folder', 'open');
                self.bind('#select-folder', 'selected');
                self.callback = callback;
                self.autoClose = autoClose;

                this.list.init(this.node);

                this.setBreadcrumb();
                this.setDefaultItem();
                this.load();

            },
            open: function(){

                this.list.node.removeClass('active');
                this.load();

            },
            close: function(){
                this.node.modal('hide');
            },
            selected: function(){

                if(this.callback !== null){



                    let callback = this.callback;

                    if(typeof callback === 'string') {
                        window[this.callback](this.item.id, this.item.name, this.location, this);
                    }else{
                        if(callback.length === 2){
                            this.callback[0][this.callback[1]](this.item.id, this.item.name, this.location, this);
                        }
                    }

                    if(this.autoClose){
                        this.close();
                    }



                }

            },
            load: function(){

                let self = this;


                $.ajax({

                    url: ROOT + '/cpanel/folders/list/' +  self.item.id,
                    type: 'POST',
                    data: {},
                    beforeSend: function(){

                    },
                    success: function(response) {

                        if(response.success){

                            self.appendData(response.data);

                        }



                    },
                    error: function (jqXHR, exception){

                    },
                    complete: function() {



                    }
                });

            },
            appendData: function(data){

                let self = this;
                this.location = '';

                if('folders' in data){

                    let folders = data.folders;

                    if(folders.length > 0){
                        this.list.clean();

                        folders.forEach(function (item, index) {

                            let itemNode = self.item.default.clone();
                            itemNode.attr('id', item.id);
                            itemNode.find('.folder-name').text(item.name);

                            self.list.add(itemNode);

                        });

                    }else{

                        if(this.item.node !== null){
                            this.list.node.find('.open-folder').removeClass('active');
                            this.item.node.addClass('active');
                        }


                    }


                }


                if('parentList' in data){

                    let parentList = data.parentList;
                    let breadcrumb = self.breadcrumb.clone();



                    if(parentList.length > 0){

                        parentList.forEach(function (item, index) {
                            let html = '<li class="breadcrumb-item open-folder" id="'+ item.id +'" ><a href="javascript:void(0)">'+ item.name +'</a></li>';
                            self.location += '/' + item.name ;
                            breadcrumb.append(html);

                        });


                    }

                    self.node.find('#breadcrumb').html(breadcrumb);
                    self.location = '/home' + self.location;

                }




            },
            reset: function(){
                this.item.id = 0;
                this.item.name = '';
            },
            setDefaultItem: function(){

                let html = '<div id="" class="list-item open-folder px-3" >' +
                    '<svg class="icon tb-icon text-warning align-middle px-0" width="1em" height="1em" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">\n' +
                    '<path fill-rule="evenodd" d="M11.828 5h3.982a2 2 0 011.992 2.181l-.637 7A2 2 0 0115.174 16H4.826a2 2 0 01-1.991-1.819l-.637-7a1.99 1.99 0 01.342-1.31L2.5 5a2 2 0 012-2h3.672a2 2 0 011.414.586l.828.828A2 2 0 0011.828 5zm-8.322.12C3.72 5.042 3.95 5 4.19 5h5.396l-.707-.707A1 1 0 008.172 4H4.5a1 1 0 00-1 .981l.006.139z" clip-rule="evenodd"></path>\n' +
                    '</svg>' +
                    '<span class="folder-name"></span>' +
                    '</div>';

                this.item.default = $($.parseHTML(html));
            },
            setBreadcrumb: function(){
                let html = '<ol class="breadcrumb breadcrumb-arrows px-3 py-2"  aria-label="breadcrumbs">' +
                    '<li class="breadcrumb-item open-folder" id="1"><a href="javascript:void(0)">Home</a></li>' +
                    '</ol>';
                this.breadcrumb = $($.parseHTML(html));
            },
            loadItem: function(node){

                this.activeNode = node;
                if(node.hasClass('open-folder')){
                    this.item.id = node.attr('id');
                    this.item.name = node.find('.folder-name').text();
                    this.item.node = node;
                }


            },
            bind: function(selector, action){
                $(document).on("click",selector,function(self) {
                    return function (e){
                        self.loadItem($(this));
                        return self[action](e);
                    }
                }(this));

            }

        },
        dropzone: function(){
            return new Dropzone("#uploadzone", {

                url: ROOT + "/cpanel/upload/chunk" ,
                autoProcessQueue: false,
                uploadMultiple: false,
                parallelUploads: 1,
                maxFilesize: 3000,
                chunking: true,
                forceChunking: true,
                parallelChunkUploads: true,
                retryChunks: true,
                retryChunksLimit: 3,
                maxFiles: 1,
                Upload: 1,


                addedfile: function (file){

                    let parent = this.parent;

                    cleanAlerts(this.parent.alertWrap);
                    this.parent.setFileInfo(file);

                    if(!parent.isReUpload){
                        parent.setBucketName();
                    }else{
                        parent.button.disable(false);
                    }


                },
                chunksUploaded: function (file, done) {

                    let self = this;
                    let parent = App.services.upload;

                    parent.waitForServerResponse();

                    $.ajax({
                        url: ROOT + "/cpanel/upload/concat",
                        type: "POST",
                        data: {
                            dzuuid: file.upload.uuid,
                            dztotalchunkcount: file.upload.totalChunkCount
                        },
                        success: function (data) {
                            if(data.success){
                                if(!parent.isReUpload){
                                    parent.createBucket();
                                }else{
                                    parent.updateBucket();
                                }
                            }else{
                                displayAlerts(data.alerts, parent.alertWrap);
                                parent.canceled();
                            }
                            done();
                        },
                        error: function (msg) {
                            file.accepted = false;
                        }
                    });

                },
                processing: function(file) {

                    let parent = this.parent;
                    let self = this;

                    parent.loadModal('upload-progress-modal');

                    if(parent.isReUpload){
                        parent.node.modal('hide');
                    }

                    self.progress.timeStarted  =  new Date();
                    parent.isProcessing = true;

                    parent.activeModal.find('.nb-file-size').text( formatBytes(file.size) );
                    parent.activeModal.find('.close').remove();
                    parent.modal('show');

                },
                uploadprogress: function(file, progress, bytesSent) {



                    let self = this;

                    if(isNaN(bytesSent)){
                        bytesSent = Math.round(file.size * Math.round(progress) / 100);
                    }

                    let timeElapsed = (new Date()) - self.progress.timeStarted;
                    let uploadSpeed = bytesSent / (timeElapsed/1000);
                    let timeRemaining = (file.size - bytesSent) / uploadSpeed;

                    self.progress.progress = progress;
                    self.progress.bytesSent = bytesSent;
                    self.progress.timeRemaining = timeRemaining;
                    self.progress.uploadSpeed = uploadSpeed;

                    self.parent.updateProgress();

                },
                error	: function(file, response ){
                    let self = this;
                    if(this.parent.isProcessing){
                        // This calls server-side code to delete temporary files created if the file failed to upload
                        $.ajax({
                            url: ROOT + "/cpanel/upload/del?dzuuid=" + file.upload.uuid ,
                            type: "DELETE",
                            success: function (data) {
                                // nothing
                            }
                        });


                        self.parent.canceled();
                        self.parent.error(response);

                    }else{

                        this.parent.error(response);

                    }
                },
                complete: function(){
                    this.parent.isProcessing = false;
                }

            });
        },
        upload: {
            node: null,
            isReUpload: false,
            reUpload: {
                status: false,
                type: 'auto',
                dest: 'new-drive',

                toNewDrive: function(){
                    this.dest = 'new-drive';
                },
                toExistFile: function(){
                    this.dest = 'exist-file'
                },
                isAuto: function(){
                    return this.type === 'auto';
                }
            },
            isProcessing: false,
            bucket: {
                id: 0,
                name: ''
            },
            folder: {
                id: 0
            },
            file: null,
            driveIds: [0],
            dropzone: null,
            alertWrap: '',
            button: null,
            activeNode: null,
            activeModal: null,
            isRemoteUpload: false,
            remoteUpload: {
                file: '',
                isValid: false,
                checkFile: function (){

                    let self = this;
                    let success = false;

                    $.ajax({
                        url: ROOT + "/cpanel/upload/check-file",
                        type: "GET",
                        data: {
                            link: self.file
                        },
                        async: false,
                        success: function ( response ) {
                            if( response.success ){
                                success = true;
                            }
                        }
                    });

                    return success;

                },
                run: function (parent){

                    let self = this;

                    parent.node.modal('hide');
                    parent.loadModal('upload-progress-modal');
                    parent.waitForServerResponse();
                    parent.modal('show');

                    //step 01: check file
                    if(self.checkFile()){
                        parent.createBucket();
                    }else{
                       console.log('invalid file url');
                    }

                },
                hasInit: function (){
                    this.file = $("#remote-link").val();
                    this.isValid = !!isValidURL(this.file);
                    return !isEmpty(this.file);
                }
            },

            init: function(dropzone){

                let self = this;
                let bucketNode = $('#bucket');

                self.dropzone = App.services.dropzone();
                self.dropzone.parent = self;
                self.dropzone.progress = {
                    timeStarted: 0,
                    progress: 0,
                    bytesSent: 0,
                    timeRemaining: 0,
                    uploadSpeed: 0
                }


                if(self.isReUpload) {
                    self.alertWrap = 're-upload-alert-wrap';
                }

                self.node = self.isReUpload ? $('#re-upload-modal') : $('#main-upload-wrap');

                self.bind('#upload', 'upload');
                self.bind('.drive-select', 'driveSelected', 'change');
                self.bind('.select-re-upload-type', 'reUploadTypeSelected', 'change');
                self.bind('.select-re-upload-dest-type', 'reUploadDestTypeSelected', 'change');
                self.bind('.select-all-drives', 'drivesSelected');
                self.bind('.cancel-upload', 'cancelUpload');

                if(self.isReUpload){
                    self.bucket.id = bucketNode.attr('data-id');
                }

                self.button = new Button($('#upload'));

                self.reUpload.status = self.isReUpload;

                $(window).bind('beforeunload', function(){
                    if(self.isProcessing){
                        return 'Are you sure you want to leave?';
                    }
                });

                self.loadActiveFolder();

                return self;

            },
            cancelUpload: function (){

                this.dropzone.removeAllFiles(true);

            },
            drivesSelected: function (){

                let attr = this.activeNode.attr('data-action');

                if(attr === 'select-all'){

                    $('.c-drive-select').prop('checked', true);

                }else{
                    $('.c-drive-select').prop('checked', false);
                }

            },
            loadActiveFolder: function (){

                if(!this.isReUpload){
                    this.folder.id = this.node.attr('data-tmp-folder');
                }

            },
            upload: function(){

                let self = this;
                let e = '';
                let isUpload = false;
                let file = self.dropzone.getQueuedFiles();

                if(self.button.node.hasAttr('data-action')){

                    if(self.remoteUpload.hasInit()){
                        self.isRemoteUpload = true;
                    }

                    if(self.button.node.attr('data-action') !== 'next'){
                        self.setDriveIds();
                        self.setBucketName();
                    }

                    if(self.isReUpload){

                        if(self.button.node.attr('data-action') === 'next'){

                            $('.file-info-wrap').hide();
                            $('.drive-list-wrap').show();

                            self.button.node.text('Upload');
                            self.button.node.attr('data-action', 'upload');
                            self.button.disable();


                        }else{

                            isUpload = true;

                        }

                    }else{
                        isUpload = true;
                    }




                    if(isUpload){



                        if(self.validate()){

                            self.alertWrap = 'progress-alert-wrap';

                            if(!this.isReUpload || !this.reUpload.isAuto()){

                                if(self.isRemoteUpload){
                                    self.remoteUpload.run(self);
                                }else{
                                    self.dropzone.processFile(file[0]);
                                }



                            }else{

                                this.updateBucket();

                            }
                        }




                    }


                }

            },
            updateBucket: function(){

                let self = this;
                let data = {
                    bucketId: self.bucket.id,
                    drives: JSON.stringify(self.getDriveIds()),
                    type: self.reUpload.type,
                    dest: self.reUpload.dest
                }

                if(!this.reUpload.isAuto()){


                    data.dzuuid = self.file.upload.uuid;

                }else{

                    self.node.modal('hide');
                    self.loadModal('upload-progress-modal');
                    self.waitForServerResponse();
                    self.modal('show');

                }




                $.ajax({
                    url: ROOT + "/cpanel/buckets/update",
                    type: "POST",
                    data: data,
                    success: function (response) {

                        if(response.success){
                            self.success();
                        }else{
                            self.canceled();
                        }

                        displayAlerts(response.alerts, self.alertWrap);

                    },
                    error: function (msg) {

                    }
                });

            },
            createBucket: function(){

                let self = this;
                let data = {
                    drives: JSON.stringify(self.getDriveIds()),
                    bucketName: self.bucket.name,
                    folder: self.folder.id
                };

                if(! self.isRemoteUpload){
                    data.dzuuid = self.file.upload.uuid;
                }else{
                    data.link = self.remoteUpload.file;
                }

                if(self.isRemoteUpload || self.file !== null){

                    $.ajax({
                        url: ROOT + "/cpanel/buckets/new",
                        type: "POST",
                        data: data,
                        success: function (response) {
                            if(response.success){
                                self.bucket.id = response.data.id;
                                self.success();
                            }else{
                                self.canceled();
                            }
                            displayAlerts(response.alerts, self.alertWrap);
                        },
                        error: function (msg) {
                            self.file.accepted = false;
                        }
                    });

                }



            },
            validate: function(){

                let self = this;
                let file = self.dropzone.getQueuedFiles();
                let error = '';

                //check drive IDs
                if(self.driveIds.length === 0){
                    error = 'You must select least 1 cloud drive';
                }

                //check bucket id/name
                if(self.isReUpload){
                    if(isEmpty(self.bucket.id)){
                        error = 'Bucket ID not found';
                    }
                }else{
                    if(isEmpty(self.bucket.name)){
                        error = 'Bucket name is required';
                    }
                }

                //check file
                if(! self.isRemoteUpload){
                    if(!self.reUpload.isAuto() && file.length === 0){
                        error = 'File is required';
                    }
                }else{
                    if(! self.remoteUpload.isValid){
                        error =  'Remote upload url is Invalid';
                    }
                }


                if(!isEmpty(error)){
                    self.error(error);
                    return false;
                }

                return true;

            },
            driveSelected: function(){
                let item = this.activeNode;

                if(item.prop("checked")){
                    this.button.disable(false);
                }else{
                    this.button.disable();
                }

            },
            reUploadTypeSelected: function(){

                let item = this.activeNode;
                let autoUploadWrap = $('.re-upload-auto');
                let manuallyUploadWrap = $('.re-upload-manually');

                if(item.val() === 'auto'){

                    autoUploadWrap.show();
                    manuallyUploadWrap.hide();
                    this.reUpload.type = 'auto';
                    this.button.disable(false);

                }else{

                    manuallyUploadWrap.show();
                    autoUploadWrap.hide();
                    this.reUpload.type = 'manually';
                    this.button.disable();

                }

            },
            reUploadDestTypeSelected: function(){
                let item = this.activeNode;
                let toNewDrive = $('.to-new-drive');
                let toExitFile = $('.to-exit-file');
                let selectedDrives = $('input:checkbox[name=drives]');
                selectedDrives.prop('checked', false);
                this.button.disable();

                if(item.val() === 'new-drive'){

                    toNewDrive.show();
                    toExitFile.hide();
                    this.reUpload.toNewDrive();


                }else{

                    toExitFile.show();
                    toNewDrive.hide();
                    this.reUpload.toExistFile();

                }
            },
            updateProgress: function(){

                let self = this;
                let progressModal = self.activeModal;
                let progressInfo = self.dropzone.progress;

                if(progressInfo.timeRemaining < 0) progressInfo.timeRemaining = 0;
                progressInfo.progress =  Math.round(progressInfo.progress);

                progressModal.find( '.nb-progress' ).text( progressInfo.progress );
                progressModal.find( '.nb-time-remaining' ).text( timeFormat( progressInfo.timeRemaining ) );
                progressModal.find( '.nb-upload-speed' ).text( formatBytes( progressInfo.uploadSpeed , 0) + '/s' );
                progressModal.find( '.nb-uploaded' ).text( formatBytes( progressInfo.bytesSent ) );

                let progressBar = progressModal.find( '.progress-bar' );

                progressBar.css( 'width' , progressInfo.progress + '%' );
                progressBar.attr( 'aria-valuenow' , progressInfo.progress );

            },
            setFileInfo: function(file){

                let fileInfoNode = this.node.find('.file-info');

                this.file = file;

                if(fileInfoNode.length > 0){

                    fileInfoNode.find('.fname').text(file.name);
                    fileInfoNode.find('.fsize').text(formatBytes(file.size));
                    fileInfoNode.find('.ftype').text(file.type);

                    fileInfoNode.show();

                }

            },
            setBucketName: function(){

                let file = this.file;

                let bucketName = $('.bucket-name');



                if(this.bucket.name.length === 0){
                    if(file !== null){
                        this.bucket.name = file.name;
                        bucketName.val(file.name);
                    }
                }

                if(isEmpty(this.bucket.name)){
                    if(! isEmpty(bucketName.val())){
                        this.bucket.name = bucketName.val();
                    }else{
                        this.bucket.name = '';
                    }
                }


            },
            setDriveIds: function(){
                let self = this;
                let tmpData = [];
                let selector = '';
                if(self.isReUpload){
                    selector = "input:checkbox[name=drives][data-type=" + self.reUpload.dest + "]:checked";
                }else{
                    selector = "input:checkbox[name=drives]:checked";
                }

                $(selector).each(function(){
                    tmpData.push($(this).val());
                });
                self.driveIds = tmpData;
            },
            setFolderId: function(id){
                this.folder.id = id;
            },
            getDriveIds: function(){
                return this.driveIds;
            },
            error: function(e){

                addAlert(e, 'danger', this.alertWrap);

            },
            canceled: function(){

                let progressModal = this.activeModal;

                progressModal.find('.modal-title').text('Error Occurred.');
                progressModal.find( '.progress-wrap, .modal-footer, .waiting-resp-msg' ).remove();


                setTimeout(function(){
                    window.location.reload();
                }, 5000);

            },
            success: function(){

                let self = this;
                let progressModal = this.activeModal;

                progressModal.find('.modal-title').text('Success !');
                progressModal.find('.waiting-resp-msg').remove();

                setTimeout(function(){
                    if(self.isReUpload){
                        window.location.reload();
                    }else{
                        window.location.href  = ROOT + '/cpanel/buckets/view/' + self.bucket.id;
                    }

                }, 2000);

            },
            waitForServerResponse: function(){

                let progressModal = this.activeModal;

                progressModal.find('.modal-title').text('Please wait...');
                progressModal.find('.progress-wrap, .modal-footer').remove();
                progressModal.find('.waiting-resp-msg').css( 'display' , 'flex' );

            },
            setSelectedFolder: function(id, name, location){
                $('.upload-path').val(location);
                this.setFolderId(id);
            },
            loadModal: function(id){
                let nodeId = '#' + id;
                this.activeModal = $(nodeId);
            },
            modal: function(t){
                this.activeModal.modal(t);
            },
            bind: function(selector, action, event = 'click'){
                $(document).on(event,selector,function(self) {
                    return function (e){
                        self.activeNode = $(this);
                        return self[action](e);
                    }
                }(this));

            }
        },
        tracker: {

            node: null,
            bucketId: 0,
            lastId: 0,
            activeIds: [],
            isDone: false,
            init: function(){
                this.node =  $('#tracker');
                this.bucketId = $('#bucket').attr('data-id');
                return this;
            },
            run: function (){

                let self = this;
                self.loadActiveIds();
                self.loadLastId();

                $.ajax({

                    url: ROOT + '/cpanel/tracker/data',
                    data: {
                        bucket: self.bucketId,
                        last: self.lastId,
                        active: JSON.stringify(self.activeIds)
                    },
                    type: 'get',
                    success: function(response) {

                        if(response.success){
                            let responseData = response.data;
                            if('data' in  responseData){
                                let data = responseData.data;
                                if(data.length !== undefined && data.length > 0){
                                    self.update(data);
                                }
                            }
                            if(!self.isUploadProcessCompleted()){
                                setTimeout(function(){ self.run() }, 1000);
                            }
                        }

                    },
                    complete: function() {

                    }
                });
            },
            loadLastId: function(){

                let waitingRaw = this.node.find('tr[data-status="waiting"]');
                let prevRow = waitingRaw.prev();
                let firstRow = waitingRaw.first();
                let lastRow = this.node.find('tbody tr').last();

                this.lastId = 0;
                if(prevRow.length > 0){
                    this.lastId =  prevRow.attr('id');
                    if(firstRow.length > 0){
                        if(firstRow.attr('id') === waitingRaw.attr('id')){
                            this.lastId = parseInt(firstRow.attr('id')) - 1;
                        }
                    }
                }else{
                    if(waitingRaw.attr('data-status') === 'waiting'){
                        this.lastId = parseInt(waitingRaw.attr('id')) - 1;
                    }else{
                        this.lastId = parseInt(lastRow.attr('id')) - 1;
                    }

                }


            },
            loadActiveIds: function(){
                let activeIds = [];
                this.node.find('tr[data-status="process"]').each(function() {
                    activeIds.push($(this).attr('id'));
                })
                this.activeIds =  activeIds;
            },
            isUploadProcessCompleted: function(){
                let waiting = this.node.find('tr[data-status="waiting"]');
                let process = this.node.find('tr[data-status="process"]');
                return waiting.length === 0 && process.length === 0;
            },
            update: function(data){
                let self = this;
                data.forEach(function (item, index) {
                    let row = self.node.find('tr[id="'+item.fileId+'"]');
                    let progressWrap = row.find('.progress-wrap');
                    //update
                    progressWrap.find('.currentSpeed').text( item.currentSpeed );
                    progressWrap.find('.progress-bar-text').text( item.progress + ' %');
                    progressWrap.find('.progress-bar').attr('aria-valuenow',  item.progress);
                    progressWrap.find('.progress-bar').css('width', item.progress + '%');
                    progressWrap.find('.remainingTime').text(item.remainingTime);

                    row.find('.status-text').text(item.pstatus);
                    row.find('.status').addClass( item.pstatus );

                    row.attr('data-status',item.pstatus);

                    if(item.isTracking !== 1 && item.pstatus !== 'process'){
                        let msg = '';
                        progressWrap.remove();
                        if(item.pstatus === 'active'){
                            msg = '<span class="text-primary font-weight-bold"> ' + item.code + ' </span>';

                        }else {
                            msg = '<span class="text-danger"> ' + item.msg + ' </span>';;
                        }
                        row.find('.message').html(msg);
                    }

                });
            }

        },
        bucketList: {

            node: null,
            folderId: 0,
            activeModal: null,
            activeNode: null,
            activeDrive: 0,
            item: {
                id: 0,
                type: '',
                isFile: '',
                parent: null
            },

            init: function(){

                this.node = $('.bucket-list-wrap');
                this.currentFolderId = parseInt(this.node.attr('data-active-folder'));

                let self = this;

                //bind active actions
                self.bind('.rename-bucket', 'rename');
                self.bind('.delete-bucket', 'delete');
                self.bind('.move-bucket', 'move');
                self.bind('#create-folder', 'createFolder');
                self.bind('#select-drives-list', 'driveSelected', 'change');


                //init movement
                // this.movement.parent = this;
                // this.movement.init();

                let bucketListTbl = $('#bucket-list-tbl');

                if(bucketListTbl.length > 0){
                    bucketListTbl.DataTable({
                        order: []
                    });
                    let  breadcrumb = $('#buckets-breadcrumb').clone();
                    breadcrumb.show();
                    $('#bucket-list-tbl').before(breadcrumb);
                }else{
                    $('#buckets-breadcrumb').show();
                }

               self.activeDrive = $("#select-drives-list").val();

                return self;


            },
            driveSelected: function(){
                let source = this.activeNode.val();
                redirect('/buckets/list?drive=' + source);
            },

            move: function(id, name, location, folderListNode){

                let self = this;


                if(name !== undefined){

                    if(id === 0) id = 1;

                    let button = new Button(folderListNode.activeNode);

                    $.ajax({

                        url: ROOT + '/cpanel/folders/move',
                        type: 'POST',
                        data: {
                            from: self.item.id,
                            to: id,
                            type: self.item.type
                        },
                        beforeSend: function(){

                            button.loading();


                        },
                        success: function(response) {

                            if(response.success){


                                self.item.parent.remove();



                                setTimeout(function (){
                                    folderListNode.close();
                                }, 1500)

                            }

                            displayAlerts(response.alerts, folderListNode.alertWrap);

                        },
                        error: function (jqXHR, exception){

                        },
                        complete: function() {

                            button.loaded();
                        }
                    });

                }


            },
            createFolder: function(){

                let self = this;

                let errorWrap = 'new-folder-alert-wrap';
                let folderName = $('#new-folder-name').val();
                let button = new Button(this.activeNode);

                if(folderName.length > 0){

                    $.ajax({

                        url: ROOT + '/cpanel/folders/new',
                        type: 'POST',
                        data: {
                            name: folderName,
                            parent: self.currentFolderId
                        },
                        beforeSend: function(){
                            button.loading();
                        },
                        success: function(response) {

                            if(response.success){

                                let folderId = response.data.folderId;

                                setTimeout(function(){
                                    redirect('/buckets/list/' + folderId + '?drive=' + self.activeDrive);
                                }, 1000);

                            }

                            displayAlerts(response.alerts, errorWrap);

                        },
                        error: function (jqXHR, exception){

                        },
                        complete: function() {
                            button.loaded();
                        }
                    });
                }else{

                    addAlert('Folder name is required', 'danger', errorWrap);

                }

            },
            delete: function(){

                this.loadModal('del-bucket-modal');
                let item = this.item;
                let self = this;
                let alertWrap = 'folder-del-alert-wrap';
                let button = new Button(this.activeNode);

                if(!this.activeNode.hasAttr('data-confirm')){

                    //send ajax
                    if(item.id.length > 0){
                        $.ajax({

                            url: ROOT + '/cpanel/folders/delete',
                            type: 'POST',
                            data: {
                                id: item.id,
                                type: item.type
                            },
                            beforeSend: function(){
                                button.loading();
                            },
                            success: function(response) {

                                if(response.success){

                                    item.parent.remove();
                                    setTimeout(function(){
                                        self.modal('hide');
                                    }, 2000);

                                }

                                self.activeModal.find('.msg').hide();
                                displayAlerts(response.alerts, alertWrap);

                            },
                            error: function (jqXHR, exception){

                            },
                            complete: function() {
                                button.loaded();
                            }
                        });
                    }else{
                        addAlert('something went wrong', 'danger');
                    }
                }else{
                    self.activeModal.find('.msg').show();
                    cleanAlerts(alertWrap);
                    self.modal('show');
                }

            },
            rename: function(){

                this.loadModal('rename-bucket-modal');
                let activeNode = this.activeNode;
                let item = this.item;
                let originalNameNode = item.parent.find('.name-txt');
                let originalName = originalNameNode.text();
                let bucketName = $('#new-bucket-name');
                let fileExtNode = $('#file-ext');
                let alertWrap = 'rename-bucket-alert-wrap';


                if(activeNode.attr('data-rename') === 'get-data'){


                    bucketName.val(originalName);
                    fileExtNode.text(item.parent.find('.file-ext').text());

                    this.item.isFile ? fileExtNode.show() : fileExtNode.hide();


                    cleanAlerts(alertWrap);
                    this.modal('show');

                }else{

                    //send ajax
                    if(item.id.length > 0){

                        let self = this;
                        let name = bucketName.val();
                        let button = new Button(this.activeNode);

                        if(name.length > 0){

                            if(originalName !== name){

                                $.ajax({

                                    url: ROOT + '/cpanel/folders/rename',
                                    type: 'POST',
                                    data: {
                                        folder: item.id,
                                        name: name,
                                        type: item.type
                                    },
                                    beforeSend: function(){
                                        button.loading();
                                    },
                                    success: function(response) {

                                        if(response.success){

                                            originalNameNode.text(name);

                                            setTimeout(function(){
                                                self.modal('hide');
                                            }, 2000);

                                        }

                                        self.activeModal.find('.msg').hide();
                                        displayAlerts(response.alerts, alertWrap);

                                    },
                                    error: function (jqXHR, exception){

                                    },
                                    complete: function() {
                                        button.loaded();
                                    }
                                });

                            }else{

                                this.modal('hide');

                            }


                        }else{
                            addAlert('bucket name is required', 'danger');
                        }


                    }else{
                        addAlert('something went wrong', 'danger');
                    }

                }

            },
            loadItem: function(node){

                //active node
                this.activeNode = node;
                let parentNode = node.closest('tr');

                //item parent node
                if(parentNode.length > 0){
                    this.item.id = parentNode.attr('id');
                    this.item.type = parentNode.attr('data-type');
                    this.item.isFile = this.item.type === 'bucket';
                    this.item.parent = parentNode;
                }


            },
            loadModal: function(id){
                let nodeId = '#' + id;
                this.activeModal = $(nodeId);
            },
            modal: function(t){
                this.activeModal.modal(t);
            },
            bind: function(selector, action, event = 'click'){
                $(document).on(event,selector,function(self) {
                    return function (e){
                        self.loadItem($(this));
                        return self[action](e);
                    }
                }(this));
            }


        },
        bucketView: {
            node: null,
            bucket:{
                id: 0,
                name: '',
                link: '',
                ext: '',
                drives: [],
                shared: false,
                node: ''
            },
            files: {

                node: null,
                bucketId: 0,
                activeModal: null,
                activeNode: null,
                item:{
                    id: 0,
                    status: '',
                    node: null
                },

                init: function (){


                    let self = this;

                    self.bind('.delete-file', 'delete');

                    self.bucketId = $('#bucket').attr('data-id');


                },

                delete: function(){

                    let self = this;

                    this.loadModal('del-file-modal');

                    let alertWrap = 'file-del-alert-wrap';
                    let button = new Button(self.activeNode);

                    if(!self.activeNode.hasAttr('data-confirm')){


                        //send ajax
                        if(self.item.id.length > 0){
                            $.ajax({

                                url: ROOT + '/cpanel/files/delete',
                                type: 'POST',
                                data: {
                                    fileId: self.item.id,
                                    bucketId: self.bucketId
                                },
                                beforeSend: function(){
                                    button.loading();
                                },
                                success: function(response) {

                                    if(response.success){

                                        self.item.node.remove();

                                        setTimeout(function(){

                                            self.modal('hide');

                                        }, 1200);

                                    }

                                    self.activeModal.find('.msg').hide();
                                    displayAlerts(response.alerts, alertWrap);

                                },
                                error: function (jqXHR, exception){

                                },
                                complete: function() {
                                    button.loaded();
                                }
                            });
                        }else{
                            addAlert('something went wrong', 'danger', alertWrap);
                        }
                    }else{
                        self.activeModal.find('.msg').show();
                        cleanAlerts(alertWrap);
                        self.modal('show');
                    }

                },

                loadItem: function(node){


                    this.activeNode = node;
                    let parentNode = node.closest('tr');

                    //item parent node
                    if(parentNode.length > 0){
                        this.item.id = parentNode.attr('id');
                        this.item.status = parentNode.attr('data-status');
                        this.item.node = parentNode;
                    }



                },

                loadModal: function(id){
                    let nodeId = '#' + id;
                    this.activeModal = $(nodeId);
                },
                modal: function(t){
                    this.activeModal.modal(t);
                },
                bind: function(selector, action){
                    $(selector).on('click', function(self){
                        return function (e){
                            self.loadItem($(this));
                            return self[action](e);
                        }
                    }(this));
                }

            },
            share: {
                node: null,
                parent: null,
                linkNode: null,
                alertWrap: 'share-bucket-alert-wrap',
                init: function(parent){

                    let self = this;
                    self.parent = parent;

                    self.loadModal('share-bucket-modal');

                    self.bind('.share-bucket', 'display');
                    self.bind('.copy-bucket-shared-link', 'copyLink');
                    self.bind('.active-cloud-drive-list', 'driveSelected', 'change');

                    self.node = self.activeModal;
                    self.linkNode = self.node.find('input.bucket-link');


                },
                driveSelected: function(){

                    let selectedDrive = this.activeNode.val();

                    let newLink = this.parent.bucket.link + '/' + selectedDrive;

                    this.linkNode.val(newLink);


                },
                copyLink: function (){

                    let self = this;
                    let btn = self.activeNode;

                    btn.text('Copied');

                    copyToClipboard(self.linkNode.val());

                    setTimeout(function(){
                        btn.text('Copy');
                    }, 1500);

                },
                display: function(){

                    let self = this;
                    let node = self.node;
                    let driveList = node.find('.active-cloud-drive-list');
                    let sharedInfo = node.find('.bucket-shared-info');
                    let sharedStatusTxt = self.parent.bucket.shared ? 'shared' : 'unshared';
                    let sharedSwitchBtn = node.find('.share-bucket');

                    if(!self.activeNode.hasAttr('data-share-status')){

                        if(node.attr('data-loaded') === '0'){
                            self.linkNode.val(self.parent.bucket.link);
                            self.parent.bucket.drives.forEach(function (item, index) {
                                driveList.append('<option value="'+item+'">' + item + '</option>');
                            });
                            sharedSwitchBtn.attr('data-share-status', sharedStatusTxt);
                            node.attr('data-loaded', 1);
                        }

                        self.modal('show');

                    }else{

                       self.toggleShared();

                    }

                    if(!self.parent.bucket.shared){
                        sharedInfo.hide();
                        sharedSwitchBtn.text('Share');
                        addAlert('Bucket not shared. Only you have access', 'warning', self.alertWrap);
                    }else{
                        sharedInfo.show();
                        sharedSwitchBtn.text('Unshare');
                        addAlert('Anyone with this link can visit into files in bucket', 'info', self.alertWrap);
                    }

                },
                toggleShared: function(){

                    let self = this;

                    let button = new Button(self.activeNode);

                    $.ajax({

                        url: ROOT + '/cpanel/buckets/shared',
                        type: 'POST',
                        data: {
                            bucketId: self.parent.bucket.id,
                            st: Number(self.parent.bucket.shared),
                        },
                        async: false,
                        beforeSend: function(){
                            button.loading();
                        },
                        success: function(response) {

                            if(response.success){

                                self.parent.bucket.shared = !self.parent.bucket.shared;

                            }else{

                                addAlert('Something went wrong', 'danger', self.alertWrap);

                            }

                        },
                        error: function (jqXHR, exception){

                        },
                        complete: function() {
                            button.loaded();
                        }
                    });

                },
                loadModal: function(id){
                    let nodeId = '#' + id;
                    this.activeModal = $(nodeId);
                },
                modal: function(t){
                    this.activeModal.modal(t);
                },
                bind: function(selector, action, event = 'click'){
                    $(document).on( event, selector, function(self){
                        return function (e){
                            self.activeNode = $(this);
                            return self[action](e);
                        }
                    }(this));
                }
            },
            downloadProgress: false,
            init: function(){

                let self = this;

                self.files.init();
                self.share.init(self);

                self.bind('.rename-bucket', 'rename');
                self.bind('.delete-bucket', 'delete');


                this.node = $('#bucket');

                self.loadBucketInfo();
                self.checkDownloadProcess();


            },
            loadBucketInfo: function(){
                let itemNode = $('.bucket-name');

                let drives = [];
                let shared = this.node.attr('data-shared') === '1';

                $(".drive-files tbody tr").each(function(){
                    let type = $(this).attr('data-type');
                    if(!drives.includes(type)){
                        drives.push(type);
                    }
                });

                drives.sort();

                this.bucket.name = itemNode.find('.name-txt').text();
                this.bucket.ext = itemNode.find('.file-ext').text();
                this.bucket.id = this.files.bucketId;
                this.bucket.link = $("#bucket-link").attr('href');
                this.bucket.drives = drives;
                this.bucket.node = itemNode;
                this.bucket.shared = shared;


            },
            checkDownloadProcess: function (){

                let self = this;

                $.ajax({

                    url: ROOT + '/cpanel/download/get-progress',
                    data: {
                        bucket_id: self.bucket.id
                    },
                    type: 'get',
                    success: function(response) {

                        if(response.success){

                            let responseData = response.data;

                            if(! $.isEmptyObject(responseData)){
                                if(! responseData.isDone){
                                    self.downloadProgress = true;
                                    self.updateDownloadProgress(responseData);
                                }else{
                                    self.downloadProgress = false;
                                    self.updateDownloadProgress('', true);
                                }
                            }


                            if(self.downloadProgress){
                                setTimeout(function(){ self.checkDownloadProcess() }, 1000);
                            }

                        }else{
                            self.updateDownloadProgress('', true);
                        }

                    },
                    complete: function() {

                    }
                });

            },
            updateDownloadProgress: function (data, isDone = false){
                let progressWrap = $('.rm-progress-wrap');
                let mainWrap = $("#rm-file-download-progress");
                if(! isDone){
                    //update
                    mainWrap.show();
                    progressWrap.find('.currentSpeed').text( data.currentSpeed );
                    progressWrap.find('.progress-bar-text').text( data.progress + ' %');
                    progressWrap.find('.progress-bar').attr('aria-valuenow',  data.progress);
                    progressWrap.find('.progress-bar').css('width', data.progress + '%');
                    progressWrap.find('.remainingTime').text(data.remainingTime);
                }else{
                    mainWrap.remove();
                }
            },
            rename: function(){

                this.loadModal('rename-bucket-modal');

                let self = this;
                let activeNode = this.activeNode;

                let bucketName = $('#new-bucket-name');
                let fileExtNode = $('#file-ext');
                let alertWrap = 'rename-bucket-alert-wrap';


                if(activeNode.attr('data-rename') === 'get-data'){


                    bucketName.val(self.bucket.name);
                    fileExtNode.text(self.bucket.ext);

                    cleanAlerts(alertWrap);
                    this.modal('show');

                }else{

                    //send ajax
                    if(self.bucket.id.length > 0){

                        let name = bucketName.val();
                        let button = new Button(this.activeNode);


                        if(name.length > 0){

                            if(self.bucket.name !== name){

                                $.ajax({

                                    url: ROOT + '/cpanel/folders/rename',
                                    type: 'POST',
                                    data: {
                                        folder: self.bucket.id,
                                        name: name,
                                        type: 'bucket'
                                    },
                                    beforeSend: function(){
                                        button.loading();
                                    },
                                    success: function(response) {

                                        if(response.success){

                                            self.bucket.node.find('.name-txt').text(name);
                                            self.bucket.name = name;

                                            setTimeout(function(){
                                                self.modal('hide');
                                            }, 1200);

                                        }

                                        displayAlerts(response.alerts, alertWrap);

                                    },
                                    error: function (jqXHR, exception){

                                    },
                                    complete: function() {
                                        button.loaded();
                                    }
                                });

                            }else{

                                this.modal('hide');

                            }


                        }else{
                            addAlert('bucket name is required', 'danger');
                        }


                    }else{
                        addAlert('something went wrong', 'danger');
                    }

                }

            },
            delete: function(){

                this.loadModal('del-bucket-modal');
                let self = this;
                let alertWrap = 'folder-del-alert-wrap';
                let button = new Button(this.activeNode);

                if(!self.activeNode.hasAttr('data-confirm')){

                    //send ajax
                    if(self.bucket.id.length > 0){
                        $.ajax({

                            url: ROOT + '/cpanel/folders/delete',
                            type: 'POST',
                            data: {
                                id: self.bucket.id,
                                type: 'bucket'
                            },
                            beforeSend: function(){
                                button.loading();
                            },
                            success: function(response) {

                                if(response.success){

                                    setTimeout(function(){
                                        window.location.href  = ROOT + '/cpanel/buckets/list';
                                    }, 2000);

                                }

                                self.activeModal.find('.msg').hide();
                                displayAlerts(response.alerts, alertWrap);

                            },
                            error: function (jqXHR, exception){

                            },
                            complete: function() {
                                button.loaded();
                            }
                        });
                    }else{
                        addAlert('something went wrong', 'danger');
                    }
                }else{
                    self.activeModal.find('.msg').show();
                    cleanAlerts(alertWrap);
                    self.modal('show');
                }

            },
            loadModal: function(id){
                let nodeId = '#' + id;
                this.activeModal = $(nodeId);
            },
            modal: function(t){
                this.activeModal.modal(t);
            },
            bind: function(selector, action){
                $(selector).on('click', function(self){
                    return function (e){
                        self.activeNode = $(this);
                        return self[action](e);
                    }
                }(this));
            }

        },
        search: {
            activeNode: null,
            inputNode: null,
            resultsListNode: null,
            searchNode: null,
            init: function (){

                let self = this;

                self.bind('#main-search-input', 'loadResults', 'keyup');
                self.inputNode = $("#main-search-input");
                self.resultsListNode = $(".search-results-wrap .list-group");
                self.searchNode = $(".search-results-wrap");

                $(document).mouseup(function(e)
                {
                    if (!self.searchNode.is(e.target) && self.searchNode.has(e.target).length === 0)
                    {
                        if (!self.inputNode.is(e.target) && self.inputNode.has(e.target).length === 0)
                        {
                            self.reset();
                        }

                    }
                });


            },
            loadResults: function (){

                let self = this;

                let searchTerm = self.inputNode.val();

                if(searchTerm.length >= 3){


                    $.ajax({

                        url: ROOT + '/cpanel/search/',
                        type: 'GET',
                        contentType: "application/json",
                        dataType: "json",
                        data: {
                            term : searchTerm
                        },
                        async: false,
                        beforeSend: function(){
                            self.reset();
                        },
                        success: function(response) {

                            if(response.success){

                                if(!$.isEmptyObject(response.data)){
                                    self.appendData(response.data);
                                    self.showResults();
                                }else{
                                    self.noResults();
                                }

                            }

                        },
                        error: function (jqXHR, exception){
                            alert('error occurred');
                        },
                        complete: function() {


                        }
                    });

                }else{

                    self.reset();

                }


            },
            appendData: function (data){

                let self = this;

                Object.keys(data).forEach(function(key) {
                    let val = data[key];

                    let html = '<li class="list-group-item ">'+
                        '<a href="#" class="d-flex align-items-center text-white ">' +
                        '<span class="cut-text sname"></span>' +
                        '<span class="badge  ml-auto ftype"></span>' +
                        '</a>' +
                        '</li>';

                    let resultsItem = $(html);

                    let link = ROOT + '/cpanel/';
                    if(val.ftype === 'bucket') {
                        link += 'buckets/view/' + val.id;
                        resultsItem.find('.ftype').addClass('bg-primary');
                    }else{
                        link += 'buckets/view?file=' + val.id;
                        resultsItem.find('.ftype').addClass('bg-warning');
                    }

                    resultsItem.find('a').attr('href', link);
                    resultsItem.find('.sname').text(val.name);
                    resultsItem.find('.ftype').text(val.ftype);

                    self.resultsListNode.append(resultsItem);


                });

            },
            showResults: function (){
                this.searchNode.removeClass('no-results');
                this.searchNode.show();
            },
            noResults: function (){
                if(!this.searchNode.hasClass('no-results')){
                    this.searchNode.addClass('no-results');
                }
                this.searchNode.show();
            },
            reset: function (){
                this.resultsListNode.html('');
                this.searchNode.hide();
            },
            bind: function(selector, action, event = 'click'){
                $(document).on(event,selector,function(self) {
                    return function (e){
                        self.activeNode = $(this);
                        return self[action](e);
                    }
                }(this));
            }

        }
    },
    sayHello: function(){
        // console.clear();
        console.log(
            "%c!" + this.name,
            "color:#1b59a3;font-family:system-ui;font-size:4rem;-webkit-text-stroke: 1px black;font-weight:bold"
        );
    }



}

App.init();
// console.log(App);

