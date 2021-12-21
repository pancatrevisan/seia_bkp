<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
if (!defined('ROOTPATH')) {
    require '../root.php';
}

require_once ROOTPATH . '/utils/checkUser.php';

checkUser(["professional","admin"], BASE_URL);


$student_id = $data['student'];


?>
<textarea id="jsonContent">
</textarea>
<script>
    document.body.onload = function(){
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                var json = JSON.parse(this.responseText);
                console.log(json);
                document.getElementById('jsonContent').value = JSON.stringify(json);
            }
        };
        var url = "<?php echo BASE_URL; ?>/dataexport/index.php?action=getStudentData_json";
        xhttp.open('POST', url, true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send("student=<?php echo $student_id;?>");
    }
    
</script>