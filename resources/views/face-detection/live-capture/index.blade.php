@extends('tablar::page')
@section('tablar_css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
@endsection
@section('content')
<div class="m-b-10 m-t-20">
    <x-userheader>
        <x-slot:title>
            Live Face Detection
            </x-slot>
            <x-slot:subtitle>
                Start your camera, Capture a snapshot, and get instance face detection results
                </x-slot>
    </x-userheader>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Camera Feed</h3>
                        </div>
                        <div class="card-body border-bottom py-3">
                            <p class="text-secondary">Start camera to begin</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-deck row-cards mb-4">
                <div class="col-sm col-lg-6">
                    <div class="card">
                        <div class="card-body border-bottom py-3">
                            <form id="live-capture-form" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="Neon Neon-theme-dragdropbox">
                                    <div class="text-center">

                                        <div id="my_camera"></div>
                                        <div id="camera-note">
                                            <div class="camera-icon">
                                                <i class="ti ti-camera"></i>
                                            </div>
                                            <h3>Camera not started</h3>
                                        </div>
                                    </div>
                                    <input type="hidden" name="image" class="image-tag" id="image-tag">
                                    <div class="d-grid gap-2">
                                        <button type="submit" id="btn-proceed-photo"
                                            class="btn btn-primary fs-16">Proceed Photo</button>
                                        <a onClick="capturePhoto()" id="btn-capture-photo"
                                            class="btn btn-dark text-white fs-16">Capture Photo</a>
                                        <a onClick="startCamera()" id="btn-start-camera"
                                            class="btn btn-dark text-white fs-16">Start Camera</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detection Result</h3>
                        </div>
                        <div class="card-body border-bottom py-3">
                            <p class="text-secondary">Face detection analysis and statistic</p>
                            <div class="text-center p-l-20 p-t-20 p-b-20 p-r-20">
                                <div class="Neon-input-icon">
                                    <img id="preview_phpto" src="{{asset('assets/file-upload.png')}}" alt="your image"
                                        width="100px" />
                                </div>
                                <h7 id="result-info">Capture and images to see detection results</h7>
                                <div id="result-box"></div>
                            </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>

<script>
$("#btn-capture-photo").css('visibility', 'hidden');
$("#btn-proceed-photo").css('visibility', 'hidden');

function startCamera() {
    // Show a loading message or visual feedback if needed
    $('#camera-note').text('Starting camera... please wait').show();

    setTimeout(function() {
        Webcam.set({
            width: 490,
            height: 350,
            image_format: 'jpeg',
            jpeg_quality: 90
        });

        Webcam.attach('#my_camera');
        $("#btn-capture-photo").css('visibility', 'visible');
        $("#btn-start-camera").css('visibility', 'hidden');
        $("#btn-proceed-photo").css('visibility', 'hidden');
        $('#camera-note').hide(); // hide the note after camera starts
    }, 2000); // 2 seconds = 2000 milliseconds
}


function capturePhoto() {

    Webcam.snap(function(data_uri) {
        $("#image-tag").val(data_uri);
        document.getElementById('my_camera').innerHTML = '<img src="' + data_uri + '"/>';
    });
    $("#btn-start-camera").css('visibility', 'visible');
    $("#btn-capture-photo").css('visibility', 'hidden');
    $("#btn-proceed-photo").css('visibility', 'visible');
}

$('#live-capture-form').on('submit', function() {

    event.preventDefault();
    $("#btn-start-camera").css('visibility', 'hidden');
    $("#btn-capture-photo").css('visibility', 'hidden');
    $("#btn-proceed-photo").css('visibility', 'hidden');
    var file = $("#image-tag").val();

    formdata = new FormData(this);
    jQuery.ajax({
        url: "{{route('proceedLiveCapture')}}",
        type: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formdata,
        processData: false,
        contentType: false,
        success: function(result) {
            if (result['error']) {
                alert(result['error']);
            } else {
                let content =
                    '<div class="table-responsive-sm table-responsive-md"><table class="table table-bordered">';

                let successCount = 0;
                let errorCount = 0;
                var soundTrackPath = "{{ asset('assets/sounds/success.mp3') }}";

                for (var i = 0; i < result.faces.length; i++) {
                    let data = result.faces[i];

                    if (data.Gender) {
                        content += '<tr><td scope="row">' + (i + 1) + '</td><th>Gender: </th><td>' +
                            data.Gender.Value + '</td></tr>';
                    }

                    if (data.AgeRange) {
                        const low = data.AgeRange.Low;
                        const high = data.AgeRange.High;
                        let label = '';
                        if (isOverlapping(low, high, ageSetting.success_min_age, ageSetting
                                .success_max_age)) {
                            successCount++;
                            label = ' ✅ (Success Range)';
                        } else if (isOverlapping(low, high, ageSetting.error_min_age, ageSetting
                                .error_max_age)) {
                            errorCount++;
                            label = ' ❌ (Error Range)';
                        }

                        content += '<tr><td></td><th>Age Range: </th><td>' + low + ' - ' + high +
                            label + '</td></tr>';
                    }

                    if (data.Confidence) {
                        content += '<tr><td></td><th>Confidence: </th><td>' + data.Confidence +
                            '</td></tr>';
                    }

                    if (data.Smile) {
                        content += '<tr><td></td><th>Smile on Face: </th><td>' + data.Smile.Value +
                            '</td></tr>';
                    }
                }

                content += '</table></div>';
                if (successCount > 0 && errorCount === 0) {
                    soundTrackPath = "{{ asset('assets/sounds/success.mp3') }}";
                    content +=
                        `<div class="alert alert-success mt-3">Success! All detected faces fall within the preferred age range.</div>`;
                } else if (errorCount == 0 && successCount == 0) {
                    soundTrackPath = "{{ asset('assets/sounds/info.mp3') }}";
                    content +=
                        `<div class="alert alert-info mt-3">Info: Detected ages do not match either range.</div>`;
                } else if (errorCount > 0) {
                    soundTrackPath = "{{ asset('assets/sounds/error.mp3') }}";
                    content +=
                        `<div class="alert alert-danger mt-3">Warning! Some faces fall within the error age range.</div>`;
                } else {
                    //default case
                    soundTrackPath = "{{ asset('assets/sounds/info.mp3') }}";
                    content +=
                        `<div class="alert alert-info mt-3">Info: Detected ages do not match either range.</div>`;
                }
                $("#result-box").html(content);
                $("#result-info").css('visibility', 'hidden');
                Webcam.reset('#my_camera');

                preview_phpto.src = file;
            }
        },

        error: function(xhr, status, error) {
            alert(xhr.responseText);
            $("#btn-start-camera").css('visibility', 'visible');
            $("#btn-capture-photo").css('visibility', 'visible');
            $("#btn-proceed-photo").css('visibility', 'visible');
        }
    });
});

function isOverlapping(rangeLow, rangeHigh, settingLow, settingHigh) {
    return rangeLow <= settingHigh && rangeHigh >= settingLow;
}
</script>
@endsection