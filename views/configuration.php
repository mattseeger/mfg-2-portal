<div class="w3-container">
    <script type="text/javascript">
    function updateModel(e){
        console.debug('Updating Model ....');
        for(i=0; i < configuration.options.length; i++){
                m_op = configuration.options[i];
                f_in = $("input[value='" + m_op.record.Id + "']")[0];
                m_op.record.SBQQ__Selected__c = f_in.checked;
        }
        document.getElementById("configurationJSON").value = JSON.stringify(configuration);
        console.debug('Model UPdated');
    }
    
    function attachUpdater(){
        $(document.configForm).find('input').change(updateModel);
    }
    
    $( document ).ready(attachUpdater);
    </script>
    <form name="configForm">
        <div class="w3-container w3-threequarter">
            <h2><?= $context->product->Name ?></h2>
            <h3><?= $context->product->SKU ?></h3>
            <p class="w3-text-grey"><?= $context->product->Description?></p>
            <?php foreach($context->configurationAttributes as $attr){
                $this->render('configurationAttribute',$attr);
            }
            ?>
            <?php foreach($context->features as $feature){ 
                $this->render('feature',$feature);
            } 
            ?>
        </div>
    </form>
    <form name="leadForm" action="/inquiry/thankYou" method="POST">
        <div class="w3-container w3-quarter">
            <div class=" w3-container w3-top" style="margin-top: 80px;">
                <h3>Get a Quote</h3>
                <table>
                    <tr>
                        <td><label for="first_name">First Name</label></td>
                        <td><input  id="first_name" maxlength="40" name="first_name" size="20" type="text" class="w3-input w3-animate-input"/></td>
                    </tr>

                    <tr>
                        <td><label for="last_name">Last Name</label></td>
                        <td><input  id="last_name" maxlength="80" name="last_name" size="20" type="text" class="w3-input w3-animate-input"/></td>
                    </tr>

                    <tr>
                        <td><label for="email">Email</label></td>
                        <td><input  id="email" maxlength="80" name="email" size="20" type="email" class="w3-input w3-animate-input"/></td>
                    </tr>

                    <tr>
                        <td><label for="phone">Phone</label></td>
                        <td><input  id="phone" maxlength="80" name="phone" size="20" type="tel" class="w3-input w3-animate-input"/></td>
                    </tr>

                    <tr>
                        <td><label for="company">Company</label></td>
                        <td><input  id="company" maxlength="40" name="company" size="20" type="text" class="w3-input w3-animate-input"/></td>
                    </tr>

                    <tr>
                        <td><label for="city">City</label></td>
                        <td><input  id="city" maxlength="40" name="city" size="20" type="text" class="w3-input w3-animate-input"/></td>
                    </tr>

                    <tr>
                        <td><label for="state">State/Province</label></td>
                        <td><input  id="state" maxlength="20" name="state" size="20" type="text" class="w3-input w3-animate-input"/></td>
                    </tr>
                </table>
                <input type="hidden" name="configurationJSON" id="configurationJSON"/>
                <input type="hidden" name="description" value="Requested Quote For <?= $context->product->Name ?>" />
                <!--<div><a class="w3-button w3-red w3-hover-black w3-hover-text-red">Calculate</a></div>-->
                <div><input type="submit" class="w3-button w3-black w3-hover-red w3-hover-text-black" value="Get a Quote" /></div>
            </div>
        </div>
    </form>
</div>
<script>
configuration = <?=$context->ConfigJSON ?>;
</script>

