class PreferenceSelect extends Instruction {

    resize(scale) {
        //nothin special
    }

    constructor(data = { 'type': 'Prompt', 'position': -1, 'editable': true, 'next': -1 }, activity) {
        super(data, activity);
        this.editableAttributes.push(
            new AttributeDescriptor("images", ['image', 'text', 'video'], true, "Adicionar Estímulo", 'add/remove'),
            new AttributeDescriptor("showTime", ['integer'], false, "Tempo de exibição", 'swap')
        );
        this.selectionOrder = [];

        this.time = 0;
        this.description = "Teste de preferência entre os estímulos.";
        if (data == null)
            return;

        this.showTime = data['showTime'];// * 1000;
        this.allowUse = true;
        this.editable = true;
        this.timer = 0;
        this.waitTime = 2000; ///2segundos 
        this.wait = false;

        this.showing = false;

        this.stimuliToShow = null;



    }

    terminate() {
        var i = 0;
        for (i = 0; i < this.stimulis.length; i++) {

            if (this.stimulis[i].type == 'audio') {
                this.stimulis[i].stop();
            }
            else if (this.stimulis[i].type == 'video') {

                this.stimulis[i].removeFromBody();
            }
        }
        var resultData = {};
        resultData['type'] = "preferenceSelection";
        resultData['activity_id'] = this.activity.id;
        resultData['preference'] = [];
        resultData['pref_ids'] = [];

        for (var i = 0; i < this.selectionOrder.length; i++) {
            resultData['preference'].push(this.exportStimulus(this.selectionOrder[i], true));
            resultData['pref_ids'].push(this.exportStimulus(this.selectionOrder[i], false));
        }
        console.log(resultData);
        this.activity.resultData = JSON.stringify(resultData);
        this.activity.next = this.onCorrect;
        this.activity.done = true;
        this.activity.result = this.activity.RESULT_NEUTRAL;
    }
    exportXML() {
        var exp = [];
        exp['stimuli'] = [];
        exp['header'] = [];
        exp['data'] = [];

        exp['header']['type'] = "PreferenceSelect";
        exp['header']['position'] = this.position;
        exp['header']['next'] = this.next;
        exp['header']['description'] = this.description;
        exp['header']['editable'] = this.editable;

        exp['data']['showTime'] = this.showTime;

        exp['stimuli'] = this.stimulis;
        return exp;
    }
    setShowing(show) {
        this.showing = show;
        var i;
        if (this.showing) {
            for (i = 0; i < this.stimulis.length; i++) {

                if (this.stimulis[i].type == 'video' && this.stimulis[i].localID == this.stimuliToShow) {
                    this.stimulis[i].createAndAddPlayer();

                }
            }



            for (i = 0; i < this.stimulis.length; i++) {
                this.stimulis[i].renderImage.position = [10, 10];
                this.stimulis[i].renderImage.setSize([780, 580]);
            }

        }

    }
    pointerDown(evt) {

        if (this.present)
            return;

        if (this.wait)
            return;

        var pos = this.activity.lastPointerPos;





        var clicked = -1;
        for (var i = 0; i < this.stimulis.length && clicked < 0; i++) {

            if (this.stimulis[i].shouldRender) {
                this.stimulis[i].pointerDown(evt);
                console.log("Stimuli: ");
                console.log(this.stimulis[i]);
                if (this.stimulis[i].type != "audio" && this.stimulis[i].wasPointed()) {
                    clicked = i;
                }
            }
        }

        if (clicked >= 0) {
            this.stimulis[clicked].shouldRender = false;
            this.selectionOrder.push(this.stimulis[clicked]);
            console.log("added: " + this.selectionOrder.length + " Total: " + this.stimulis.length);
            this.swapFarthest();
            console.log(this.selectionOrder);
            if (this.selectionOrder.length >= this.stimulis.length) {
                /*resultData['type'] = "preference";
                
                resultData['stimulis'] = [];
                for(var j =0; j< this.stimulis.length; j++){
                    resultData['stimulis'].push(this.exportStimulus(this.stimulis[j]));
                }

                resultData['preferenceOrder'] = [];
                for(var j =0; j< this.selectionOrder.length; j++){
                    resultData['preferenceOrder'].push(this.exportStimulus(this.stimulis[this.selectionOrder[j]]));
                }

                this.done = true;
                this.next = this.onWrong;
                this.activity.result = this.activity.RESULT_WRONG;

                this.activity.resultData =JSON.stringify(resultData);
                */
                this.terminate();
            }
            this.timer = 0;
            this.wait = true;

        }

    }



