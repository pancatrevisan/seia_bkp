

<div class="row">
    <div class="col">
        <div class="container">
            <form method="post" autocomplete="off" action="index.php?action=newFromTemplate_submit">
                <input type="text" hidden name="templateId" id="templateId" value="<?php echo $data['templateId']; ?>">
                
                <div class="form-group">
                    <label for="antecedent">Nome</label>
                    <input  type="text" class="form-control" id="name" name="name">
                </div>
                
                <div class="form-group">
                    <label for="antecedent">Antecedente</label>
                    <textarea class="form-control" id="antecedent" name="antecedent" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="behavior">Comportamento esperado</label>
                    <textarea class="form-control" id="behavior" name="behavior" rows="3"></textarea>
                </div>
                
                
                <div class="form-group">
                    <label for="consequence">Consequência</label>
                    <textarea class="form-control" id="consequence" name="consequence" rows="3"></textarea>
                </div>
                
                <?php 
                
                if(!$data['reinforcement']){
                    
                ?>
                <div class="form-group">
                    <label for="category">Categorias (separadas por vírgulas)</label>
                    <input type="text" class="form-control" id="category" name="category">
                </div>
                <?php 
                
                }
                else{
                    ?>
                    <input hidden type="text" class="form-control" id="category" name="category" value="reinforcement">
                    <?php
                }
                ?>
                <button type="submit" class="btn-lg btn-block btn-primary">Cadastrar</button>
            </form>
        </div>
    </div>
</div>