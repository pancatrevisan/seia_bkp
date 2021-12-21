<?php
$sel_lang = 'ptb';
?>
<html lang="ptb">

<head>
    <script>
        //TODO: alterar
        var GLOBAL_YT_READY = true; //false;
        var tag = document.createElement('script');
        console.log("GET CONTROL");
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        console.log("first script Tag");
        console.log(firstScriptTag);
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
    <script  src="<?php echo BASE_URL;?>/external/face-api.js"></script>
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
<script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/stimulis/DrawStimulus.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/Activity.js"></script>
    <script type="text/javascript" src="<?php echo BASE_URL; ?>/activity/Instruction.js"></script>

    <?php
    /* TODO: user
         */


    foreach ($data['instructions'] as $instruction) {
        echo '<script type="text/javascript" src="' . $instruction . '"></script>';
    }
    ?>


    <script>
        var activity = null;
        var animate;
        var paused = false;
        var last = -1;
        var G_FULL_SCREEN = false;
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

            //i_w = window.innerWidth;
            //i_h = window.innerHeight;

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

            //remover
            msg.style.display = 'none';
                notRotated = false;
            //re,pver

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
                alert("Fim da atividade!");
                document.getElementById('fullScreenMessage').innerHTML = 
                '<button type="button" onclick="return_back()" class="btn btn-lg btn-block btn-danger" > Fechar </button>';
                
                
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
                activity.pointerDown(evt);
            });
            canvas.addEventListener("mousemove", function(evt) {
                evt.stopPropagation();
                activity.pointerMove(evt);
            });
            canvas.addEventListener("mouseup", function(evt) {
                evt.stopPropagation();
                activity.pointerUp(evt);
            });

            canvas.addEventListener("touchstart", function(evt) {
                console.log(evt);
                activity.pointerDown(evt);
            });
            canvas.addEventListener("touchmove", function(evt) {
                activity.pointerMove(evt);
            });
            canvas.addEventListener("touchend", function(evt) {
                activity.pointerUp(evt);
            });
            canvas.addEventListener("touchcancel", function(evt) {
                activity.pointerUp(evt);
            });
        }

        function run_timer(id) {
            run(id);
        }
        function return_back(){
            console.log("close");
            window.close();

        }
        function run(id) {
            var canvas = document.getElementById('drawCanvas');

            canvas.width = screen.width;
            canvas.height = screen.height;
            console.log("w: " + screen.width + " h: " + screen.height);
            if (activity == null) {
                return;
            }


            var d = new Date();
            last = d.getTime();
            animate = window.setInterval(update, 66);
            checkScreen();

            if (activity.isReady() && G_FULL_SCREEN && GLOBAL_YT_READY) {
                console.log('ready');
                activity.startRunning();
                activity.resize();
            } else {
                console.log('not ready yet');
                setTimeout(function() {
                    run_timer(id)
                }, 1000);
            }


        }

        function start(id) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText.length <= 0) {
                        console.log("lascou-se");
                    } else {
                        console.log(this.responseText);
                        var canvas = document.getElementById('drawCanvas');

                        activity = new Activity(id, this.responseText, document.getElementById('drawCanvas'), canvas, "<?php echo BASE_URL; ?>");
                        setUpEvents();

                        run();
                    }
                }
            };

            xhttp.open("GET", "index.php?action=getActivity&id=" + id, true);
            xhttp.send();
        }

        document.onfullscreenchange = function(){
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

<body onload="start('<?php echo $data['activity_id'] ?>')">

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
    <div id="YT_PLAYER" style="display: none;"></div>
</body>

</html>