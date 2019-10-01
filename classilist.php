		<tr>
           
           <td class="mobilehide480" valign="top" align="left" width="<?php echo $OptionsVis["summ_img_width"];?>" style="padding-right:12px;<?php if(ReadDB($Ads['highlight'])=='true'){?>background:<?php echo $OptionsVis["hl_bgr_color"];?>; padding-left:4px;;<?php } else {?> padding-left:0;<?php }?>">
           	<a style="border:0; text-decoration:none" href="<?php echo $thisPage; ?>?id=<?php echo $Ads['id']; ?>&cat_id=<?php echo $_REQUEST["cat_id"]; ?>&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST["p"]; ?>&search=<?php if(isset($_REQUEST["search"])) echo urlencode($_REQUEST["search"]); ?>#onads">
          	<?php if(ReadDB($Ads["image1"])!='' and $OptionsVis["summ_show_image"]=='yes') { ?>  		
            	<div><img src="<?php echo $CONFIG["full_url"].$CONFIG["upload_thumbs"].ReadDB($Ads["image1"]); ?>" alt="<?php echo ReadDB($Ads["title"]); ?>" width="<?php echo $OptionsVis["summ_img_width"];?>" /></div>
            <?php } else { ?>       
            	<div><img src="<?php echo $CONFIG["full_url"];?>images/no_image.png" alt="<?php echo ReadHTML($Ads["title"]); ?>" width="<?php echo $OptionsVis["summ_img_width"];?>" /></div>
            <?php } ?> 
            </a>
           </td>          
           
           <td valign="top"<?php if(ReadDB($Ads['highlight'])=='true'){?> style="background:<?php echo $OptionsVis["hl_bgr_color"];?>;"<?php }?>> 
        
  			<div style="text-align:<?php echo $OptionsVis["summ_title_text_align"];?>; padding-right:8px;">
           	 <?php if(ReadDB($Ads["image1"])!='' and $OptionsVis["summ_show_image"]=='yes') { ?>  		
            	<div class="mobileshow480"><img src="<?php echo $CONFIG["full_url"].$CONFIG["upload_thumbs"].ReadDB($Ads["image1"]); ?>" alt="<?php echo ReadDB($Ads["title"]); ?>" width="<?php echo $OptionsVis["summ_img_width"];?>" /></div>             
             <?php } ?> 
             
             <a style="color:<?php echo $OptionsVis["summ_title_color"];?>;font-family:<?php echo $OptionsVis["summ_title_font"];?>;font-size:<?php echo $OptionsVis["summ_title_size"];?>;font-weight:<?php echo $OptionsVis["summ_title_font_weight"];?>;font-style:<?php echo $OptionsVis["summ_title_font_style"];?>;text-decoration:none" onmouseover="this.style.textDecoration = 'underline'" onmouseout="this.style.textDecoration = 'none'" href="<?php echo $thisPage; ?>?id=<?php echo $Ads['id']; ?>&cat_id=<?php echo $_REQUEST["cat_id"]; ?>&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST["p"]; ?>&search=<?php if(isset($_REQUEST["search"])) echo urlencode($_REQUEST["search"]); ?>#onads">
				<?php echo ReadHTML($Ads["title"]); ?>
             </a>
            </div>
            
            <div style="clear:both; padding-bottom:<?php echo $OptionsVis["summ_dist_title_text"];?>;"></div> 
             
             
            <div style="color:<?php echo $OptionsVis["summ_price_color"];?>;font-family:<?php echo $OptionsVis["summ_price_font"];?>;font-size:<?php echo $OptionsVis["summ_price_size"];?>;font-weight:<?php echo $OptionsVis["summ_price_font_weight"];?>;font-style:<?php echo $OptionsVis["summ_price_font_style"];?>;text-align:<?php echo $OptionsVis["summ_price_text_align"];?>;">
            
            	<span style="text-align:<?php echo $OptionsVis["summ_price_text_align"];?>; color:<?php echo $OptionsVis["column_color"]; ?>; font-family:<?php echo $OptionsVis["title_font"]; ?>; font-size:<?php echo $OptionsVis["column_size"]; ?>; font-weight:<?php echo $OptionsVis["column_font_weight"]; ?>; font-style:<?php echo $OptionsVis["column_font_style"]; ?>;"><?php echo $OptionsLang["Price"]; ?></span>
            	<?php echo CurrFormat($Options["currency"], $CurrSign[$Options["currency"]], ReadHTML($Ads["price"])); ?>
                
                <?php if(strlen(trim($Ads["sale_price"]))>0) {?>
                &nbsp;&nbsp;
                <span style="text-align:<?php echo $OptionsVis["summ_price_text_align"];?>; color:<?php echo $OptionsVis["column_color"]; ?>; font-family:<?php echo $OptionsVis["title_font"]; ?>; font-size:<?php echo $OptionsVis["column_size"]; ?>; font-weight:<?php echo $OptionsVis["column_font_weight"]; ?>; font-style:<?php echo $OptionsVis["column_font_style"]; ?>;"><?php echo $OptionsLang["Listed"]; ?></span>
                <span style="color:<?php echo $OptionsVis["summ_date_color"];?>;font-family:<?php echo $OptionsVis["summ_date_font"];?>;font-size:<?php echo $OptionsVis["summ_date_size"];?>;font-weight:<?php echo $OptionsVis["summ_date_font_weight"];?>;font-style:<?php echo $OptionsVis["summ_date_font_style"];?>;text-align:<?php echo $OptionsVis["summ_date_text_align"];?>;">
				<?php echo CurrFormat($Options["currency"], $CurrSign[$Options["currency"]], ReadHTML($Ads["sale_price"])); ?>
            	</span>
                <?php } ?>
            </div>
        	
            <div style="clear:both; padding-bottom:<?php echo $OptionsVis["summ_dist_title_text"];?>;"></div>           
        
			<div style="color:<?php echo $OptionsVis["summ_color"];?>; font-family:<?php echo $OptionsVis["summ_font"];?>; font-size:<?php echo $OptionsVis["summ_size"];?>;font-style: <?php echo $OptionsVis["summ_font_style"];?>;text-align:<?php echo $OptionsVis["summ_text_align"];?>;line-height:<?php echo $OptionsVis["summ_line_height"];?>;padding-right:8px;">
        	
			 <?php echo cutText(ReadHTML($Ads["description"]), $Options["char_num"]); ?>
             <a style="color:<?php echo $OptionsVis["summ_title_color"];?>; text-decoration: underline" href="<?php echo $thisPage; ?>?id=<?php echo $Ads['id']; ?>&cat_id=<?php echo $_REQUEST["cat_id"]; ?>&p=<?php if(isset($_REQUEST["p"])) echo $_REQUEST["p"]; ?>&search=<?php if(isset($_REQUEST["search"])) echo urlencode($_REQUEST["search"]); ?>#onads"><?php echo $OptionsLang['Read_more']; ?></a>
        	</div>
            
          </td>
          
          <!-- <td valign="middle"<?php if(ReadDB($Ads['highlight'])=='true'){?> style="background:<?php echo $OptionsVis["hl_bgr_color"];?>;"<?php }?> width="13%">
		  	<div style="color:<?php echo $OptionsVis["summ_price_color"];?>;font-family:<?php echo $OptionsVis["summ_price_font"];?>;font-size:<?php echo $OptionsVis["summ_price_size"];?>;font-weight:<?php echo $OptionsVis["summ_price_font_weight"];?>;font-style:<?php echo $OptionsVis["summ_price_font_style"];?>;text-align:<?php echo $OptionsVis["summ_price_text_align"];?>;">
				<?php echo CurrFormat($Options["currency"], $CurrSign[$Options["currency"]], ReadHTML($Ads["price"])); ?>
          	</div>
          </td>
          
          <td class="mobilehide480" valign="middle" style="padding-right:0; <?php if(ReadDB($Ads['highlight'])=='true'){?>background:<?php echo $OptionsVis["hl_bgr_color"];?>;<?php }?>" width="13%">
          	<div style="color:<?php echo $OptionsVis["summ_date_color"];?>;font-family:<?php echo $OptionsVis["summ_date_font"];?>;font-size:<?php echo $OptionsVis["summ_date_size"];?>;font-weight:<?php echo $OptionsVis["summ_date_font_weight"];?>;font-style:<?php echo $OptionsVis["summ_date_font_style"];?>;text-align:<?php echo $OptionsVis["summ_date_text_align"];?>;">
				<?php echo CurrFormat($Options["currency"], $CurrSign[$Options["currency"]], ReadHTML($Ads["sale_price"])); ?>
            </div>
          </td> -->
          
         </tr>
         
         <tr><td colspan="4" height="<?php echo $OptionsVis["dist_btw_entries"];?>"></td></tr>