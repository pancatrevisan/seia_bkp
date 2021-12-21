<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!defined('ROOTPATH')) {
    require '../root.php';
}
require_once ROOTPATH . '/utils/checkUser.php';

checkUser(["admin", "professional"], BASE_URL);

$sel_lang = 'ptb';/*
$student_id = $data['studentId'];
$user_id = $_SESSION['username'];
$programId = $data['programId'];*/


isset($data['program_trial_id']) ? $program_trial_id = $data['program_trial_id'] : $program_trial_id = "PREVIEW";
//Load program data
$SQL = "SELECT * FROM ..";
//print_r($data);
?>
<html lang="ptb">

<head>
    <title><?php echo $data['pagetitle'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/activity/views/paper.css">


    <script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/AttributeDescriptor.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/Stimulus.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/DFTImage.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/TextStimulus.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/ImageStimulus.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/AudioStimulus.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/TextInputStimulus.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/YoutubeVideo.js"></script> 
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/CamCaptureStimulus.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/Activity.js"></script>
    <script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/Instruction.js"></script>

    <?php
    /* TODO: user
         */


    foreach ($data['instructions'] as $instruction) {
        echo '<script type="text/javascript" src="' . BASE_URL . "/activity/" . $instruction . '"></script>';
    }
    ?>
    <script>
        var session = JSON.parse('<?php echo $data["session"]; ?>', true);
        console.log(session);
        //activites, reinforces and corrections are 'push-ed' here
        var activityQueue = [];
        var currentActivityInfo = null;

        function genActivityQueue() {

            session['id_programs'] = [];
            for (var i = 0; i < session['programs'].length; i++) {
                session['id_programs'][session['programs'][i].id] = session['programs'][i];
            }

            if (session['continue'] == "new") {
                //if(session['type']=='curriculum'){
                for (var i = 0; i < session['programs'].length; i++) {
                    for (var j = 0; j < session['programs'][i]['activities'].length; j++) {
                        var a = {
                            type: 'ACTIVITY',
                            activity: session['programs'][i]['activities'][j]
                        };
                        activityQueue.push(a);
                    }
                }
                //}
                //else if(session['type']=="program"){
                //load the program only.

                //}
            } else if (session['continue'] == 'continue') {
                if (session['last_program'] == "FROM_START") {
                    for (var i = 0; i < session['programs'].length; i++) {
                        for (var j = 0; j < session['programs'][i]['activities'].length; j++) {
                            var a = {
                                type: 'ACTIVITY',
                                activity: session['programs'][i]['activities'][j]
                            };
                            activityQueue.push(a);
                        }
                    }
                } else {
                    for (var i = 0; i < session['programs'].length; i++) {
                        for (var j = 0; j < session['programs'][i]['activities'].length; j++) {
                            var a = {
                                type: 'ACTIVITY',
                                activity: session['programs'][i]['activities'][j]
                            };
                            activityQueue.push(a);
                        }
                    }
                    var must_continue = true;
                    while (must_continue && activityQueue.length > 0) {
                        var a = activityQueue.splice(0, 1)[0];

                        if (a.activity.ga_id != session['last_group_activity'] && a.activity.ga_program_id != session['last_program']) {
                            must_continue = false;
                        }
                    }

                }
            }

        }





        function saveData() {
            if (session.type == 'preview')
                return;
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                }
            }


            var program_trial_id = currentActivityInfo.activity.program_trial_id;
            var student_id = session['studentId'];
            var activity_id = currentActivityInfo.activity.activity_id;
            var result = activity.getResult();
            var result_data = activity.getResultData();
            var start_date = currentActivityInfo['start_date'];
            var end_date = currentActivityInfo['end_date'];
            var groupactivity_id = currentActivityInfo.activity['ga_id'];
            console.log(currentActivityInfo);
            console.log("SAVE. groupactivity_id: " + groupactivity_id);

            var url = "<?php echo BASE_URL; ?>/program/index.php?action=saveTrial";
            xhttp.open('POST', url, true);
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");


            xhttp.send("program_trial_id=" + program_trial_id + "&student_id=" + student_id +
                "&activity_id=" + activity_id + "&result=" + result + "&result_data=" + result_data +
                "&start_date=" + start_date + "&end_date=" + end_date + "&session_id=" + session['session_id'] +
                "&groupactivity_id=" + groupactivity_id);
        }

        function getReinforcement() {
            console.log("reinforcement type: " + currentActivityInfo.activity.ga_reinforcement_type);
            if (currentActivityInfo.activity.ga_reinforcement_type != 'none') {
                if (currentActivityInfo.activity.ga_reinforcement_type == "definedByGroup") {
                    var program = session['id_programs'][currentActivityInfo.activity.ga_program_id];
                    return {
                        type: program.reinforcement_type,
                        value: program.reinforcement_value
                    };
                } else {
                    return {
                        type: currentActivityInfo.activity.ga_reinforcement_type,
                        value: currentActivityInfo.activity.ga_ga_reinforcement_value
                    };
                }
            }
            return {
                'type': 'none'
            };
        }

        function getCorrection() {
            if (currentActivityInfo.activity.ga_correction_type != 'none') {
                if (currentActivityInfo.activity.ga_correction_type == "definedByGroup") {
                    var program = session['id_programs'][currentActivityInfo.activity.ga_program_id];
                    return {
                        type: program.correction_type,
                        value: program.correction_value
                    };
                } else {
                    return {
                        type: currentActivityInfo.activity.ga_correction_type,
                        value: currentActivityInfo.activity.ga_correction_value
                    };
                }
            }

            return {
                'type': 'none'
            };
        }


        function startNextActivity() {
            if (activityQueue.length <= 0) {
                ///DONE!
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.responseText);
                    }
                }
                var url = "<?php echo BASE_URL; ?>/program/index.php?action=finishSession";
                xhttp.open('POST', url, true);
                xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

                if (session.type != 'preview')
                    xhttp.send("session_id=" + session['session_id']);

                alert("Programa completo.");

                return;
            }
            currentActivityInfo = activityQueue.splice(0, 1)[0];

            if (currentActivityInfo.type == "ACTIVITY") {
                var act_id = currentActivityInfo.activity;
                loadAndRunActivity(act_id.activity_id);
            } else if (currentActivityInfo.type == "REINFORCER") {
                var reinf_id = currentActivityInfo.id;
                loadAndRunActivity(reinf_id);
            } else if (currentActivityInfo.type == "CORRECTION") {
                var act_id = currentActivityInfo.activity;
                //if should show a tip....
                if (currentActivityInfo.correctionType == 'groupWrongTip') {
                    loadAndRunActivity(act_id.activity_id, currentActivityInfo.correctionValue);
                } else {

                    loadAndRunActivity(act_id.activity_id);
                }
            }

        }


        function endActivity() {
            console.log("END ACTIVITY");
            if (currentActivityInfo == null)
                return;
            var e_date = new Date();
            currentActivityInfo['end_date'] = e_date.toJSON();

            if (currentActivityInfo.type == "ACTIVITY" || currentActivityInfo.type == "CORRECTION") {
                saveData();

                var result = activity.getResult();
                console.log("Result:  " + result);
                if (result == 1 || result == 2 || result == 3) {
                    //add reinforcer

                    var reinforer = getReinforcement();
                    if (reinforer.type != 'none') {
                        activityQueue.splice(0, 0, {
                            type: 'REINFORCER',
                            id: reinforer.value
                        });
                    }

                } else if (result == -1 && currentActivityInfo.type == "ACTIVITY") {
                    //add correction.
                    var correction = getCorrection();
                    console.log("CORRECTION " + correction);
                    if (correction.type == "groupWrongTip") { /// show tip
                        activityQueue.splice(0, 0, {
                            type: 'CORRECTION',
                            activity: currentActivityInfo.activity,
                            correctionType: correction.type,
                            correctionValue: correction.value
                        });
                    } else if (correction.type == "groupWrongRepeat") { //repeat...
                        var number = parseInt(correction.value);
                        for (var i = 0; i < number; i++) {
                            activityQueue.splice(0, 0, {
                                type: 'CORRECTION',
                                activity: currentActivityInfo.activity,
                                correctionType: correction.type,
                                correctionValue: correction.value
                            });
                        }
                    } else if (correction.type == "groupWrongNextGroup") { //skip the whole program

                    }

                }

            }
            console.log(activityQueue);
            startNextActivity();
        }
    </script>

    <script>
        function load() {
            setUpEvents();
            genActivityQueue();
            startNextActivity();
        }
    </script>

    <script>
        var activity = null;
        var animate;
        var paused = false;
        var last = -1;

        function mobileAndTabletcheck() {
            var check = false;
            (function(a) {
                if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4)))
                    check = true;
            })(navigator.userAgent || navigator.vendor || window.opera);
            return check;
        }

        function checkScreen() {
            var i_w, i_h;

            var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
            i_w = (iOS) ? screen.width : window.innerWidth;
            i_h = (iOS) ? screen.height : window.innerHeight;

            if (iOS) {
                if (i_h > i_w) {
                    var tm = i_h;
                    i_h = i_w;
                    i_w = tm;
                }

                function preventBehavior(e) {
                    e.preventDefault();
                };

                document.addEventListener("touchmove", preventBehavior, {
                    passive: false
                });
            }
            var browser = navigator.platform;

            var notFullScreen = false;
            var msg_f_screen = document.getElementById('fullScreenMessage');
            if (!document.fullscreenElement && !iOS) {

                msg_f_screen.style.display = 'block';
                notFullScreen = true;

            } else {
                msg_f_screen.style.display = 'none';
            }

            var notRotated = false;
            var msg = document.getElementById('rotateSmartphoneMessage');
            if (mobileAndTabletcheck() && (Math.abs(window.orientation) != 90)) {
                msg.style.display = 'block';
                notRotated = true;
            } else {
                msg.style.display = 'none';
                notRotated = false;
            }

            paused = (notFullScreen || notRotated);
            var canvas = document.getElementById('drawCanvas');
            var dispContent = document.getElementById('displayContent');
            activity.setPause(paused);
            if (paused) {

                canvas.style.display = 'none';
                dispContent.classList.remove('d-none'); // = 'block';
                dispContent.classList.add('d-flex'); // = 'block';
            } else {
                canvas.style.display = 'block';
                dispContent.classList.add("d-none");
                dispContent.classList.remove('d-flex'); // = 'block';
            }




            if (!paused) {
                canvas.width = i_w; //+ "px";
                canvas.height = i_h; // + 'px';
            }


        }

        function openFullscreen(elem) {
            var doc = window.document;
            var docEl = doc.documentElement;

            var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
            var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

            if (!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
                requestFullScreen.call(docEl);
            } else {
                cancelFullScreen.call(doc);
            }

            var msg_f_screen = document.getElementById('fullScreenMessage');
            msg_f_screen.style.display = 'none';
        }



        /**
         * updates states and render
         * @returns {undefined}
         */
        function update() {
            if (activity == null)
                return;

            var d = new Date();
            var curr = d.getTime();

            var dt = curr - last;
            last = curr;
            checkScreen();
            if (paused) {
                return;
            }
            activity.update(dt);
            var canvas = document.getElementById('drawCanvas');
            var context = canvas.getContext("2d");
            activity.render(context);
            if (activity.isDone()) {

                clearInterval(animate);
                endActivity();
            }
        }

        function setUpEvents() {

            document.addEventListener("fullscreenchange", function() {
                checkScreen();
            });
            document.addEventListener("mozfullscreenchange", function() {
                checkScreen();
            });
            document.addEventListener("webkitfullscreenchange", function() {
                checkScreen();
            });
            document.addEventListener("msfullscreenchange", function() {
                checkScreen();
            });
            var canvas = document.getElementById('drawCanvas');

            canvas.addEventListener("mousedown", function(evt) {
                evt.stopPropagation();
                if (activity == null)
                    return;
                activity.pointerDown(evt);
            });
            canvas.addEventListener("mousemove", function(evt) {
                evt.stopPropagation();
                if (activity == null)
                    return;
                activity.pointerMove(evt);
            });
            canvas.addEventListener("mouseup", function(evt) {
                evt.stopPropagation();
                if (activity == null)
                    return;
                activity.pointerUp(evt);
            });

            canvas.addEventListener("touchstart", function(evt) {
                console.log(evt);
                if (activity == null)
                    return;
                activity.pointerDown(evt);
            });
            canvas.addEventListener("touchmove", function(evt) {
                if (activity == null)
                    return;
                activity.pointerMove(evt);
            });
            canvas.addEventListener("touchend", function(evt) {
                if (activity == null)
                    return;
                activity.pointerUp(evt);
            });
            canvas.addEventListener("touchcancel", function(evt) {
                if (activity == null)
                    return;
                activity.pointerUp(evt);
            });
        }

        function run_timer(id) {
            checkScreen();
            run(id);
        }

        function run(id) {
            var canvas = document.getElementById('drawCanvas');

            canvas.width = screen.width;
            canvas.height = screen.height;

            if (activity == null) {
                return;
            }


            var d = new Date();
            last = d.getTime();



            if (activity.isReady()) {
                activity.resize();
                activity.startRunning();
                var s_date = new Date();
                currentActivityInfo['start_date'] = s_date.toJSON();
                animate = window.setInterval(update, 66);

            } else {

                setTimeout(function() {
                    run_timer(id)
                }, 1000);
            }


        }

        function loadAndRunActivity(id, tip = null) {

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText.length <= 0) {
                        console.log("lascou-se");
                    } else {
                        var canvas = document.getElementById('drawCanvas');
                        activity = new Activity(id, this.responseText, document.getElementById('drawCanvas'), canvas, "<?php echo BASE_URL; ?>");
                        if (tip != null) {
                            activity.setShowTip(tip);
                        }
                        run();
                    }
                }
            };

            xhttp.open("GET", "<?php echo BASE_URL; ?>/activity/index.php?action=getActivity&id=" + id, true);
            xhttp.send();
        }
    </script>
    <style>
        #drawCanvas,
        body,
        html {
            padding: 0;
            margin: 0;
        }
    </style>
