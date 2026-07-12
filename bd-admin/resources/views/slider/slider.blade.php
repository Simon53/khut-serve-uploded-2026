@extends('layout.app')
@section('title', 'Khut::Site Slider')
@section('content')

<style>
   .table th img, .jsgrid .jsgrid-table th img, .table td img, .jsgrid .jsgrid-table td img {
    width: 30px;
    height: 30px;
    border-radius: 0px;
}
</style>
<div class="container-fluid">
   <div class="page-header"><h3 class="page-title">Slider Imager</h3></div>
   <div class="row mb-4">
      <div class="col-md-12">
         <button type="button" class="btn btn-info btn-fw" data-toggle="modal" data-target="#addSliderModal">+ Add New</button>
      </div>
   </div>

   <div class="row">
      <div class="col-lg-12 grid-margin stretch-card">
         <div class="card">
            <div class="card-body">
               <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                     <thead class="thead-dark">
                        <tr>
                           <th>Sl</th>
                           <th>Images</th>
                           <th>Is Active</th>
                           <th>Edit</th>
                           <th>Delete</th>
                        </tr>
                     </thead>
                     <tbody class="sliderRow">
                        @foreach($sliders as $index => $slider)
                        <tr data-id="{{ $slider->id }}">
                           <td>{{ $index + 1 }}</td>
                           <!-- <td>
                              <img src="{{ asset('storage/'.$slider->slider_location) }}" style="width:100px; height:auto">
                           </td> -->
                           <td>
                                @php
                                    $file = asset('storage/'.$slider->slider_location);
                                    $ext = strtolower(pathinfo($slider->slider_location, PATHINFO_EXTENSION));
                                @endphp

                                @if(in_array($ext, ['mp4','mov','avi','wmv','webm','mkv']))
                                    <video width="120" height="70" controls>
                                        <source src="{{ $file }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img src="{{ $file }}" style="width:100px;height:auto;">
                                @endif
                            </td>
                           <td>
                              <span class="badge {{ $slider->is_active=='Yes' ? 'badge-success' : 'badge-danger' }}">
                                 {{ $slider->is_active }}
                              </span>
                           </td>
                           <td>
                              <button class="btn btn-primary editMenuBtn" data-id="{{ $slider->id }}" data-status="{{ $slider->is_active }}">Edit</button>
                           </td>
                           <td>
                              <button class="btn btn-danger btn-sm deleteMenuBtn" data-id="{{ $slider->id }}">Delete</button>
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div> 
<!-- Add Slider Modal -->
<div class="modal fade" id="addSliderModal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <form id="addSliderForm" enctype="multipart/form-data">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Create New Slider</h5>
               <button type="button" class="btn btn-danger" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
               <div class="form-group">
                  <label>Slider Image</label>
                  <input type="file" name="slider_image" id="sliderImage" class="form-control" accept="image/*,video/*" >
                  <div class="progress mt-3" id="uploadProgress" style="display:none;height:22px;">
                    <div
                            id="uploadProgressBar"
                            class="progress-bar progress-bar-striped progress-bar-animated"
                            role="progressbar"
                            style="width:0%">
                            0%
                    </div>
                </div>
                  <br/><br/>
                  or<br/><br/>
                  <input type="text" name="video_url" class="form-control" placeholder="YouTube / Facebook Embed URL">
               </div>
               <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="is_active" id="isActive">
                  <label class="form-check-label" for="isActive">Is Active</label>
               </div>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-success">Save</button>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
         </div>
      </form>
   </div>
</div>


<!-- Edit Slider Modal -->
<div class="modal fade" id="editSliderModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="editSliderForm" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="edit_id" id="edit_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Edit Slider</h5>
          <button type="button" class="btn btn-danger" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="file" name="slider_image" class="form-control mb-2">
          <div class="form-check">
            <input type="checkbox" name="is_active" id="edit_is_active" class="form-check-input">
            <label class="form-check-label" for="edit_is_active">Is Active</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>



