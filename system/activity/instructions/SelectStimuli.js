class SelectStimuli extends Instruction {

    exportXML() {
        var exp = [];
        exp['stimuli'] = [];
        exp['header'] = [];
        exp['data'] = [];

        exp['header']['type'] = "SelectStimuli";
        exp['header']['position'] = this.position;
        exp['header']['next'] = this.next;
        exp['header']['description'] = this.description;

        exp['header']['editable'] = this.editable;
        exp['data']['onCorrect'] = this.onCorrect;
        exp['data']['onWrong'] = this.onWrong;

        exp['stimuli'] = this.stimulis;

        return exp;
    }
    constructor(data = { 'type': 'SelectStimuli', 'position': -1, 'editable': true, 'next': -1 }, activity) {
        super(data,activity);
        this.allowUse = true;
        this.editableAttributes.push(
            new AttributeDescriptor("images",['image'],true,"Adicionar imagem ",'add/remove',null,null,null,null,{'isContainer':false, 'dragAndAssociate':false}));

        this.description = "Apresenta estímulos e permite ao estudante selecionar um para ser utilizado em uma tela posterior";
        if (data == null)
            return;
        
        this.onCorrect = data['onCorrect'];
        this.onWrong = data['onWrong'];
        

        this.editable = true;
        this.selectedImageEditing = null;
        this.ignoreInLocalSearch.push('model');

        //for tips
        this.elapsedTime = 0;
        this.appliedTip = false;
    }
    resize(scale) {

    }
    startRunning() {
        super.startRunning();
    }
    pointerUp(evt) {


    }
    pointerDown(evt) {

        var pos = this.activity.lastPointerPos;
        var resultData = {};
        this.next = this.onWrong;
        this.activity.result = this.activity.RESULT_WRONG;
        var i;
        for (i = 0; i < this.stimulis.length; i++) {
            this.stimulis[i].pointerDown(evt);

            if (this.stimulis[i].type!="audio" && this.stimulis[i].wasPointed()) {
                resultData['type'] = "selectStimuli"; 
                resultData['selected'] = this.exportStimulus(this.stimulis[i]);
                this.done = true;
                this.activity.resultData =JSON.stringify(resultData);
            }
        }
    }
    update(dt) {
        this.elapsedTime += dt;
    }

    render(ctx, scale = 1) {
        var i;
        for (i = 0; i < this.stimulis.length; i++) {
            this.stimulis[i].render(ctx, scale);
        }
    }


    renderPreview(ctx, scale = 1) {

        this.render(ctx, scale);
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