<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!defined('ROOTPATH')) {
    require '../root.php';
}
require_once ROOTPATH . '/utils/checkUser.php';

checkUser(["admin", "professional", "tutor"], BASE_URL);

$sel_lang = 'ptb';/*
$student_id = $data['studentId'];
$user_id = $_SESSION['username'];
$programId = $data['programId'];*/

isset($data['athena']) ? $athena = $data['athena'] : $athena = "false";

isset($data['sessionprogramTrial_id']) ? $sessionprogramTrial_id = $data['sessionprogramTrial_id'] : $sessionprogramTrial_id = "PREVIEW";
//Load program data
$SQL = "SELECT * FROM ..";
//print_r($data);
?>
<html lang="ptb">

<head>
    <title><?php echo $data['pagetitle'] ?></title>
    <script src="<?php echo BASE_URL; ?>/external/face-api.js"></script>
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
    <script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/DrawStimulus.js"></script>

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
        var athena = "<?php echo $athena; ?>";

        athena = athena == "true";

        var GLOBAL_YT_READY = false;
        var tag = document.createElement('script');

        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];

        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);


        ///TODO:
        var GLOBAL_YT_PLAYER = null;
        var GLOBAL_YT_CONTAINER = null; 
        var GLOBAL_YT_PLAYER_STATUS = -1;
        ///TODO:

        function onYouTubeIframeAPIReady() {
            console.log("YT API READY!!!!!");
            GLOBAL_YT_PLAYER = new YT.Player('YT_PLAYER', {
            height: '360',
            width: '640',
            videoId: 'M7lc1UVf-VE',
            events: {
                'onReady': function (event) {
                    playerCreated();
                }
            }
            });
        }

        function playerCreated(){
            GLOBAL_YT_READY = true;
            GLOBAL_YT_CONTAINER = document.getElementById("YT_PLAYER");
        }
    </script>

    <script>
        var ret = '<?php echo $data["session"]; ?>';


        var SESSIONS = JSON.parse('<?php echo $data["session"]; ?>', true);
        var ACTIVITIES_TO_LOAD = 0;
        var PRE_LOADED_ACTIVITIES = [];
        var REWARDS = [];
        var G_FULL_SCREEN = false;
        var AUTO_REINF_PREFS = null;
        var finished_pre_load = false;
        var current_session = 0;
        console.log(SESSIONS);
        var session = SESSIONS[0];

        //activites, reinforces and corrections are 'push-ed' here
        var activityQueue = [];
        var currentActivityInfo = null;

        function genActivityQueue() {
            console.log("__GEN ACTIVIT QUEUE__");
            console.log("session['session_follow_activity_order']: " + session['session_follow_activity_order']);
            session['id_activities'] = {};
            //follow order
            if (session['session_follow_activity_order'] == "1" || session['session_follow_activity_order'] == "2") {

                for (var i = 0; i < session['activities'].length; i++) {
                    var a = {
                        type: 'ACTIVITY',
                        activity: session['activities'][i]
                    };
                    activityQueue.push(a);
                }
                return;
            }
            //follow order

            for (var i = 0; i < session['activities'].length; i++) {
                session['id_activities'][session['activities'][i].spa_id] = session['activities'][i];
            }

            var a = {
                type: 'ACTIVITY',
                activity: session['activities'][0]
            };
            activityQueue.push(a);
            console.log(activityQueue);

        }



        function saveData() {
            if (session.type == 'preview')
                return;
            //console.log("SAVE----------------------------");
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    //console.log(this.responseText);
                }
            }

            var sessionprogramTrial_id = session['sessionprogramTrial_id'];
            var student_id = session['student_id'];
            var result = activity.getResult();
            var result_data = activity.getResultData();
            if(result_data == "ONLY_SHOW"){
                return;
            }
            var start_date = currentActivityInfo['start_date'];
            var end_date = currentActivityInfo['end_date'];


            var spa_id = currentActivityInfo.activity.spa_id;
            var pref_sel = false;
            console.log("result data");
            console.log(result_data);
            var json = JSON.parse(result_data, true);


            console.log("json result_data: ");
            console.log(json);
            if ('save_screenshot' in json) {
                if (json['save_screenshot'] == true) {
                    console.log("save screenshot");

                    var date_k = new Date(end_date);

                    var date_k_str = date_k.getDate() + date_k.getMonth() + date_k.getFullYear() + date_k.getHours() + date_k.getMinutes() + date_k.getSeconds();

                    var ss_key = sessionprogramTrial_id + "_" + date_k_str;
                    activity.saveTempScreen(ss_key, student_id);
                    json['screenshot_id'] = ss_key;
                }
            }

            if ('type' in json) {
                if (json['type'] == "preferenceSelection" && currentActivityInfo.type != "AUTO_REINFORCER") {
                    pref_sel = true;
                }
            }

            result_data = JSON.stringify(json);

            var url = "";
            //if (!pref_sel) {
            url = "<?php echo BASE_URL; ?>/sessionProgram/index.php?action=saveSessionProgramActivityTrial";
            //} else {
            //   console.log("Update PReference List");
            //    url = "<?php echo BASE_URL; ?>/sessionProgram/index.php?action=updatePreferenceList";
            //}
            if (!athena) {


                xhttp.open('POST', url, true);
                xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");


                xhttp.send("sessionprogramTrial_id=" + sessionprogramTrial_id + "&student_id=" + student_id +
                    "&spa_id=" + spa_id + "&result=" + result + "&result_data=" + result_data +
                    "&start_date=" + start_date + "&end_date=" + end_date);
            }
        }

        function getReinforcement() {

            return {
                type: currentActivityInfo.activity.spa_reinforcer_type,
                value: currentActivityInfo.activity.spa_id //spa_reinforcer_value
            };

        }

        function getCorrection() {

            return {
                type: currentActivityInfo.activity.spa_correction_type,
                value: currentActivityInfo.activity.spa_correction_value
            };

        }

        function return_back() {

            window.history.back();

        }

        function startNextActivity() {
            if (activityQueue.length <= 0) {
                current_session++;
                if (current_session < SESSIONS.length) {
                    console.log("Next session");
                    session = SESSIONS[current_session];
                    genActivityQueue();
                } else {
                    ///DONE!
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            //console.log(this.responseText);
                        }
                    }
                    var url = "<?php echo BASE_URL; ?>/program/index.php?action=finishSession";
                    xhttp.open('POST', url, true);
                    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");

                    if (session.type != 'preview')
                        xhttp.send("session_id=" + session['session_id']);

                    alert("Programa completo.");

                    document.getElementById('fullScreenMessage').innerHTML =
                        '<button type="button" onclick="return_back()" class="btn btn-lg btn-block btn-danger" > Voltar </button>';

                    return;
                }

            }
            currentActivityInfo = activityQueue.splice(0, 1)[0];

            if (currentActivityInfo.type == "ACTIVITY") {
                var act_id = currentActivityInfo.activity;
                loadAndRunActivity(act_id.spa_id);
            } else if (currentActivityInfo.type == "REINFORCER" || currentActivityInfo.type == "AUTO_REINFORCER") {
                var reinf_id = currentActivityInfo.id;

                if (REWARDS[reinf_id] == null) {
                    startNextActivity();
                    return;
                }
                loadAndRunActivity(reinf_id, "", true);

            } else if (currentActivityInfo.type == "CORRECTION") {

                var act_id = currentActivityInfo.activity;
                var canvas = document.getElementById('drawCanvas');
                var a = new Activity(act_id.id, activity.xml, document.getElementById('drawCanvas'), canvas, "<?php echo BASE_URL; ?>");
                PRE_LOADED_ACTIVITIES[act_id.spa_id] = a;

                //if should show a tip....
                if (currentActivityInfo.correctionType == 'tip') {
                    console.log("show tip");
                    console.log(currentActivityInfo);

                    loadAndRunActivity(act_id.spa_id, currentActivityInfo.correctionValue);
                } else {

                    loadAndRunActivity(act_id.spa_id);
                }
            }
        }

        function checkCorrection() {
            console.log("%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%");
            console.log("CHECK CORRECTION FOR...");
            console.log(currentActivityInfo.activity);
            var correction = getCorrection();
            if (correction.type == "tip") { /// show tip
                //se a correcao for uma dica, adiciona a mesma atividade; o tratamento de acerto/erro é feito no 'endActivity'
                activityQueue.splice(0, 0, {
                    type: 'CORRECTION',
                    activity: currentActivityInfo.activity,
                    correctionType: correction.type,
                    correctionValue: correction.value
                });  
               
                
            } else if (correction.type == "repeat") { //repeat...

                console.log("&7777777REPETIR");
                if (currentActivityInfo.activity.spa_next_after_correction_id != null && currentActivityInfo.activity.spa_next_after_correction_id.length > 0 && currentActivityInfo.activity.spa_next_after_correction_id != 'none' && currentActivityInfo.activity.spa_next_after_correction_id != 'nenhuma' && currentActivityInfo.activity.spa_next_after_correction_id != 'undefined' && currentActivityInfo.activity.spa_next_after_correction_id != 'null') {
                
                    var a = {
                        type: 'ACTIVITY',
                        activity: session['id_activities'][currentActivityInfo.activity.spa_next_after_correction_id]
                    };
                    
                    activityQueue.splice(0, 0, a);
                    
                }
                var number = parseInt(correction.value);
                console.log("NUMERO DE REPETICOES? " + number);
                console.log(activityQueue);
                for (var i = 0; i < number; i++) {
                    activityQueue.splice(0, 0, {
                        type: 'CORRECTION',
                        activity: currentActivityInfo.activity,
                        correctionType: correction.type,
                        correctionValue: correction.value
                    });
                }
                console.log(activityQueue);
            } else {
                //next on wrong
                if (session['session_follow_activity_order'] == "2" || session['session_follow_activity_order'] == "1") {
                    //se é 'seguir ordem' ou 'linha de base', não há o que corrigir.
                    return;
                }
                if (currentActivityInfo.activity.spa_next_on_wrong_id != null && currentActivityInfo.activity.spa_next_on_wrong_id.length > 0 && currentActivityInfo.activity.spa_next_on_wrong_id != 'none' && currentActivityInfo.activity.spa_next_on_wrong_id != 'undefined' && currentActivityInfo.activity.spa_next_on_wrong_id != undefined && currentActivityInfo.activity.spa_next_on_wrong_id != 'nenhuma' && currentActivityInfo.activity.spa_next_on_wrong_id != 'null') {
                    console.log("=-=-=-=-=-=-=-=-ADD ACTIVITY 11");
                    var a = {
                        type: 'ACTIVITY',
                        activity: session['id_activities'][currentActivityInfo.activity.spa_next_on_wrong_id]
                    };
                  
                    activityQueue.splice(0, 0, a);
                   
                }
            }
        }


        function endActivity() {
            console.log("*****************************#*********************************");
            console.log(currentActivityInfo.activity);
            console.log(activityQueue);
            if (currentActivityInfo == null)
                return;
            var e_date = new Date();
            currentActivityInfo['end_date'] = e_date.toJSON();
            if (session['session_follow_activity_order'] == "1") { /// linha de base...
                console.log("***************************** 1 *********************************");
                saveData();
            } else if (session['session_follow_activity_order'] == "2") { //seguir a ordem e apresentar recompensa
                console.log("***************************** 2 *********************************");
                if (currentActivityInfo.type == "ACTIVITY" || currentActivityInfo.type == "CORRECTION") {
                    saveData();
                    var result = activity.getResult();

                    if (result == 1 || result == 2 || result == 3 || result == this.activity.RESULT_CORRECT_DATA) {
                        //add reinforcer
                        var reinforer = getReinforcement();
                        if (reinforer.type != 'none') {
                            //// AQUI ////
                            //// 
                            console.log("Reinforcer tyoe: " + reinforer.type);
                            if (reinforer.type == 'auto_reinforce') {
                                console.log("AUTO REINFORCER");
                                activityQueue.splice(0, 0, {
                                    type: 'AUTO_REINFORCER',
                                    id: reinforer.value
                                });
                            } else {
                                activityQueue.splice(0, 0, {
                                    type: 'REINFORCER',
                                    id: reinforer.value
                                });
                            }
                        }
                    } else if (result == -1 && currentActivityInfo.type == "ACTIVITY") {
                        //add correction.
                        checkCorrection();
                    }
                }
            } else if (currentActivityInfo.type == "ACTIVITY" || currentActivityInfo.type == "CORRECTION") { //desvios...
                console.log("&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&");
                saveData();

                var result = activity.getResult();
                console.log("Result:  " + result);
                
                if (result == 1 || result == 2 || result == 3 || result == this.activity.RESULT_CORRECT_DATA || result == this.activity.RESULT_NEUTRAL) {
                    //add reinforcer
                    if (currentActivityInfo.type == "ACTIVITY") {
                        if (currentActivityInfo.activity.spa_next_on_correct_id != 'null' && currentActivityInfo.activity.spa_next_on_correct_id != null && currentActivityInfo.activity.spa_next_on_correct_id.length > 0 && currentActivityInfo.activity.spa_next_on_correct_id != 'none' && currentActivityInfo.activity.spa_next_on_correct_id != 'undefined' && currentActivityInfo.activity.spa_next_on_correct_id != undefined) {
                            console.log("ADD ACTIVITY 1");
                            var a = {
                                type: 'ACTIVITY',
                                activity: session['id_activities'][currentActivityInfo.activity.spa_next_on_correct_id]
                            };                            
                            activityQueue.splice(0, 0, a);                         
                        }
                    }
                    else if (currentActivityInfo.type == "CORRECTION") {
                        var correction = getCorrection();
                        
                        if(correction.type == "repeat"){
                            //faz nada

                        }
                        
                        else if (currentActivityInfo.activity.spa_next_after_correction_id != 'null' && currentActivityInfo.activity.spa_next_after_correction_id != null && currentActivityInfo.activity.spa_next_after_correction_id.length > 0 && currentActivityInfo.activity.spa_next_after_correction_id != 'none' && currentActivityInfo.activity.spa_next_after_correction_id != 'undefined' && currentActivityInfo.activity.spa_next_after_correction_id != undefined) {
                            var a = {
                                type: 'ACTIVITY',
                                activity: session['id_activities'][currentActivityInfo.activity.spa_next_after_correction_id]
                            };                        
                            activityQueue.splice(0, 0, a);                        
                        }
                    }

                    var reinforer = getReinforcement();
                    if (reinforer.type != 'none') {
                        if (reinforer.type == 'auto_reinforce') {
                            activityQueue.splice(0, 0, {
                                type: 'AUTO_REINFORCER',
                                id: reinforer.value
                            });
                        } else {
                           
                            activityQueue.splice(0, 0, {
                                type: 'REINFORCER',
                                id: reinforer.value
                            });
                        }


                    }


                } else if (result == -1 && currentActivityInfo.type == "ACTIVITY") {
                    //errou a atividade; vai checar se tem correção, qual é e adicionar à fila :)
                    checkCorrection();
                } else if (result == -1 && currentActivityInfo.type == "CORRECTION") { // caso da correcao e o aluno errou novamente...
                    //caso tenha apresentado dica e mesmo assim errou,
                    //alert("aquele caso esperado :)");
                    var correction = getCorrection();
                        
                    if(correction.type == "repeat"){
                        //faz nada

                    }
                    else if (currentActivityInfo.activity.spa_next_after_correction_wrong_id != 'null' && currentActivityInfo.activity.spa_next_after_correction_wrong_id != null && currentActivityInfo.activity.spa_next_after_correction_wrong_id.length > 0 && currentActivityInfo.activity.spa_next_after_correction_wrong_id != 'none' && currentActivityInfo.activity.spa_next_after_correction_wrong_id != 'undefined' && currentActivityInfo.activity.spa_next_after_correction_wrong_id != undefined) {
                        var a = {
                            type: 'ACTIVITY',
                            activity: session['id_activities'][currentActivityInfo.activity.spa_next_after_correction_wrong_id]
                        };
                    
                        activityQueue.splice(0, 0, a);
                    }
                    else{ // para manter os programas continuando, se errou na correcao, executa a proxima apos correcao...
                        console.log("MANDENTO COMPATIBILIDADE...");
                        var a = {
                            type: 'ACTIVITY',
                            activity: session['id_activities'][currentActivityInfo.activity.spa_next_after_correction_id]
                        };                    
                        activityQueue.splice(0, 0, a);
                    }
                }
            }
            startNextActivity();
        }
    </script>

    <script>
        function load() {
            
            setUpEvents();
            preload_activities();

            waitPreLoad();
        }

        function waitPreLoad() {
            finished_pre_load = check_preload();
            console.log("pre loaded?" + finished_pre_load);
            if (finished_pre_load) {
                genActivityQueue();
                startNextActivity();
            } else {
                setTimeout(function() {
                    waitPreLoad()
                }, 1000);
            }
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
            console.log("Try to run... " + id);
            var canvas = document.getElementById('drawCanvas');

            canvas.width = screen.width;
            canvas.height = screen.height;

            if (activity == null) {
                console.log("null activity");

                return;
            }


            var d = new Date();
            last = d.getTime();



            if (activity.isReady() && G_FULL_SCREEN) {
                console.log("start update");
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




        function check_preload() {
            var num_loaded = ACTIVITIES_TO_LOAD;
            console.log(PRE_LOADED_ACTIVITIES);
            console.log(REWARDS);
            for (var key in PRE_LOADED_ACTIVITIES) {

                if (PRE_LOADED_ACTIVITIES[key] == null || !PRE_LOADED_ACTIVITIES[key].isReady())
                    num_loaded--;
            }
            for (var key in REWARDS) {
                if (REWARDS[key] == null || !REWARDS[key].isReady())
                    num_loaded--;
            }

            var percentageMessage = document.getElementById('percentageMessage');
            percentageMessage.classList.remove('d-none');
            
            var message = "Carregando ... ";
            var pecentage = 100.0 / ACTIVITIES_TO_LOAD;
            pecentage = pecentage * num_loaded;
            message += parseInt(pecentage) + "";
            message += "&percnt;"
            percentageMessage.innerHTML = "<div class='jumbotron' >" + message + "</div>";



            //TODO
            
            if (num_loaded == ACTIVITIES_TO_LOAD && GLOBAL_YT_READY) {
                percentageMessage.classList.add('d-none');
                return true;
            } else
                return false;
        }

        function preload_activities() {
            var ids = [];
            var reinforcer_ids = [];
            console.log(SESSIONS);
            for (var s = 0; s < SESSIONS.length; s++) {
                var sess = SESSIONS[s];
                console.log("SESSION: " + sess);
                for (var i = 0; i < sess['activities'].length; i++) {
                    var id = sess['activities'][i]['spa_activity_id'];
                    var spa_id = sess['activities'][i]['spa_id']

                    if (!(spa_id in ids)) {
                        ids[spa_id] = id;
                    }

                    if (sess['activities'][i]['spa_reinforcer_type'] != "none") {
                        console.log("TYPO REFORÇO: " + sess['activities'][i]['spa_reinforcer_type']);
                        if (sess['activities'][i]['spa_reinforcer_type'] == "auto_reinforce") {
                            reinforcer_ids[spa_id] = "AUTO_REINFORCER";
                        } else {


                            var reinforcer_id = sess['activities'][i]['spa_reinforcer_value'];
                            if (!(spa_id in reinforcer_ids)) {
                                //TODO: chave para reforco
                                reinforcer_ids[spa_id] = reinforcer_id;
                            }
                        }
                    }
                }
            }
            console.log(ids);

            //for (var i = 0; i < session['activities'].length; i++) {  
            for (var key in ids) {

                var id = ids[key]; // session['activities'][i]['spa_activity_id'];
                var spa_id = key;

                if (!(spa_id in PRE_LOADED_ACTIVITIES)) {
                    //if(spa_id == id)
                    //  REWARDS[id] = null;
                    //else
                    PRE_LOADED_ACTIVITIES[spa_id] = null;
                    ACTIVITIES_TO_LOAD++;
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {

                        if (this.readyState == 4 && this.status == 200) {


                            if (this.responseText.length <= 0) {
                                console.log("lascou-se");

                            } else {
                                var canvas = document.getElementById('drawCanvas');
                                var resp = JSON.parse(this.responseText, true);
                                var a = new Activity(id, resp['xml'], document.getElementById('drawCanvas'), canvas, "<?php echo BASE_URL; ?>");


                                /* if(resp['spa_id']=="REINFORCER"){
                                     console.log("reinforcer ");
                                     REWARDS[resp['id']] = a;
                                 }
                                 else*/
                                PRE_LOADED_ACTIVITIES[resp['spa_id']] = a;

                            }
                        }
                    };

                    xhttp.open("GET", "<?php echo BASE_URL; ?>/activity/index.php?action=getActivity_json&id=" + id + "&spa_id=" + spa_id, true);
                    xhttp.send();
                }
            }

            for (var key in reinforcer_ids) {
                var id = reinforcer_ids[key]; // session['activities'][i]['spa_activity_id'];
                var spa_id = key;
                if (id == "AUTO_REINFORCER") {
                    console.log(">>>>>>>>>>>>>>..load auto reinforcer;;");
                    REWARDS[spa_id] = null;

                    ACTIVITIES_TO_LOAD++;
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {

                        if (this.readyState == 4 && this.status == 200) {

                            console.log("RESPONSE");
                            console.log(this.responseText);
                            if (this.responseText.length <= 0) {
                                console.log("lascou-se");

                            } else {
                                var canvas = document.getElementById('drawCanvas');
                                var obj;
                                var hadError = false;
                                try{
                                    obj = JSON.parse(this.responseText);
                                }catch(e) {
                                    alert("Erro carregando o reforço " ); // error in the above string (in this case, yes)!
                                    hadError = true;
                                    ACTIVITIES_TO_LOAD--;
                                }
                                if(!hadError){
                                    if (obj['has_ret'] == '1') {
                                        console.log("HAS RET 0");
                                        delete REWARDS[obj['spa_id']]; // = "";  
                                        ACTIVITIES_TO_LOAD--;
                                        return;
                                    }
                                    console.log(obj);
                                    var a = new Activity(obj['activity_id'], obj['xml'], document.getElementById('drawCanvas'), canvas, "<?php echo BASE_URL; ?>");
                                    var inst = a.instructions[0];

                                    var prefs = obj['prefs']['preference'];
                                    var d = new Date();
                                    var n = d.getMinutes();
                                    var pos = n % 2;
                                    console.log("Pref: ");
                                    console.log(prefs[pos]);
                                    inst.stimuliToShow = prefs[pos]['value'];
                                    inst.setShowing(true);




                                    AUTO_REINF_PREFS = obj['prefs']['preference'];



                                    //var a = new Activity(id, resp['xml'], document.getElementById('drawCanvas'), canvas, "<?php echo BASE_URL; ?>");
                                    REWARDS[obj['spa_id']] = a;
                                }
                            }
                        }
                    };

                    var student_id = session['student_id'];
                    xhttp.open("GET", "<?php echo BASE_URL; ?>/activity/index.php?action=getAutoReinforcer_json&student_id=" + student_id + "&spa_id=" + spa_id, true);
                    xhttp.send();

                } else {
                    if ((!(spa_id in REWARDS))) {
                        REWARDS[spa_id] = null;

                        ACTIVITIES_TO_LOAD++;
                        var xhttp = new XMLHttpRequest();
                        xhttp.onreadystatechange = function() {

                            if (this.readyState == 4 && this.status == 200) {

                                console.log("RESPONSE");
                                console.log(this.responseText);
                                if (this.responseText.length <= 0) {
                                    console.log("lascou-se");

                                } else {
                                    var canvas = document.getElementById('drawCanvas');
                                    var resp = JSON.parse(this.responseText, true);
                                    var a = new Activity(id, resp['xml'], document.getElementById('drawCanvas'), canvas, "<?php echo BASE_URL; ?>");
                                    REWARDS[resp['spa_id']] = a;

                                }
                            }
                        };

                        xhttp.open("GET", "<?php echo BASE_URL; ?>/activity/index.php?action=getActivity_json&id=" + id + "&spa_id=" + spa_id, true);
                        xhttp.send();
                    }
                }
            }
        }



        function loadAndRunActivity(id, tip = null, reward = false) {

            console.log("__RUN__: " + id);

            if (reward) {
                console.log("*********reward");
                console.log("id: " + id);

                var a = REWARDS[id];

                var canvas = document.getElementById('drawCanvas');
                activity = new Activity(a.id, a.xml, document.getElementById('drawCanvas'), canvas, "<?php echo BASE_URL; ?>");

                if (currentActivityInfo.type == "AUTO_REINFORCER") {
                    var inst = activity.instructions[0];
                    var d = new Date();
                    var n = d.getMinutes();
                    var pos = n % 2;
                    inst.stimuliToShow = AUTO_REINF_PREFS[pos]['value'];
                    inst.setShowing(true);
                }

            } else {
                activity = PRE_LOADED_ACTIVITIES[id];
            }

            if (tip != null) {
                activity.setShowTip(tip);
            }

            run(id);
        }
        document.onfullscreenchange = function() {
            if (document.fullscreenElement) {
                G_FULL_SCREEN = true;

            }
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
                <div id="percentageMessage">

                </div>
            </div>
        </div>
    </div>

    <canvas id="drawCanvas" width="1024" height="768">

    </canvas>
    <div id="YT_PLAYER" style="display: none;"></div>
</body>

</html>