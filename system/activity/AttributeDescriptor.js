class AttributeDescriptor{

    //TODO: adicionar editor personalizado para um atributo.
    constructor(attributeName, attributeTypes, multipleValues, attributeDescription,attributeEditType,
        attributeValue=null, attributeMaxValue =null, attributeMinValue=null, destination=null, attributeValues = null, selectValues = null){
        this.attributeName = attributeName;//nome do atributo; 'images' para estimulos
        this.attributeTypes =  attributeTypes; //image, audio, text, video, stimuliID, integer, boolean, select
        this.multipleValues = multipleValues;//true ou false; 
        this.attributeDescription = attributeDescription;
        this.attributeEditType  = attributeEditType;
        this.attributeValue = attributeValue;
        this.attributeMaxValue = attributeMaxValue;
        this.attributeMinValue = attributeMinValue;
        this.destination = destination;
        this.changeType = null;
        this.attributeValues = attributeValues;
        this.selectValues = selectValues; //se o attributeTypes for 'select', essas são as opções para o combobox.
        
    }
}

