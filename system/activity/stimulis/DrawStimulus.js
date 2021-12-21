class DrawStimulus extends Stimulus{
    constructor(localID, activity, instruction, position){
        super(null,'drawStimulus', localID,true,true, activity, instruction);
        
        
        var size = [64,64];
        this.renderImage = new DFTImage('_textFrame',size ,position,instruction,this, false);         
        this.points = [];
        //a posição já é o primeiro ponto.
        this.points.push(position);
        this.color = "#000";
        this.center = null;
        this.radius = 0;
        this.trianglePoints =[];
    }
    checkIsCloseToTriangle(){
        console.log("Checando se é próximo a um triangulo...");
        var tolerance = 10;//10% de distância da reta
        var numFarAwayTolerance = 10;
        //vai definir a linha como o primeiro e ultimo ponto...
        var numFarAway = 0;
        var centroid = [0,0];
        var maiorX = this.points[0][0], maiorY = this.points[0][1], menorX = this.points[0][0], menorY = this.points[0][1];
        //https://en.wikipedia.org/wiki/Distance_from_a_point_to_a_line
        for (var i = 0; i < this.points.length; i++){
            if(this.points[i][0]> maiorX)
                maiorX = this.points[i][0];
            
            if(this.points[i][0] < menorX)
                menorX = this.points[i][0];
            
            if(this.points[i][1]> maiorY)
                maiorY = this.points[i][1];

            if(this.points[i][1] < menorY)
                menorY = this.points[i][1];
        }
        console.log(menorX, maiorX, menorY, maiorY);
        /*
                p1
            v1 /    \ v2
            p3-------p2
                 v3
         */

        var top = (maiorX-menorX)/2 + menorX;

        
        var pct = 0.05;
        var p1 = [top, menorY - (menorY*pct)];
        var p2 = [maiorX+(maiorX*pct), maiorY+(maiorY*pct) ];
        var p3 = [menorX-(menorX*pct), maiorY+maiorY*pct];
        
        this.center = [(p1[0] + p2[0]+p3[0])/3, (p1[1]+p2[1]+p3[1])/3];
        this.center = [parseInt(this.center[0]), parseInt(this.center[1])];

        var v1 = [p2[0]-p1[0], p2[1]-p1[1]];
        var v2 = [p3[0]-p2[0], p3[1]-p2[1]];
        var v3 = [p3[0]-p1[0], p3[1]-p1[1]];
/*
        var a1 = this.angleBetween(v1,v2);
        var a2 = this.angleBetween(v2,v3);
        var a3 = this.angleBetween(v3,v1);
*/
        var a = p1[0] * (p2[1]-p3[1]) +
                p2[0] * (p3[1]-p1[1]) +
                p3[0] * (p1[1]-p2[1]);

        this.trianglePoints.push(p1);
        this.trianglePoints.push(p2);
        this.trianglePoints.push(p3);

        console.log("Testando se é triângulo... " + !(a == 0) );
        console.log(this.trianglePoints);
        if(a == 0){
            console.log("Não é aqui já...");
            return false;
        }
            

        for (var i = 0; i < this.points.length; i++){
            var isInside = this.pointInTriangle (this.points[i], p1, p2, p3);
            if(!isInside)
                numFarAway++;

            
        }
        console.log("num points: " + this.points.length +" Numfaraway: " +numFarAway);
        if(numFarAway<numFarAwayTolerance)
            return true;
        else
            return false;

    }
    sign (p1, p2, p3)
    {
        return (p1[0] - p3[0]) * (p2[1] - p3[1]) - (p2[0] - p3[0]) * (p1[1] - p3[1]);
    }

    pointInTriangle (pt, v1, v2, v3)
    {
        var d1, d2, d3;
        var has_neg, has_pos;

        d1 = this.sign(pt, v1, v2);
        d2 = this.sign(pt, v2, v3);
        d3 = this.sign(pt, v3, v1);

        has_neg = (d1 < 0) || (d2 < 0) || (d3 < 0);
        has_pos = (d1 > 0) || (d2 > 0) || (d3 > 0);

        return !(has_neg && has_pos);
    }
    
    isCloseToHorizontalLine(){
        var tolerance = 30;//30% da largura.
        //vai definir a linha como o primeiro e ultimo ponto...

        var p0 = [this.points[0][0],this.points[0][1]];
        var p1 = [this.points[this.points.length-1][0],this.points[this.points.length-1][1]];

        var lineDist = [p0[0]-p1[0], p0[1]-p1[1]];
        var lineLen = Math.sqrt(lineDist[0]*lineDist[0] + lineDist[1]*lineDist[1]);

        var h = Math.abs(p1[1]-p0[1]);

        var distTolerance = (tolerance/100.0) * lineLen;
        console.log("DistsTolerance: " + distTolerance);
        console.log("Height> " +h);
        if(h>distTolerance)
            return false;
        
        return true;


    }

    checkIsCloseToLine(){
        console.log("Checando se é próximo á uma linha...");
        var tolerance = 10;//10% de distância da reta
        var numFarAwayTolerance = 10;
        //vai definir a linha como o primeiro e ultimo ponto...

        var p0 = [this.points[0][0],this.points[0][1]];
        var p1 = [this.points[this.points.length-1][0],this.points[this.points.length-1][1]];

        var lineDist = [p0[0]-p1[0], p0[1]-p1[1]];
        var lineLen = Math.sqrt(lineDist[0]*lineDist[0] + lineDist[1]*lineDist[1]);
        var numFarAway = 0;

        var centroid = [0,0];
        var maiorX = this.points[0][0], maiorY = this.points[0][1], menorX = this.points[0][0], menorY = this.points[0][1];
        //https://en.wikipedia.org/wiki/Distance_from_a_point_to_a_line
        for (var i = 0; i < this.points.length; i++){
            if(this.points[i][0]> maiorX)
                maiorX = this.points[i][0];
            
            if(this.points[i][0] < menorX)
                menorX = this.points[i][0];
            
            if(this.points[i][1]> maiorY)
                maiorY = this.points[i][1];

            if(this.points[i][1] < menorY)
                menorY = this.points[i][1];
                
            var point = this.points[i];
            var dist = Math.abs((p1[0]-p0[0]) * (p0[1]-point[1]) - (p0[0]-point[0])*(p1[1]-p0[1]))/lineLen;
            if(dist > tolerance){
                numFarAway++;
            }
        }
        centroid[0] = (p1[0]+p0[0]) / 2;//menorX + (maiorX - menorX) / 2;// centroid[0] / this.points.length;
        centroid[1] = (p1[1]+p0[1]) / 2;//menorY + (maiorY-menorY) / 2; //centroid[1] / this.points.length;

        this.center = [parseInt(centroid[0]), parseInt(centroid[1])];
        console.log("num distantes: " + numFarAway);
        if(numFarAway > numFarAwayTolerance){
            console.log("muito longe...");
            return false;
        }
        return true;
    }

    checkIsCloseToCircle(){
        var tolerance = 25;//30%
        var outTolerance = 0;
        var centroid = [0,0];

        var maiorX = this.points[0][0], maiorY = this.points[0][1], menorX = this.points[0][0], menorY = this.points[0][1];

        for (var i = 0; i < this.points.length; i++){
            if(this.points[i][0]> maiorX)
                maiorX = this.points[i][0];
            
            if(this.points[i][0] < menorX)
                menorX = this.points[i][0];
            
            if(this.points[i][1]> maiorY)
                maiorY = this.points[i][1];

            if(this.points[i][1] < menorY)
                menorY = this.points[i][1];
            //centroid[0] += this.points[i][0];
            //centroid[1] += this.points[i][1];
        }
        centroid[0] = menorX + (maiorX - menorX) / 2;// centroid[0] / this.points.length;
        centroid[1] = menorY + (maiorY-menorY) / 2; //centroid[1] / this.points.length;

        this.center = [parseInt(centroid[0]), parseInt(centroid[1])];
        
        var distances = [];
        var mean = 0;
        for (var i = 0; i < this.points.length; i++){
            //distancia...
            var d_vec = [centroid[0] - this.points[i][0], centroid[1] - this.points[i][1]];
            var dist = Math.sqrt(d_vec[0]*d_vec[0] + d_vec[1]*d_vec[1]);
            distances.push(dist);
            mean += dist;
        }
        //mean radius
        mean = mean / this.points.length;
        mean = mean * 1.2; // aumenta 20%
        this.radius = mean;
        var outOfRadius = 0;
        console.log("radius: " + this.radius+ " center> " + centroid[0]+","+centroid[1]);

        for(var i = 0; i < this.points.length; i++){
            var diff = distances[i];// Math.abs(distances[i] - mean);
            //diff = (diff*100)/mean;
            
            //if(diff>tolerance)
            if(diff>mean)
                outOfRadius++;
        }
        console.log("Numero de pontos: " + this.points.length +" pontos fora: " + outOfRadius);
       // outOfRadius = (outOfRadius * 100) / this.points.length;
        console.log("Fora: " + outOfRadius);

        if(outOfRadius > outTolerance){
            console.log("Muitos fora.");
            return false;
        }
        console.log("Quase circulo");
        return true;

    }
    
    
        
     wasPointed(){
        
        return this.renderImage.wasPointed();
    }
    
    addPoint(p){
        this.points.push(p);
    }
    exportXML(xmlDoc){
        var text_xml = xmlDoc.createElement('finishInstruction');
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
        ctx.strokeStyle = this.color;
        ctx.lineWidth = 5;
        ctx.beginPath();
       var drawPos = [this.renderImage.position[0],this.renderImage.position[1]+parseInt(this.fontSize)];
       var maxSize  = [this.renderImage.size[0],this.renderImage.size[1]];
      
       for (var i=1; i < this.points.length; i++){
           var p1 = this.points[i-1];
           var p2 = this.points[i];
           ctx.moveTo(p1[0], p1[1]);
           ctx.lineTo(p2[0], p2[1]);

       }
       ctx.stroke();
      
      
       if(1<0){
           
            ctx.strokeStyle = "#f00";
            var circle = new Path2D();
            for (var i=1; i < this.trianglePoints.length; i++){
                var p1 = this.trianglePoints[i-1];
                var p2 = this.trianglePoints[i];
                circle.moveTo(parseInt(p1[0]), parseInt(p1[1]));
                circle.lineTo(parseInt(p2[0]), parseInt(p2[1]));
            }
            ctx.stroke(circle);
       }
       
    }
    
    renderPreview(ctx, scale){
        
        ctx.font = this.fontSize + "px Georgia";
       ctx.fillStyle = this.fontColor;
       
       
       //var maxSize = [this.numberOfColumns*ctx.measureText("M").width, parseInt(this.fontSize)*this.numberOfLines];
       ctx.strokeStyle = "#D44147";
       ctx.fillStyle = "linear-gradient(transparent, transparent 28px, #91D1D3 28px)";
       var drawPos = [this.renderImage.position[0],this.renderImage.position[1]+parseInt(this.fontSize)];
       this.renderImage.render(ctx, scale);
    }
    
     pointerDown(evt) {
        //throw new Error('You have to implement the pointerDown method!');
    }
    
    pointerUp(evt) {
        this.instruction.terminate();
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





