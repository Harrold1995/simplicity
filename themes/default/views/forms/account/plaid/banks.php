
<div class="modal fade newCharge-modal <?php echo $edit ?>" id="chooseBank" tabindex="-1" role="dialog"  type="chooseBank" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md " role="document" >
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px; height:600px; width: 600px;">
                <form style= "top:40px; position: initial; bottom:0; right:0; box-shadow: initial; width: 550px; height: 100%;"
                        id="chooseBank" action="<?php echo $target; ?>" method="post" class="form-fixed chargeForm" type="chooseBank"
                        formType=""><!-- formType used for js reload-->
        
                    <header class="modal-h">
                        <h2 class="text-uppercase" style ="margin:0px; "><?php echo $title; ?></h2>
                        <nav>
                            <ul>
                                <li><span class="buttons" style=""><span class="min" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                                <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                                <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                            </ul>
                        </nav>
                            
                    </header>
                    <section class="plain modal-body" style="border-style: none; box-shadow:none; margin-top: 65px; height: 90%;">
                        <h2 id = "container-header">Choose your bank or <a href="#"  id ="linkButton"  onclick = "linkHandler.open();">Add a new bank</a></h2> 
                            <div style = "max-height:80%; overflow-y: auto; overflow-x: hidden;">
                                <div id = "container" class ="row" >
                                <?php 
                                    foreach ($banks as $bank){  $test = $bank->custom->institution->primary_color; ?>
                                        <div style = "background-color:<?= $bank->custom->institution->primary_color ?>" class="col-4 bankcard" id ="bankcard" onclick="getaccounts(event.target,'<?= $bank->ins_id ?>');">
                                        <img src=" <?= $bank->custom->institution->logo != null?'data:image/jpeg;base64,'.$bank->custom->institution->logo:'uploads/images/Bank.png' ?>" alt="Placeholder" style="width:158px; height:158px; padding:10px"> 
                                        <p style = "color:white; font-size: 2em; text-align: center"><?= $bank->bank_name ?></p>                            
                                        </div>
                                        
                                <?php } ?>
                                </div>
                            </div>
                    </section>

            
                    <footer>
                    
                    </footer>
                </form>   
            </div>
		</div>
     </div>
</div>


