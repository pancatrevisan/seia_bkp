<!-- Modal -->
<div id="modal" class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" >
    <div id="modal-dialog " class="modal-dialog modal-xl " role="document" >
        <div class="modal-content" id="modal-content">
        <div class="modal-header">
            <h5 id="modal-title" class="modal-title">Modal title</h5>
            <button style="font-size: 36pt;" type="button" class="close text-danger" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div  class="modal-body" id="modal-body">
           
        </div>
        <div class="modal-footer">
            <button id="modal-accept" type="button" class="btn btn-primary">Confirmar</button>
            <button id="modal-cancel"type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeModal()">Cancelar</button>
        </div>
        </div>
    </div>
</div>


<script>
    
function clickBodyCloseModal(e){
    return;

    var panel = document.getElementById("modal-content");
  
  var rect = panel.getBoundingClientRect();
  
  var x = e.clientX - rect.left; //x position within the element.
  var y = e.clientY - rect.top;  //y position within the element.

  
 if(x < 0 || x > rect.width || y < 0 || y > rect.height)
    closeModal();
 e.stopPropagation();
}
function closeModal()
{
    
    var fade = document.getElementById("fade-remove-me");
    while(fade!=null){
        if(fade!=null)
        {
            fade.parentElement.removeChild(fade);
        }
        fade = document.getElementById("fade-remove-me");
    }
    
    
    
    document.body.classList.remove("modal-open");

    var mod = document.getElementById("modal");
    if(mod!=null){
        mod.style.display="none";
        mod.classList.remove("show");
        document.getElementById ("modal-body").innerHTML = ""; //removeChild(document.getElementById ("modal-body").children[0]);
    }
    
}

function swapModalContent(message, acceptFunction=null, cancelFunction=null){
    document.getElementById ("modal-body").innerHTML = "";
    if((typeof message) == "string")
        document.getElementById ("modal-body").innerHTML = message;
    else
    {
        document.getElementById ("modal-body").appendChild(message);
    }
    if(acceptFunction!=null){
        document.getElementById ("modal-accept").classList.remove('d-none');
        document.getElementById ("modal-accept").onclick = acceptFunction;
    }
    else{
        document.getElementById ("modal-accept").classList.add('d-none');
    }
    if(cancelFunction!=null)
        document.getElementById ("modal-cancel").onclick = cancelFunction;
    else
        document.getElementById ("modal-cancel").onclick = closeModal;
}

function isModalVisible(){
    var mod = document.getElementById("modal");
    if(mod.classList.contains('show'))
        return true;
    return false;
        
}



function showModal(title, message, acceptFunction=null,showConfirm=true,cancelFunction=null) 
{
    var mod = document.getElementById("modal");
    document.body.onmousedown = clickBodyCloseModal;
    mod.style.display="block";
    mod.classList.add("show");

    var doc = document.documentElement;
    var top = (window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0);
    
    
    //clean content
    document.getElementById ("modal-body").innerHTML = "";
    document.getElementById ("modal-body").offsetTop = top;
    document.getElementById("modal-title").innerHTML = title;
    if((typeof message) == "string")
        document.getElementById ("modal-body").innerHTML = message;
    else
    {
        document.getElementById ("modal-body").appendChild(message);
    }
    if(acceptFunction == null){
        document.getElementById ("modal-accept").onclick = closeModal;
    }
    else
        document.getElementById ("modal-accept").onclick = acceptFunction;
    
    
    
    document.body.classList.add("modal-open");

    var fade = document.createElement("div");
    
    fade.id = "fade-remove-me";
    fade.classList.add("modal-backdrop");
    fade.classList.add("fade");
    fade.classList.add("show");
    
    if(!showConfirm){
        document.getElementById('modal-accept').classList.add('d-none');
    }
    else{
        document.getElementById('modal-accept').classList.remove('d-none');
    }
    
     if(cancelFunction!=null)
        document.getElementById ("modal-cancel").onclick = cancelFunction;
    else
        document.getElementById ("modal-cancel").onclick = closeModal;
    if(acceptFunction == null)
        document.getElementById ("modal-cancel").classList.add('d-none');
    else
        document.getElementById ("modal-cancel").classList.remove('d-none');
    document.body.appendChild(fade);
    mod.focus();
}
</script>