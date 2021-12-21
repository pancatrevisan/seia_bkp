class CamCaptureStimulus extends Stimulus{
    
    constructor(localID, activity, instruction,  position, size){
        
        super(null,'camCapture', localID,false,false, activity, instruction);
        console.log("cam_capture_constructor");
        //var size = [64,64];
        this.renderImage = new DFTImage('_camFrame',size ,position,instruction,this, true);        
        this.renderImage.borderColor = "00F0FF";
        
        this.cameraView = document.createElement("video");
        this.cameraView.autoplay = false;
        this.cameraView.id = this.localID;
        this.cameraView.style.position='absolute';
        this.cameraView.style.resize='none';
        this.cameraView.style.zIndex = 8;
        
        this.videoStarted = false;
        document.body.appendChild(this.cameraView);
        
        this.faceAPILoaded = false;
        this.faceVisible = true;
        this.emotionMap = {"angry":"anger", "disgusted":"disgust", "fearful":"fear","happy":"hapiness","neutral":"neutral","sad":"sadness","surprised":"surprise"};

        this.faceNotVisibleText = document.createElement("div");
        this.faceNotVisibleText.classList.add("d-none", "alert", "alert-danger");
        this.faceNotVisibleText.innerHTML = "Face não encontrada. Procure um local com melhor iluminação!";
        this.faceNotVisibleText.style.position='absolute';
        this.faceNotVisibleText.style.bottom = "20px";
        this.faceNotVisibleText.style.left = "50px";
        this.faceNotVisibleText.style.zIndex = 9;
        document.body.appendChild(this.faceNotVisibleText);

        this.currentEmotionDetected = document.createElement("div");
        this.currentEmotionDetected.classList.add("d-none", "alert", "alert-info");
        this.currentEmotionDetected.style.position='absolute';
        this.currentEmotionDetected.style.bottom = "20px";
        this.currentEmotionDetected.style.left = "50px";
        this.currentEmotionDetected.style.zIndex = 9;
        document.body.appendChild(this.currentEmotionDetected);


        this.lastEmotion = "";

        ///load face api
        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
            faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
            faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
            faceapi.nets.faceExpressionNet.loadFromUri('/models')
          ]).then(this.setFaceAPILoaded());
          this.hide();
    }

    setFaceNotVisiblle(){
        this.faceVisible = false;

    }
setFaceVisible(){
    this.faceVisible = true;
}
    
    
    getWidth(){
        return this.cameraView.width;
    }
    getHeight(){
        return this.cameraView.height;
    }
    isReady(){
        return this.faceAPILoaded && this.videoStarted;
    }

    setFaceAPILoaded(){
        this.faceAPILoaded = true;
        console.log("face api loaded...");
    }

    hide(){
        
        this.cameraView.classList.add("d-none");
        this.faceNotVisibleText.classList.add("d-none");
        this.currentEmotionDetected.classList.add('d-none');
    }
    show(){
        
        this.cameraView.classList.remove("d-none");
        this.faceNotVisibleText.classList.remove("d-none");
        this.currentEmotionDetected.classList.remove('d-none');
    }
    
    removeFromBody(){
        
        document.body.removeChild(this.cameraView);
    }
     wasPointed(){
        
        return this.renderImage.wasPointed();
    }

    startDetection(){
        setInterval(async () => {
            this.detectEmotion();
          }, 1000);
    }
    
    async detectEmotion(){
        //video?
        if(!this.isReady())
            return;
        const displaySize = { width: this.getWidth(), height: this.getHeight() };
        
       // faceapi.matchDimensions(canvas, displaySize);
        
        const detections =  await  faceapi.detectAllFaces(this.cameraView, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceExpressions();
       // console.log(detections);
        
        //const resizedDetections = faceapi.resizeResults(detections, displaySize);
        
        if(detections.length <=0 ){
            this.lastEmotion  = "";
            this.setFaceNotVisiblle();
        }
        else{
            this.setFaceVisible();
            var higher_val = -99999;
            var best_emotion = "";
            for(var i = 0; i < detections.length; i++){
                var d = detections[i];
                

                for(var emotion in d['expressions']){
                    if(d['expressions'][emotion]>higher_val){
                        higher_val = d['expressions'][emotion];
                        best_emotion = emotion;
                    }
                }
            }
           
            this.lastEmotion = this.emotionMap[best_emotion];
            this.currentEmotionDetected.innerHTML = this.lastEmotion;
        }

    }

    exportXML(xmlDoc){
        var text_xml = xmlDoc.createElement('camCapture');
        var txtData =[];    
        
        
        txtData['isClicable'] = this.isClickable;
        txtData['isDraggable'] = this.isDraggable;
        txtData['localID'] = this.localID;
        txtData['posX'] = this.renderImage.position[0];
        txtData['posY'] = this.renderImage.position[1];
        txtData['sizeX'] = this.renderImage.size[0];
        txtData['sizeY'] = this.renderImage.size[1];
        
        return [text_xml,txtData];
    }
    render(ctx, scale=1){
        
       
       var drawPos = [this.renderImage.position[0],this.renderImage.position[1]];
       var maxSize  = [this.renderImage.size[0],this.renderImage.size[1]];
       
       this.cameraView.style.left = drawPos[0];
       this.cameraView.style.top  = drawPos[1];

       this.cameraView.style.width = parseInt(maxSize[0])+"px";
       this.cameraView.style.height  = parseInt(maxSize[1]+"px");
       this.cameraView.width = parseInt(maxSize[0]);
       this.cameraView.height = parseInt(maxSize[1]);
        
       this.faceNotVisibleText.classList.add("d-none");
        if(!this.faceVisible){
            this.faceNotVisibleText.classList.remove("d-none");//, "alert ", "alert-danger");
            this.currentEmotionDetected.classList.add('d-none');
        }
        else{
            this.currentEmotionDetected.classList.remove('d-none');
        }
      
      
    }


    startVideo(){
        var me = this;
        this.cameraView.onplay = function(){
            console.log(this);
            console.log("START_video");
            me.videoStarted = true;
            me.show();
        };

        if (navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                me.cameraView.srcObject = stream;
                
                console.log("era para mostrar...");
                console.log(stream);
                me.cameraView.play();
            })
            .catch(function (err0r) {
                console.log("Something went wrong! " + err0r);
            });
        }
        
        //this.cameraView.addEventListener("play", this.setVideoStarted);
    }
    
    
    renderPreview(ctx, scale){
       
       
       this.renderImage.render(ctx, scale);
        
    }
    
     pointerDown(evt) {
        //throw new Error('You have to implement the pointerDown method!');
    }
    
    pointerUp(evt) {
        //throw new Error('You have to implement the pointerUp method!');
    }
    pointerMove(evt) {
        //throw new Error('You have to implement the pointerMove method!');
    }
    pointerDrag(evt){
        //throw new Error('You have to implement the pointerDrag method!');
    }
    
    editPointerUp(evt){
        
        
    }
    
    editPointerDown(evt){
        //throw new Error('You have to implement the editMouseDown method!');
    }
    
    editPointerMove(evt){
        //throw new Error('You have to implement the editMouseDrag method!');
    }
    editPointerDrag(evt){
        //throw new Error('You have to implement the editPointerDrag method!');
    }
    
}