    swapFarthest() {
        var s1 = -1, s2 = -1;
        var left = 1000, right = -1000;

        for (var i = 0; i < this.stimulis.length; i++) {
            if (this.stimulis[i].shouldRender) {
                if (this.stimulis[i].renderImage.position[0] < left) {
                    left = this.stimulis[i].renderImage.position[0];
                    s1 = i;
                }

                if (this.stimulis[i].renderImage.position[0] > right) {
                    right = this.stimulis[i].renderImage.position[0];
                    s2 = i;
                }
            }
        }
        if (s1 != s2 && s1 >= 0 && s2 >= 0) {
            console.log("S1: " + s1 + " S2: " + s2);
            var tmpPos1 = [this.stimulis[s1].renderImage.position[0], this.stimulis[s1].renderImage.position[1]];
            var tmpSize1 = [this.stimulis[s1].renderImage.size[0], this.stimulis[s1].renderImage.size[1]];

            var tmpPos2 = [this.stimulis[s2].renderImage.position[0], this.stimulis[s2].renderImage.position[1]];
            var tmpSize2 = [this.stimulis[s2].renderImage.size[0], this.stimulis[s2].renderImage.size[1]];

            //  this.stimulis[s2].renderImage.setSize(tmpSize1);
            //  this.stimulis[s1].renderImage.setSize(tmpSize2);

            this.stimulis[s1].renderImage.position = [tmpPos2[0], tmpPos2[1]];
            this.stimulis[s2].renderImage.position = [tmpPos1[0], tmpPos1[1]];




        }
    }

    pointerUp(evt) {

    }

    startRunning() {
        super.startRunning();
        console.log("start running");
        var i;
        for (i = 0; i < this.stimulis.length; i++) {
            if (this.stimulis[i].type == 'video' && !this.activity.paused && this.showing) {
                console.log('show');
                // this.stimulis[i].createAndAddPlayer();
                this.stimulis[i].show();

            }
        }
    }
    pause() {

        if (this.activity.paused) {
            var i;
            for (i = 0; i < this.stimulis.length; i++) {
                if (this.stimulis[i].type == 'audio') {

                    if (!this.stimulis[i].played) {
                        this.stimulis[i].stop();
                    }
                }
                if (this.stimulis[i].type == 'video') {
                    //this.stimulis[i].hide();
                }
            }
        }
        else {
            var i;
            for (i = 0; i < this.stimulis.length; i++) {
                if (this.stimulis[i].type == 'audio') {

                    if (!this.stimulis[i].played) {
                        this.stimulis[i].play();
                    }
                }
                if (this.stimulis[i].type == 'video') {
                    //this.stimulis[i].show();
                }
            }
        }

    }

    update(dt) {
        if (this.activity.paused)
            return;
        if (this.wait) {
            this.timer += dt;
            if (this.timer >= this.waitTime) {
                this.wait = false;
            }
            return;
        }



        var i = 0;
        if (!this.activity.paused && this.showing) {
            for (i = 0; i < this.stimulis.length; i++) {
                if (this.stimulis[i].type == 'video' && this.stimulis[i].localID == this.stimuliToShow) {

                    if (!this.stimulis[i].playerDone)
                        return;
                    if (!this.stimulis[i].startedToPlay) {

                        return;

                    }
                    if (this.stimulis[i].type == 'video' && !this.activity.paused && !this.stimulis[i].startedToPlay) {

                        // this.stimulis[i].createAndAddPlayer();
                        this.stimulis[i].show();

                    }

                    // console.log("total time: " + this.showTime +" video time: " + this.stimulis[i].player.getCurrentTime());
                    if (this.stimulis[i].player.getCurrentTime() >= this.showTime) {
                        this.terminate();
                    }
                    return;
                    // this.stimulis[i].show();                        
                }
            }
        }
        if (this.showing) {
            this.time = this.time + dt;
            if (this.time >= (1000 * this.showTime)) {
                this.terminate();
            }
        }
        if (this.showTime == -1 && this.showing) {
            return;
        }



    }

    render(ctx, scale = 1) {
        if (this.wait)
            return;
        var i;
        if (this.showing) {
            for (i = 0; i < this.stimulis.length; i++) {
                if (this.stimulis[i].localID == this.stimuliToShow) {
                    this.stimulis[i].render(ctx, scale);
                    if (this.stimulis[i].type == 'video' && !this.showing) {
                        this.stimulis[i].renderPreviewImage(ctx, scale);
                    }
                }

            }
        }
        else {
            for (i = 0; i < this.stimulis.length; i++) {
                if (this.stimulis[i].type == 'video') {
                    this.stimulis[i].renderPreviewImage(ctx, scale);
                }
                else
                    this.stimulis[i].render(ctx, scale);
            }
        }


    }


    renderPreview(ctx, scale = 1) {
        var i;
        for (i = 0; i < this.stimulis.length; i++) {
            if (this.stimulis[i].type == 'video') {
                this.stimulis[i].renderPreviewImage(ctx, scale);
            }
            else
                this.stimulis[i].render(ctx, scale);
        }
    }

    editPointerDown(evt) {
        this.clickEditImage();
    }

    editPointerMove(evt) {

    }
    editPointerDrag(evt) {
        if (this.imageBeingEdited != null) {
            this.imageBeingEdited.editPointerDrag();
        }
    }
}