</head>

<body onload="load()">

    
<img hidden id="_playerEndPosition" src="<?php echo BASE_URL . "/ui/finish-306245_1280.png"; ?>">
<img hidden id="_emotionPosition" src="<?php echo BASE_URL . "/ui/emoji-2074153_1280.png"; ?>">
<img hidden id="_playerStartPosition" src="<?php echo BASE_URL . "/ui/1024px-OOjs_UI_icon_userAvatarOutline-progressive.svg.png"; ?>">
<img hidden id="_camFrame" src="<?php echo BASE_URL . "/ui/camera-2008479_640.png"; ?>">
<img hidden id="_gearIcon" src="<?php echo BASE_URL . "/ui/icons8-engrenagem-64.png"; ?>">
<img hidden id="_audioIcon" src="<?php echo BASE_URL . "/ui/audioPlayIcon.png"; ?>">
<img hidden id="_notFoundIcon" src="<?php echo BASE_URL . "/ui/Image-Not-Found1.png"; ?>">
<img hidden id="_textFrame" src="<?php echo BASE_URL . "/ui/textFrame.png"; ?>">
<img hidden id="_finishButton" src="<?php echo BASE_URL . "/ui/check-24849_640.png"; ?>">

<img hidden id="_correctButton" src="<?php echo BASE_URL . "/ui/correct-button-png-9.png"; ?>">
<img hidden id="_correctWithTipButton" src="<?php echo BASE_URL . "/ui/5dcd519269d67.png"; ?>">
<img hidden id="_wrongButton" src="<?php echo BASE_URL . "/ui/error-24842_640.png"; ?>">
    <div style="display: none;" id='runActivityData'></div>

    <div class="row m-0 p-0">
        <div class="col">
            <div class="container d-flex justify-content-center jumbotron " id="displayContent">


                <div id="fullScreenMessage" style="display:none">
                    <p class="alert alert-warning"><?php echo 'A atividade deve ser realizada em tela cheia!' ?></p>
                    <button class="btn btn-success" onclick="openFullscreen();"> <?php echo 'Alternar para tela cheia' ?> </button>
                </div>

                <div id="rotateSmartphoneMessage" style="display:none">
                    <p class="alert alert-danger"> <?php echo 'Rotacione o celular' ?> </p>
                </div>

            </div>
        </div>
    </div>

    <canvas id="drawCanvas" width="1024" height="768">

    </canvas>
</body>

</html>