<script type="text/javascript">
    
    var ins_id;

    var user1;
    var link_token;

    

    function getaccounts(target, ins_id2){

        that = target;
        console.log(that);
        ins_id = ins_id2;


        $.post(JS.baseUrl + 'accounts/get_bank_accts_list', {ins_id: ins_id},function(data, status){
            if(data == 'There are no more accounts to link to from this bank'){alert(data); return}
            var accts = jQuery.parseJSON(data);
            
            var container = $(that).closest('.modal').find('#container')[0];
            $(container).empty();
            $('#container-header').empty();
            $(that).width(70); 
            $(that).height(70);

            if (accts.error){
                $('#container-header').append('You need to reset your credentials');
   
                //user1 = accts.user;
                link_token = accts.link_token;
                console.log(link_token);
 
                var linkUpdateHandler  = Plaid.create({
                    token: link_token,
                    onSuccess: (<?php echo $plaid_public ?>, metadata) => {
                        console.log("update link");
                        // You do not need to repeat the /item/public_token/exchange
                        // process when a user uses Link in update mode.
                        // The Item's access_token has not changed.
                    },
                    onExit: (err, metadata) => {
                        // The user exited the Link flow.
                        if (err != null) {
                        // The user encountered a Plaid API error prior
                        // to exiting.
                        }
                        // metadata contains the most recent API request ID and the
                        // Link session ID. Storing this information is helpful
                        // for support.
                    },
                    });  

               

                linkUpdateHandler.open();

            } else {
                $('#container-header').append(that);
                $('#container-header').append('choose your account');
               
                $(container).append('<ul style ="list-style: none; width:100%"></ul>');
                container = $(container).find('ul')[0];
                $.each(accts, function(key,acct) {
                    format = acct.bank_id == null ? `onclick="saveAccount(event,'`+acct.plaid_id+`');"` : '';
                    conclass = acct.bank_id !== null ? 'connected' :'';
                    connected = acct.bank_id !== null ?`<span style='float: right;'>already connected to ${acct.name}<span>`: '';
                    $(container).append('<li class ="bankAcct '+conclass+'" '+format+' ><div>'+acct.info.name+' ('+acct.info.subtype +') '+connected+'</div> <div> <h6>*******'+acct.info.mask+' $'+number_format(acct.info.balances.current, 2)+`</h6></div></li>`);
                    
               }); 
               $(container).append("<span>Need to add another account? <a>sign in here</a></span>");
               
            }

            


            


           
        });
        
        


    }


    function saveAccount(event, plaid_id){
        that = event.target;
            $.post(JS.baseUrl + 'accounts/save_plaid_acct', {'plaid_acct': plaid_id, 'ins_id': ins_id, 'bank_id': <?=  ($bank_id != "") ? $bank_id : '0' ?>, acct_id: <?php echo $acct_id ?> },function(data, status){
              alert(status);
              $(that).closest('.modal').remove();
              account_id = $($(".right-side").find('#checkButton')[0]).attr('data-account-id');
              JS.loadRight($(".right-side"), `layout/getRightColumn?type=account&id=${account_id}` );
              JS.openModalsObjectRemove('chooseBank',  $(that).closest('.modal').attr('openModal-id'));
      
        });
    }

    var linkHandler = Plaid.create({
        //selectAccount: true,
        env: '<?php echo $plaid_env ?>',
        apiVersion: 'v2',
        clientName: 'Simpli-city',
        key: '<?php echo $plaid_public ?>',
        product: ['auth', 'transactions', 'identity'],
        webhook: 'https://myurl.com/webhooks/p_responses.php',
        onEvent: function (event, metadata)  {
        // send event and metadata to self-hosted analytics
        //analytics.send(eventName, metadata);
        //console.log('selected');
        //console.log(event);
        },
        onLoad: function() {
        // The Link module finished loading.
        },
        onSuccess: function(public_token, metadata) {
        $('#container').prepend('<div class="lds-roller" style="padding-top: 50px;  overflow:hidden;  padding-left: 200px; min-width:400px"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>');
        // The onSuccess function is called when the user has successfully
        // authenticated and selected an account to use. 
        console.log(metadata);   

        $.post( 'accounts/process_plaid_token', {pt:public_token,md:metadata,id:"<?php echo $account_id?>", bank:"<?php echo $bank_id?>"}, function( data ) {                        
            var data = JSON.parse(data);
            if (data.accounts){ 
                newbank = document.createElement("div");
                $(newbank).classname = 'col-4 bankcard';
                $(newbank).css("background-color",`${data.ins_info.primary_color}`);
                ins_id2 = data.ins_info.institution_id;
                logo = data.ins_info.logo;
                $(newbank).append(`<img src=" data:image/jpeg;base64,${logo}" style="width:158px; height:158px; padding:10px">`);
                $('#container').find('.lds-roller').remove().prepend(newbank);
                getaccounts(newbank, ins_id2);            
/*                 console.log(data);
                $(that).closest('.modal').remove();
                alert("Your account has been successfully Linked");//Let users know the process was successful 
                account_id = $($(".right-side").find('#checkButton')[0]).attr('data-account-id');
                JS.loadRight($(".right-side"), `layout/getRightColumn?type=account&id=${account_id}` );
                //$('#chooseBank').remove();
                JS.openModalsObjectRemove('chooseBank',  $('#chooseBank').attr('openModal-id')); */
            }else if (data=="duplicate"){
                console.log(data);
                alert("duplicate");//Let users know they already have a login
            }else{
                console.log(data);
                alert("error");//Let users know the process failed
            }
            });    
        },
        onExit: function(err, metadata) {
        // The user exited the Link flow. This is not an Error, so much as a user-directed exit   
        if (err != null) {
            console.log(err);
            console.log(metadata);        
        }
        },
    }); 


        

</script>

    