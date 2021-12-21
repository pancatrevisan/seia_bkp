

<div class="row">
    <div class="col">
        <div class="container">
            <form method="post" autocomplete="off" action="index.php?action=newStudent">
                <div class="form-group">
                    <label for="studentName">Nome</label>
                    <input  type="text" class="form-control" id="studentName" name="studentName">
                </div>
                
                <div class="form-group">
                    <label for="birthday">Data de Nascimento</label>
                    <input type="date" class="form-control" id="birthday" name="birthday">
                </div>
                
                <div class="form-group row">
                    
                    <div class="col-sm-9">
                      <label for="sex">Sexo</label>
                    </div>
                    <div class="col-sm-3">
                      <select name="sex" class="form-control">
                          <option value="male">Masculino </option>
                          <option value="female">Feminino </option>
                      </select>
                    </div>
                </div>
                
                <div class ="form-group">
                    
                        <label for="city">Cidade</label>
                    
                </div>
                
                <div class="form-group row">
                    
                    <div class="col-sm-9">
                      <input class="form-control" id="city" name="city" >
                    </div>
                    <div class="col-sm-3">
                      <select name="state" class="form-control">
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                    </select>
                    </div>
                  </div>
               
                
                <div class="form-group">
                    <label for="medication">Uso de medicação? Qual/quais?</label>
                    <input type="text" class="form-control" id="medication" name="medication">
                </div>
                
                <button type="submit" class="btn-lg btn-block btn-primary">Cadastrar</button>
            </form>
        </div>
    </div>
</div>

