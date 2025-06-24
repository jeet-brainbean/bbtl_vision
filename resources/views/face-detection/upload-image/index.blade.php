@extends('tablar::page') 

@section('content')
<div >
    <x-userheader>
        <x-slot:title>
            Upload Images
        </x-slot>
        <x-slot:subtitle>
            Upload images for face detection analysis. Supported formats: JPEG, PNG, WebP, GIF (max 10mb)
        </x-slot>
    </x-userheader>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Credit Balance</h3>
                        </div>
                        <div class="card-body border-bottom py-3">
                            <p class="text-secondary">{{$credit_require_per_image}} credit required per image</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-deck row-cards mb-4">
                <div class="col-sm col-lg-6">
                    <div class="card">
                        <div class="card-header d-block"> 
                            <h3><i class="fa fa-upload"></i>Upload Images</h3>
                            <h4>Drag and drop images or click to select files </h4>
                        </div>
                        <div class="card-body border-bottom py-3">
                             <div>
                                <div class="text-center py-3 border border-2 mb-2">
                                    <form action="{{ route('postUploadImage') }}" method="POST" enctype="multipart/form-data">
                                        <div class="Neon Neon-theme-dragdropbox">
                                            <div class="Neon-input-icon">
                                                <i class="ti ti-file-image"></i>
                                            </div>
                                            <input 
                                                style="z-index: 999; opacity: 0; width: 92%; height: 200px; position: absolute; right: 0px; left: 0px; margin-right: auto; margin-left: auto;" 
                                                name="faceImage" 
                                                id="faceImage" 
                                                type="file" 
                                            >
                                            <div class="">
                                                <div class="">
                                                    <div class="camera-icon">
                                                        <i class="ti ti-file"></i>
                                                    </div>
                                                    <div class="">
                                                        <h3>Drag&amp;Drop files here</h3> 
                                                        <span style="display:inline-block; margin: 15px 0">or</span>
                                                    </div>
                                                    <a class=""><i class="ti ti-file"></i> Browse Files</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="m-l-40">
                                    <ul>
                                        <li>Supported formats: JPEG, PNG, WebP, GIF</li>
                                        <li>Maximum file size: 10MB</li>
                                        <li>Cost: 1 credit per image</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm col-lg-6">
                    <div class="card">
                        <div class="card-header d-block">
                            <h3><i class="fa fa-upload"></i>Detection Result</h3>
                            <h4>Face detection anlysis for uploaded image </h4>

                        </div>
                        <div class="card-body border-bottom py-3 text-center">
                            <div class="Neon-input-icon">
                                <img id="preview_phpto" src="https://static-00.iconduck.com/assets.00/file-image-icon-1618x2048-ly70lz3m.png" alt="your image" width="100px" />
                            </div>
                            <h3>Upload and process images to see results</h3>
                            <div id="result-box"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('tablar_js')
<script>
   
    $('#faceImage').change(function(){    
        //on change event  

        // add validation

        formdata = new FormData();
        if($(this).prop('files').length > 0)
        {
            file =$(this).prop('files')[0];
            
            formdata.append("photo", file);
            console.log('formdata', file);
            jQuery.ajax({
                url: "{{route('postUploadImage')}}",
                type: "POST",
                 headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                data: formdata,
                processData: false,
                contentType: false,
                success: function (result) {
                    // if all is well
                    // play the audio file
                    if(result.error){
                    alert(result.error)
                    }else{
                            let content = '<div class="table-responsive-sm table-responsive-md"><table class="table table-bordered">';
                            preview_phpto.src = URL.createObjectURL(file);
                            for(var i=0; i<result.faces.length; i++){
                                let data = result.faces[i];
                                if(data.Gender){    content+='<tr><td scope="row">'+(i+1)+'</td><th>Gender: </th><td>'+data.Gender.Value+'</td></tr>';              }
                                if(data.AgeRange){  content+='<tr><td></td><th>Age Range: </th><td>'+data.AgeRange.Low+' - '+data.AgeRange.High+'</td></tr>';       }
                                if(data.Confidence){    content+='<tr><td></td><th>Confidence: </th><td>'+data.Confidence+'</td></tr>';                    }
                                if(data.Smile){    content+='<tr><td></td><th>Smile on Face: </th><td>'+data.Smile.Value +'</td></tr>';               }
                            }  
                            content+='</table></div>';
                            $("#result-box").html(content);
                    }
                }, error: function ( xhr, status, error) {
                  alert( xhr.responseText );
        
                }
            });
        }
    });
</script>
@endsection
