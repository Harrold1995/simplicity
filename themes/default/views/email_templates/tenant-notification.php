        <?php 
            $company_name = $this->site->settings->company_name;
            $company_phone = $this->site->settings->company_phone;
            $company_email = $this->site->settings->company_email;
            $company_logo = $this->site->settings->company_logo;
        ?>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=' . strtolower(config_item('charset')) . '" />
            <title><?php html_escape($subject) ?></title>
            <style type="text/css">
            
            html { -webkit-text-size-adjust: none; -ms-text-size-adjust: none;}

                @media only screen and (min-device-width: 750px) {
                    .table750 {width: 750px !important;}
                }
                @media only screen and (max-device-width: 750px), only screen and (max-width: 750px){
                  table[class="table750"] {width: 100% !important;}
                  .mob_b {width: 93% !important; max-width: 93% !important; min-width: 93% !important;}
                  .mob_b1 {width: 100% !important; max-width: 100% !important; min-width: 100% !important;}
                  .mob_left {text-align: left !important;}
                  .mob_center {text-align: center !important;}
                  .mob_soc {width: 50% !important; max-width: 50% !important; min-width: 50% !important;}
                  .mob_menu {width: 50% !important; max-width: 50% !important; min-width: 50% !important; box-shadow: inset -1px -1px 0 0 rgba(255, 255, 255, 0.2); }
                  .mob_btn {width: 100% !important; max-width: 100% !important; min-width: 100% !important;}
                  .mob_pad {width: 15px !important; max-width: 15px !important; min-width: 15px !important;}
                  .top_pad {height: 15px !important; max-height: 15px !important; min-height: 15px !important;}
                  .top_pad2 {height: 50px !important; max-height: 50px !important; min-height: 50px !important;}
                  .mob_title1 {font-size: 18px !important; line-height: 40px !important;}
                  .mob_title2 {font-size: 26px !important; line-height: 33px !important;}
                  .mob_txt {font-size: 20px !important; line-height: 25px !important;}
                }
               @media only screen and (max-device-width: 550px), only screen and (max-width: 550px){
                  .mod_div {display: block !important;}
               }
                .table750 {width: 750px;}
            </style>
            </head>
            <body style="margin: 0; padding: 0;">

            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background: #f5f8fa; min-width: 340px; font-size: 1px; line-height: normal;">
                <tr>
                <td align="center" valign="top">            
                    <!--[if (gte mso 9)|(IE)]>
                     <table border="0" cellspacing="0" cellpadding="0">
                     <tr><td align="left" valign="top" width="750"><![endif]-->
                    <table cellpadding="0" cellspacing="0" border="0" width="750" class="table750" style="width: 100%; max-width: 750px; min-width: 340px; background: #f5f8fa;">
                        <tr>
                           <td class="mob_pad" width="25" style="width: 25px; max-width: 25px; min-width: 25px;">&nbsp;</td>
                            <td align="center" valign="top" style="background: #ffffff;">

                              <table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100% !important; min-width: 100%; max-width: 100%; background: #f5f8fa;">
                                 <tr>
                                    <td align="right" valign="top">
                                       <div class="top_pad" style="height: 25px; line-height: 25px; font-size: 23px;">&nbsp;</div>
                                    </td>
                                 </tr>
                              </table>

                              <table cellpadding="0" cellspacing="0" border="0" width="88%" style="width: 88% !important; min-width: 88%; max-width: 88%;">
                                 <tr>
                                    <td class="mob_left" align="center" valign="top">
                                       <div style="height: 40px; line-height: 40px; font-size: 38px;">&nbsp;</div>
                                       <a href="#" target="_blank" style="display: block; max-width: 128px;">
                                       
                                          <img src="<?php echo base_url() ?>uploads/images/<?php echo $company_logo ?>" alt="img" width="128" border="0" style="display: block; width: 128px;" />
                                       </a>
                                       <div class="top_pad2" style="height: 78px; line-height: 78px; font-size: 76px;">&nbsp;</div>
                                    </td>
                                 </tr>
                              </table>

                              <table cellpadding="0" cellspacing="0" border="0" width="88%" style="width: 88% !important; min-width: 88%; max-width: 88%;">
                                 <tr>
                                    <td class="mob_left" align="left" valign="top">
                                       <font class="mob_title1" face="\'Source Sans Pro\', sans-serif" color="#1a1a1a" style="font-size: 15px; line-height: 55px; font-weight: 300; ">
                                          <span class="mob_title1" style="font-family: \'Source Sans Pro\', Arial, Tahoma, Geneva, sans-serif; color: #1a1a1a; font-size: 22px; line-height: 32px; font-weight: 300;">Hello <?php echo $tenant->first_name ?>
                                          <br>

                                               <?php echo $message ?></span>
                                       </font>
                                       <div style="height: 25px; line-height: 25px; font-size: 23px;">&nbsp;</div>
                                       <font class="mob_title2" face="\'Source Sans Pro\', sans-serif" color="#5e5e5e" style="font-size: 18px; line-height: 45px; font-weight: 300; ">
                                          <span class="mob_title2" style="font-family: \'Source Sans Pro\', Arial, Tahoma, Geneva, sans-serif; color: #5e5e5e; font-size: 18px; line-height: 35px; font-weight: 300;">
                                            Thank you for choosing <?php echo $company_name ?>. <br><br>

                                            <?php echo $company_name ?> <br><br>

                                            <?php echo $company_phone ?>.</span>
                                       </font>
                                       <div style="height: 38px; line-height: 38px; font-size: 18px;">&nbsp;</div>
                                       <table class="mob_btn" cellpadding="0" cellspacing="0" border="0" width="250" style="width: 250px !important; max-width: 250px; min-width: 250px; background: #27cbcc; border-radius: 4px;">
                                          <tr>
                                          </tr>
                                       </table>
                                       <div class="top_pad2" style="height: 78px; line-height: 78px; font-size: 76px;">&nbsp;</div>
                                    </td>
                                 </tr>
                              </table>
                              <table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100% !important;min-width: 100%;max-width: 100%;background-color: #302b4d;color: white;">
                              <tbody><tr>
                                 <td class="mob_left" align="left" valign="top" style="text-align: right; padding: 15px;">
                                    
                                    
                                 <a href="https://simpli-city.com/"style="text-decoration: none;">
                                    <font class="mob_title2" face="\'Source Sans Pro\', sans-serif" color="#5e5e5e" style="font-size: 16px;line-height: 45px;font-weight: 300;color: white;">
                                       <span class="mob_title2" style="font-family: \'Source Sans Pro\', Arial, Tahoma, Geneva, sans-serif; color: #5e5e5e; font-size: 16px; line-height: 35px; font-weight: 300;;;;;color: #d61156;;;;;">
                                         Sent with   </span>
                                    </font>
                                   <img style="width: 70px;" src="<?php echo base_url(); ?>themes/default/assets/images/logo-dark.png"></a>
                                    
                                    
                                    
                                 </td>
                              </tr>
                           </tbody></table>

                              

                           </td>
                           <td class="mob_pad" width="25" style="width: 25px; max-width: 25px; min-width: 25px;">&nbsp;</td>
                        </tr>
                     </table>
                     
                     <!--[if (gte mso 9)|(IE)]>
                     </td></tr>
                     </table><![endif]-->
                  </td>
               </tr>
            </table>
            </body>
            </html>