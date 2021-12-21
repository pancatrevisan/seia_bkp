class YoutubeVideo extends Stimulus{
    /**
     * 
     * @param {type} databaseID
     * @param {type} name
     * @param {type} owner_id
     * @param {type} description
     * @param {type} type
     * @param {type} presentable_stimuli
     * @param {type} version
     * @param {type} localID
     * @return {Stimuli}
     */
    constructor(localID, activity, instruction, url=null, size=[200,150], pos=[200,200]){
        super(null,'video', localID,true,true, activity, instruction);

        this.url = url;
        if(this.url!=null)
            this.videoId = this.youtube_parser(this.url);
        else
            this.videoId = '  ';
        
        this.setURL(this.url,size, pos);
        this.player = null;
        this.shouldRender = true;
       this.playerDone = true;
       this.startedToPlay = false;
        //https://img.youtube.com/vi/<insert-youtube-video-id-here>/0.jpg
    }

    getControl(){
        if(GLOBAL_YT_READY){
            var maxSize  = [this.renderImage.size[0],this.renderImage.size[1]];
            this.player = GLOBAL_YT_PLAYER;
            this.player.setSize(maxSize[0], maxSize[1]);
            this.player.loadVideoById(this.videoId, 0, "small");     
        }
    }

    
    setURL(url, size=[300,150], position=[200,150]){
        this.url = url;
        if(url!=null)
            this.videoId = this.youtube_parser(this.url);
        else
            this.videoId = '  ';
        var thumbnail = document.createElement("img");
        thumbnail.crossOrigin = 'Anonymous';
        if(url==null)
            thumbnail.src = "http://cors-anywhere.herokuapp.com/http://img.youtube.com/vi/"+this.videoId+"/0.jpg";
        else{
            thumbnail.crossOrigin = 'Anonymous';
            thumbnail.src = "http://cors-anywhere.herokuapp.com/http://img.youtube.com/vi/"+this.videoId+"/0.jpg";
        }
        thumbnail.id  = this.localID;
        thumbnail.hidden = true;
        this.activity.addStimulusLocally(thumbnail);
        this.renderImage = new DFTImage(this.localID,size ,position,this.instruction,this, true);
        
        
    }

    removeFromBody(){
        if(this.player!=null && this.player.hasOwnProperty("stopVideo"))
            this.player.stopVideo();
        var container = GLOBAL_YT_CONTAINER;
        console.log(container);
        if(container!=null){
            container.style.display = "none";
        }
    }

    ///source: https://stackoverflow.com/questions/3452546/how-do-i-get-the-youtube-video-id-from-a-url
    youtube_parser(url){
        var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
        var match = url.match(regExp);
        return (match&&match[7].length==11)? match[7] : false;
    }

    exportXML(xmlDoc){
        var text_xml = xmlDoc.createElement('video');
        var txtData =[];
        
        
        txtData['isClicable'] = this.isClickable;
        txtData['isDraggable'] = this.isDraggable;
        txtData['localID'] = this.localID;
        txtData['posX'] = this.renderImage.position[0];
        txtData['posY'] = this.renderImage.position[1];
        txtData['sizeX'] = this.renderImage.size[0];
        txtData['sizeY'] = this.renderImage.size[1];
       
        
       
        txtData['url'] = this.url;
        
        return [text_xml,txtData];
    }

    renderPreviewImage(ctx, scale){
        if(this.renderImage!=null && this.shouldRender){
            this.renderImage.render(ctx, scale);
        }
    }

    render(ctx, scale=1){
       //throw new Error('You have to implement the render method!');
        if(!this.shouldRender){
        return;
        }
        if(this.renderImage!=null && this.activity.editing){
            this.renderImage.render(ctx, scale);
        }
        
    }


    createAndAddPlayer(){
        //this.getControl();
    }
    
    hide(){
        var container = GLOBAL_YT_CONTAINER; //document.getElementById("video_"+this.localID);
        if(container!=null){
            container.style.display = "none";
            if(this.player!=null && this.playerDone){
                this.player.stopVideo();
                
            }
        }
    }
    show(){
        this.getControl();
        var container = GLOBAL_YT_CONTAINER; //document.getElementById("video_"+this.localID);
        if(container!=null){
            
            container.style.display = "block";
            container.style.position='absolute';
            container.style.zIndex = "-3";
            container.resize='none';

            var drawPos = [this.renderImage.position[0],this.renderImage.position[1]];
            var maxSize  = [this.renderImage.size[0],this.renderImage.size[1]];
            
            container.style.left = drawPos[0];
            container.style.top  = drawPos[1];
            container.width = ""+maxSize[0];
            container.height = ""+maxSize[1]; 
            if(this.activity.isRunning()){

                console.log("play -----");
                this.startToPlay();
            }
            else{
                console.log("not running");
            }
        }
            
    }

    endedVideo(){
        if(this.player.getPlayerState() == 0)
            return true;
        return false;

    }

    startToPlay(){
        if(this.player!=null && this.playerDone){
                this.startedToPlay = true;
                
                this.player.playVideo();
        }

    }

    renderPreview(ctx, scale){
        //throw new Error('You have to implement the renderPreview method!');
        this.render(ctx,scale);
    }
    
    pointerDown(evt) {
        //throw new Error('You have to implement the pointerDown method!');
        console.log("YT Video pointer down");
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
        //throw new Error('You have to implement the editPointerUp method!');
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
    wasPointed(){
        return this.renderImage.wasPointed();
    }
}