@endsection  
@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
 $(document).ready(function(){
    const token = $('meta[name="csrf-token"]').attr('content');

    // Add slider
//     $('#addSliderForm').submit(function(e){
//     e.preventDefault();
//     let formData = new FormData(this);
//     $.ajax({
//         url: "{{ url('/slider/store') }}",
//         type: 'POST',
//         data: formData,
//         headers: {
//             'X-CSRF-TOKEN': token
//         },
//         contentType: false,
//         processData: false,
//         success: function(res){
//             if(res.status == 'success'){
//                 toastr.success(res.message);
//                 $('#addSliderModal').modal('hide');
//                 let media = '';
//                 // embed video
//                 if(res.slider.video_url){
//                     media = `
//                         <iframe
//                             src="${res.slider.video_url}"
//                             width="120"
//                             height="80"
//                             frameborder="0"
//                             allowfullscreen>
//                         </iframe>
//                     `;
//                 }

//                 // uploaded video
//                 else if(
//                     res.slider.url &&
//                     (
//                         res.slider.url.endsWith('.mp4') ||
//                         res.slider.url.endsWith('.webm') ||
//                         res.slider.url.endsWith('.mov') ||
//                         res.slider.url.endsWith('.avi')
//                     )
//                 ){
//                     media = `
//                         <video width="120" controls>
//                             <source src="${res.slider.url}">
//                         </video>
//                     `;
//                 }
//                 // image
//                 else {

//                     media = `
//                         <img src="${res.slider.url}" style="width:100px">
//                     `;
//                 }
//                 let row = `
//                     <tr data-id="${res.slider.id}">
//                         <td>#</td>
//                         <td>${media}</td>
//                         <td>
//                             <span class="badge ${res.slider.is_active=='Yes' ? 'badge-success' : 'badge-danger'}">
//                                 ${res.slider.is_active}
//                             </span>
//                         </td>
//                         <td>
//                             <button class="btn btn-primary editMenuBtn"
//                                     data-id="${res.slider.id}">
//                                 Edit
//                             </button>
//                         </td>
//                         <td>
//                             <button class="btn btn-danger btn-sm deleteMenuBtn"
//                                     data-id="${res.slider.id}">
//                                 Delete
//                             </button>
//                         </td>
//                     </tr>
//                 `;

//                 $('.sliderRow').prepend(row);
//             } else {
//                 toastr.error(res.message);
//             }
//         },
//         error: function(xhr){
//             console.log(xhr.responseText);
//             toastr.error('Server Error');
//         }
//     });
// });


$('#addSliderForm').submit(function(e){
    e.preventDefault();
    let formData = new FormData(this);
    $("#uploadProgress").show();
    $("#uploadProgressBar")
        .css("width","0%")
        .text("0%");

    $.ajax({
        url:"{{ url('/slider/store') }}",
        type:"POST",
        data:formData,
        headers:{
            'X-CSRF-TOKEN':token
        },
        processData:false,
        contentType:false,
        timeout:0,
        xhr:function(){
            let xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(e){
                if(e.lengthComputable){
                    let percent = Math.round((e.loaded/e.total)*100);
                    $("#uploadProgressBar")
                        .css("width",percent+"%")
                        .text(percent+"%");
                }

            }, false);
            return xhr;
        },

        success:function(res){
            $("#uploadProgressBar")
                .css("width","100%")
                .text("100%");
            setTimeout(function(){
                $("#uploadProgress").hide();
            },800);
            if(res.status=="success"){
                toastr.success(res.message);
                $('#addSliderModal').modal('hide');
                location.reload();

            }else{
                toastr.error(res.message);
            }
        },
        error:function(xhr){
            $("#uploadProgress").hide();
            console.log(xhr.responseText);
            toastr.error("Upload Failed");

        }
    });
});

     // show modal with existing data
    $(document).on('click', '.editMenuBtn', function(){
        let id = $(this).data('id');
        let status = $(this).data('status');
        $('#edit_id').val(id);
        $('#edit_is_active').prop('checked', status === 'Yes');
        $('#editSliderModal').modal('show');
    });

    // Update slider
     $('#editSliderForm').submit(function(e){
        e.preventDefault();
        let formData = new FormData(this);
        let id = $('#edit_id').val();
        $.ajax({
            url: `/bd-admin/public/slider/update/${id}`, // absolute path
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': token },
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                if(res.status === 'success'){
                    toastr.success(res.message);
                    $('#editSliderModal').modal('hide');
                    location.reload();
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr){
                if(xhr.status === 419){
                    toastr.error('Session expired. Please login again.');
                } else {
                    toastr.error('Update failed!');
                }
            }
        });
    });

    // Delete slider
    $(document).on('click', '.deleteMenuBtn', function(){
        let id = $(this).data('id');
        let row = $(this).closest('tr');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert!",
            icon: 'warning',
            showCancelButton:true,
            confirmButtonColor:'#e3342f',
            cancelButtonColor:'#6c757d',
            confirmButtonText:'Yes, delete it!'
        }).then((result)=>{
            if(result.isConfirmed){
                $.ajax({
                    url: `/bd-admin/public/slider/delete/${id}`,
                    type: 'DELETE',
                    headers:{ 'X-CSRF-TOKEN': token },
                    success: function(res){
                        if(res.status=='success'){
                            toastr.success(res.message);
                            row.remove();
                        } else toastr.error(res.message);
                    }
                });
            }
        });
    });
});


</script>
@endsection