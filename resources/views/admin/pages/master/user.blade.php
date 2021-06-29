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
                let column = table.column( $(this).attr('data-column') );

                // Toggle the visibility
                column.visible( ! column.visible() );
            });
        },
    });
}
$(document).ready(function(){
    let url = "{{route('master.user-data')}}";
    Tabel(url);
    $(document).on('click','#BtnSearchData',function(){
        let month = $('#month').val();
        let url = "{{route('master.user-data')}}";
        Tabel(url);
    });
});
$(document).on('click','#edit',function(){
    let id = $(this).attr('data-id');
    let name = $(this).attr('data-name');
    let email = $(this).attr('data-email');
    let role = $(this).attr('data-role');

    $("#editModalID").html(id);
    $("#name").val(name);
    $("#email").val(email);
    $("#role").val(role).change();
});
$(document).on('click','#saveModal',function(){
    $(this).attr('disabled','disabled');
    let url = "{{route('master.user-put')}}";
    $.ajax({
        type:'PUT',
        url:url,
        data:{
            "_token" : $('meta[name="csrf-token"]').attr('content'),
            "id" : $("#editModalID").html(),
            "name" : $("#name").val(),
            "email" : $("#email").val(),
            "role" : $("#role").val()
        },
        success:function(){
            $('#saveModal').removeAttr('disabled','disabled');
            $('#editModal').modal('hide');
            let url = "{{route('master.user-data')}}";
            Tabel(url);
        },
        error:function(){
            $('#saveModal').removeAttr('disabled','disabled');
        }
    });
});
$(document).on('click','#saveModalAdd',function(){
    $(this).attr('disabled','disabled');
    let url = "{{route('master.user-add')}}";
    $.ajax({
        type:'POST',
        url:url,
        data:{
            "_token" : $('meta[name="csrf-token"]').attr('content'),
            "name" : $("#name_add").val(),
            "email" : $("#email_add").val(),
            "role" : $("#role_add").val(),
            "password": $("#password_add").val()
        },
        success:function(){
            $('#saveModalAdd').removeAttr('disabled','disabled');
            $('#editModal').modal('hide');
            let url = "{{route('master.user-data')}}";
            Tabel(url);
        },
        error:function(){
            $('#saveModalAdd').removeAttr('disabled','disabled');
        }
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
                    Master User
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <button id='addUser' class="btn waves-effect btn-primary" data-toggle='modal' data-target='#addModal'>
                            <i class="material-icons">add</i> Add User
                        </button>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p>Hide column:</p>
                        <div id="data-column"></div>
                    </div>
                </div>
                <div class="table-responsive" id="FormTabel"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add User</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group form-float">
                            <label>Name</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="name_add" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>Email</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="email_add" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>Password</label>
                            <div class="form-line">
                                <input type="password" class="form-control" id="password_add" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>Role</label>
                            <div class="form-line">
                                <select class="form-control show-tick" id="role_add">
                                    <option value="">-- Select Role --</option>
                                    <option value="Administrator">Administrator</option>
                                    @foreach ($profile['menus'] as $key => $data)
                                        <option value="{{ $profile['owner_name'][$key] }}">{{$profile['owner_name'][$key]}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveModalAdd" class="btn btn-link waves-effect">ADD</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
<!-- #END# Exportable Table -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="defaultModalLabel">Edit Profile ID <span id="editModalID"></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group form-float">
                            <label>Name</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="name" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>Email</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="email" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <select class="form-control show-tick" id="role">
                                    <option value="">-- Select Role --</option>
                                    <option value="Administrator">Administrator</option>
                                    @foreach ($profile['menus'] as $key => $data)
                                        <option value="{{ $profile['owner_name'][$key] }}">{{$data}} (Owner : {{$profile['owner_name'][$key]}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveModal" class="btn btn-link waves-effect">ADD</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
@endsection
