<?php 
require_once ROOTPATH . '/utils/checkUser.php';

checkUser(["athena"], BASE_URL);
$student_id = $data['studentId'];


$SQL = "SELECT * FROM student WHERE id='$student_id'";

$db = new DBAccess();

$res = $db->query($SQL);
$fetch = mysqli_fetch_assoc($res);
?>
<div class="row">

<div class="col-2 p-3">
<img class="img-fluid rounded img-thumbnail" width="100%" height="auto" src="<?php echo BASE_URL; ?>/data/student/<?php echo $fetch['id']; ?>/<?php echo $fetch['avatar']; ?>">
        <p class="alert alert-primary "> Estudante: <?php echo $fetch['name'];?></p>
    </div>


    <div class="col">

        <!-- resultados -->

        <div class="container mt-3">


<?php
$SQL = "SELECT * FROM session_program WHERE student_id='$student_id' AND active='1'";

$db = new DBAccess();

$res = $db->query($SQL);
?>
<p class="alert alert-info "> Programas de ensino</p>
<?php
while($fetch = mysqli_fetch_assoc($res)){
    ?>
        <p class="alert alert-primary m-3"><?php echo $fetch['name'];?>   (<?php echo $fetch['owner_id'];?> )</p>
    <?php
}
?>


<p class="alert alert-info "> Profissionais/tutores</p>
<?php 
$SQL = "SELECT * FROM student_tutorship st  INNER JOIN user u ON st.professional_id = u.username WHERE st.student_id='$student_id'";

$db = new DBAccess();

$res = $db->query($SQL);

while($fetch = mysqli_fetch_assoc($res)){
    if($fetch['role']=='tutor'){
?>

<p class="alert alert-warning m-3"><?php echo $fetch['name'];?> (<?php echo $fetch['username'];?>) <i>(tutor)</i></p>
<?php
    }
    else{
        ?>
<p class="alert alert-primary m-3"><?php echo $fetch['name'];?> (<?php echo $fetch['username'];?>)</p>
    <?php
    }
}

?>


<?php

$SQL = "SELECT * FROM sessionprogram_trial spt INNER JOIN session_program sp ON spt.session_program_id=sp.id   WHERE spt.student_id='$student_id' ORDER BY spt.last_date DESC";

$db = new DBAccess();

$res = $db->query($SQL);
?>

<p class="alert alert-info "> Sessões/aplicadores. Total: <?php echo mysqli_num_rows($res);?></p>
<?php
if(!$res){
    echo mysqli_error($db->con);
}
while($fetch = mysqli_fetch_assoc($res)){
    
?>

<p class="alert alert-primary m-3"><?php echo $fetch['name'];?> (<?php echo $fetch['professional_id'];?>)- <?php echo $fetch['last_date']; ?></p>
<?php
}

?>


</div>
    </div>
</div>