@extends('tablar::page')

@section('content')
<div>
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
                                    <form action="{{ route('postUploadImage') }}" method="POST"
                                        enctype="multipart/form-data">
                                        <div class="Neon Neon-theme-dragdropbox">
                                            <div class="Neon-input-icon">
                                                <i class="ti ti-file-image"></i>
                                            </div>
                                            <input
                                                style="z-index: 999; opacity: 0; width: 92%; height: 200px; position: absolute; right: 0px; left: 0px; margin-right: auto; margin-left: auto;"
                                                name="faceImage" id="faceImage" type="file">
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
                                <div id="resultContent"></div>
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
                                <img id="preview_phpto" src="{{asset('assets/file-upload.png')}}" alt="your image"
                                    width="100px" />
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
const ageSetting = @json($age_setting);
</script>

<script>
$('#faceImage').change(function() {
    //on change event

    // add validation

    formdata = new FormData();
    if ($(this).prop('files').length > 0) {
        file = $(this).prop('files')[0];

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
            success: function(result) {
                // if all is well
                // play the audio file
                if (result.error) {
                    alert(result.error)
                } else {
                    let resultContent = '';
                    let content =
                        '<div class="table-responsive-sm table-responsive-md"><table class="table table-bordered">';
                    preview_phpto.src = URL.createObjectURL(file);

                    let successCount = 0;
                    let errorCount = 0;
                    var soundTrackPath = "{{ asset('assets/sounds/success.mp3') }}";
                    for (let i = 0; i < result.faces.length; i++) {
                        let data = result.faces[i];
                        content += '<tr><td colspan="3"><strong>Face #' + (i + 1) +
                            '</strong></td></tr>';

                        if (data.Gender) {
                            content += '<tr><td></td><th>Gender: </th><td>' + data.Gender.Value +
                                '</td></tr>';
                        }

                        if (data.AgeRange) {
                            const low = data.AgeRange.Low;
                            const high = data.AgeRange.High;

                            console.log('Age Range:', low + ' - ' + high);
                            console.log('Success Age Range:', ageSetting.success_min_age + ' - ' +
                                ageSetting.success_max_age);
                            console.log('Error Age Range:', ageSetting.error_min_age + ' - ' +
                                ageSetting.error_max_age);

                            content += '<tr><td></td><th>Age Range: </th><td>' + low + ' - ' +
                                high + '</td></tr>';

                            const isOverlapping = (rangeLow, rangeHigh, settingLow,
                                settingHigh) => {
                                return rangeLow <= settingHigh && rangeHigh >= settingLow;
                            };

                            if (isOverlapping(low, high, ageSetting.success_min_age, ageSetting
                                    .success_max_age)) {
                                successCount++;
                            } else if (isOverlapping(low, high, ageSetting.error_min_age, ageSetting
                                    .error_max_age)) {
                                errorCount++;
                            }
                        }

                        if (data.Confidence) {
                            content += '<tr><td></td><th>Confidence: </th><td>' + data.Confidence +
                                '</td></tr>';
                        }

                        if (data.Smile) {
                            content += '<tr><td></td><th>Smile on Face: </th><td>' + data.Smile
                                .Value + '</td></tr>';
                        }
                    }
                    content += '</table></div>';

                    // Show success or error message
                    if (successCount > 0 && errorCount === 0) {
                        soundTrackPath = "{{ asset('assets/sounds/success.mp3') }}";
                        resultContent +=
                            `<div class="alert alert-success mt-3">Success! All detected faces fall within the preferred age range.</div>`;
                    } else if (errorCount == 0 && successCount == 0) {
                        soundTrackPath = "{{ asset('assets/sounds/info.mp3') }}";
                        resultContent +=
                            `<div class="alert alert-info mt-3">Info: Detected ages do not match either range.</div>`;
                    } else if (errorCount > 0) {
                        soundTrackPath = "{{ asset('assets/sounds/error.mp3') }}";
                        resultContent +=
                            `<div class="alert alert-danger mt-3">Warning! Some faces fall within the error age range.</div>`;
                    } else {
                        //default case
                        soundTrackPath = "{{ asset('assets/sounds/info.mp3') }}";
                        resultContent +=
                            `<div class="alert alert-info mt-3">Info: Detected ages do not match either range.</div>`;
                    }

                    $("#result-box").html(content);
                    $("#resultContent").html(resultContent);
                    const audio = new Audio(soundTrackPath);
                    audio.play().catch(function(error) {
                        console.warn("Audio playback failed:", error);
                    });
                }
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);

            }
        });
    }
});

function isOverlapping(rangeLow, rangeHigh, settingLow, settingHigh) {
    return rangeLow <= settingHigh && rangeHigh >= settingLow;
}
</script>
@endsection