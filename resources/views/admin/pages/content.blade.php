@extends('admin.layouts.app')

@push('after-style')
<!-- JQuery DataTable Css -->
<link href="/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">
<style>
    .profile-table{
        width: 100%;
    }
</style>
@endpush
@push('before-script')
@endpush
@push('after-script')
<!-- Jquery DataTable Plugin Js -->
<script src="/plugins/jquery-datatable/jquery.dataTables.js"></script>
<script src="/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>
<script>
let Tabel = function(url){
    $('#FormTabel').html(createSkeleton(1));
    $.ajax( {
        url: url,
        dataType: "json",
        success:function(json) {
            $('#FormTabel').html("<table id='Tabel' class='table table-bordered table-striped table-hover'></table>");
            $('#Tabel').DataTable(json);
            let arr = [];
            for(let i=0;i<json.columns.length;i++){
                let title = json.columns[i].title;
                arr.push("<a class='btn btn-primary waves-effect toggle-vis' data-column='"+i+"'>"+title+"</a>");
            }
            let combine = arr.join();
            let fix = combine.replace(/,/g, '');
            $("#data-column").html(fix);
            /* ------------------------------
            / DATATABLES SEARCH BY COLUMN
            ------------------------------ */
            let table = $('#Tabel').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: ['copy'],
                destroy: true,
                searching: true,
                order: [[0,'desc']]
            });
            $('a.toggle-vis').on( 'click', function (e) {
                e.preventDefault();
                if($(this).hasClass('btn-warning')){
                    $(this).addClass('btn-primary');
                    $(this).removeClass('btn-warning');
                }else{
                    $(this).addClass('btn-warning');
                    $(this).removeClass('btn-primary');
                }

                // Get the column API object
                let column = table.column( $(this).attr('data-column') );

                // Toggle the visibility
                column.visible( ! column.visible() );
            });
        },
    });
}
let Tabel2 = function(url){
    $('#FormTabel2').html(createSkeleton(1));
    $.ajax( {
        url: url,
        dataType: "json",
        success:function(json) {
            $('#FormTabel2').html("<table id='Tabel2' class='table table-bordered table-striped table-hover'></table>");
            $('#Tabel2').DataTable(json);
            let arr = [];
            for(let i=0;i<json.columns.length;i++){
                let title = json.columns[i].title;
                arr.push("<a class='btn btn-primary waves-effect toggle-vis' data-column2='"+i+"'>"+title+"</a>");
            }
            let combine = arr.join();
            let fix = combine.replace(/,/g, '');
            $("#data-column2").html(fix);
            /* ------------------------------
            / DATATABLES SEARCH BY COLUMN
            ------------------------------ */
            let table = $('#Tabel2').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: ['copy'],
                destroy: true,
                searching: true,
                order: [[0,'asc']]
            });
            $('a.toggle-vis').on( 'click', function (e) {
                e.preventDefault();
                if($(this).hasClass('btn-warning')){
                    $(this).addClass('btn-primary');
                    $(this).removeClass('btn-warning');
                }else{
                    $(this).addClass('btn-warning');
                    $(this).removeClass('btn-primary');
                }

                // Get the column API object
                let column = table.column( $(this).attr('data-column2') );

                // Toggle the visibility
                column.visible( ! column.visible() );
            });
        },
    });
}
$(document).ready(function(){
    let url = "{{route('mining-data')}}?d={{$_GET['d']}}";
    Tabel(url);
    $(document).on('click','#BtnSearchData',function(){
        let month = $('#month').val();
        let url = "{{route('mining-data')}}?d={{$_GET['d']}}&m="+month;
        Tabel(url);
    });
});
$(document).ready(function(){
    let url = "{{route('withdraw-data')}}?d={{$_GET['d']}}";
    Tabel2(url);
    $(document).on('click','#BtnSearchData2',function(){
        let month = $('#month2').val();
        let url = "{{route('withdraw-data')}}?d={{$_GET['d']}}&m="+month;
        Tabel2(url);
    });
});
</script>
@endpush

@section('content')
<!-- Exportable Table -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>
                    {{ $_GET['d'] }}
                </h2>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <table class="profile-table">
                            <tr>
                                <td><b>Worker Name</b></td>
                                <td>:</td>
                                <td>{{$arr['worker_name']}}</td>
                                <td><b>Owner Name</b></td>
                                <td>:</td>
                                <td>{{$arr['owner_name']}}</td>
                            </tr>
                            <tr>
                                <td><b>VGA</b></td>
                                <td>:</td>
                                <td>{{$arr['vga']}}</td>
                                <td><b>Quantity</b></td>
                                <td>:</td>
                                <td>{{$arr['quantity']}}</td>
                            </tr>
                            <tr>
                                <td><b>Placement</b></td>
                                <td>:</td>
                                <td>{{$arr['placement']}}</td>
                                <td><b>Start Mining</b></td>
                                <td>:</td>
                                <td>{{$arr['start_mining']}}</td>
                            </tr>
                            <tr>
                                <td><b>Customer Share</b></td>
                                <td>:</td>
                                <td>{{$arr['customer_share']}}</td>
                                <td><b>Company Share</b></td>
                                <td>:</td>
                                <td>{{$arr['company_share']}}</td>
                            </tr>
                            <tr>
                                <td><b>Wallet Address</b></td>
                                <td>:</td>
                                <td>{{$arr['wallet_address']}}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="text-align: center;">
                        <h4>Total ETH Customer</h4>
                        <h4 id="total_cust">{{$arr['total_eth_customer']}}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="header">
                <h2>Result</h2>
            </div>
            <div class="body">
                <div class="row custom-row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <div class="form-line">
                                <input type="month" class="form-control" id="month" />
                            </div>
                        </div>
                        <button type="button" id="BtnSearchData" class="btn btn-block btn-primary waves-effect"><i class="material-icons">search</i> Search</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p>Hide column:</p>
                        <div id="data-column"></div>
                    </div>
                </div>
                <div class="table-responsive" id="FormTabel"></div>
            </div>
        </div>
        <div class="card">
            <div class="header">
                <h2>Withdraw</h2>
            </div>
            <div class="body">
                <div class="row custom-row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <div class="form-line">
                                <input type="month" class="form-control" id="month2" />
                            </div>
                        </div>
                        <button type="button" id="BtnSearchData2" class="btn btn-block btn-primary waves-effect"><i class="material-icons">search</i> Search</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p>Hide column:</p>
                        <div id="data-column"></div>
                    </div>
                </div>
                <div class="table-responsive" id="FormTabel2"></div>
            </div>
        </div>
    </div>
</div>
<!-- #END# Exportable Table -->
@endsection
