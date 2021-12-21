class VideoStimulus extends Stimulus{

    constructor(localID, activity, text, position, size){
        super(null,'video', localID, activity);
        
        
    }
    exportXML(xmlDoc){
        throw new Error('You have to implement the exportXML method!');
    }
    render(ctx, scale=1){
       
    }
    
    renderPreview(ctx, scale){
        throw new Error('You have to implement the renderPreview method!');
    }
    
    pointerDown(evt) {
        throw new Error('You have to implement the pointerDown method!');
    }
    
    pointerUp(evt) {
        throw new Error('You have to implement the pointerUp method!');
    }
    pointerMove(evt) {
        throw new Error('You have to implement the pointerMove method!');
    }
    pointerDrag(evt){
        throw new Error('You have to implement the pointerDrag method!');
    }
    
    editPointerUp(evt){
        throw new Error('You have to implement the editPointerUp method!');
    }
    
    editPointerDown(evt){
        throw new Error('You have to implement the editMouseDown method!');
    }
    
    editPointerMove(evt){
        throw new Error('You have to implement the editMouseDrag method!');
    }
    editPointerDrag(evt){
        throw new Error('You have to implement the editPointerDrag method!');
    }
    
}





