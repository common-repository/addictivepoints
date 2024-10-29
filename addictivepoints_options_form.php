<?php
// LAYOUT FOR THE SETTINGS/OPTIONS PAGE
?>

<style>

	.div_ap_red_warning{
		padding: 0;
		margin-bottom:-15px;
		background-color: rgba(226, 119, 119, 0.24);
	}
	
	input.text_ap_red_warning{
		border-width:3px;
		border-color : rgba(226, 119, 119, 0.24);
		margin-bottom: 6px;
		padding: 0px;
	}
	
	#ap_table{
		border: dashed;
		border-width:1px;
		border-color:#73107D;
	}
	
	#ap_intro_text{
		padding-left: 10px;
		line-height: 28px;
		font-size:14px;
		color: #7B7B7B;
	}
	#large_hangapos{
		font-size:36px;
		color: #5D326A;
		font-family: fantasy;
	}
	
</style>

<div class="wrap">
    <?php screen_icon(); ?>
    <form action="options.php" method="post" id=<?php echo $this->plugin_id; ?>"_options_form" name=<?php echo $this->plugin_id; ?>"_options_form">
	
		<?php settings_fields($this->plugin_id.'_options'); ?>
		
		<h2><strong>addictive</strong>points &raquo; Options</h2>
		
		<br />
		
		<table width="600" border='0' cellpadding="4" cellspacing="4" id='ap_table'>
			<tr>
				<td colspan='2' align='center'>
				<?php
					echo '<img src="' .plugins_url( 'addictive-points-logo.png' , __FILE__ ). '" > ';
				?>
					<br />
					
					<p id='ap_intro_text' align='left'>
						<span id='large_hangapos' align='left'>&ldquo;</span>
						
							<strong>addictive</strong>points drive customer loyalty and engagement with your site
							by rewarding customers with <strong>addictive</strong>points, which are exchangeable
							for thousands of products and services on a local, regional and national level, all
							of which are fulfilled by us.
							
							<span id='large_hangapos' align='left'>&rdquo;</span>
							<br /><br />Need a key? Click
							<a id="signup" href='http://www.addictivepoints.com/partner/reward/join' target='_blank' >here</a>
							to sign up for <strong>addictive</strong>points!
							
						
					</p>
				</td>
			</tr>
		
			<tr>
				<td align="right"> <strong>Key:</strong></td>
				
				<td id="keyfield">
					
					<input type="text" id="addictivepoints_key"
						<?php if (count($this->validation_errors) > 0 ) echo "class='text_ap_red_warning'";?>
						placeholder="Enter your Addictive Points key here."
						name="<?php echo $this->plugin_id; ?>[key]"
						value="<?php echo $options['key']; ?>" size="50" />
				</td>
			</tr>
	
		<?php
			// Validation error displays within the admin pages; allows selective execution of HTML, creating new table rows.
			
			if (isset($this->validation_errors['key_length']) && !empty($this->validation_errors['key_length']) ){ ?>
				<tr>
					<td align="right">&nbsp;</td>
					<td>
						<?php echo "<div class='div_ap_red_warning'>".$this->validation_errors['key_length']."</div><br />"; ?>
					</td>
				</tr>
		<?php } ?>
			
		<?php if (isset($this->validation_errors['key_chars']) && !empty($this->validation_errors['key_chars']) ){ ?>
			<tr>
				<td align="right">&nbsp;</td>
				<td>
					<?php echo "<div class='div_ap_red_warning'>".$this->validation_errors['key_chars']."</div><br />"; ?>
				</td>
			</tr>
		<?php } ?>
	
			<tr>
				<td align="right" width='50'> <strong>Tab Position</strong></td>
				<td>
					<select name="<?php echo $this->plugin_id; ?>[var_pos]">
						<option value="topright" <?php if ($options['var_pos'] == 'topright' || empty($options['var_pos'])) echo 'selected="selected"'; ?>>&nbsp; Top Right &nbsp;</option>
						<option value="bottomright" <?php if ($options['var_pos'] == 'bottomright') echo 'selected="selected"'; ?>>&nbsp; Bottom Right &nbsp;</option>
						<option value="bottomleft" <?php if ($options['var_pos'] == 'bottomleft') echo 'selected="selected"'; ?>>&nbsp; Bottom Left &nbsp;</option>
						<option value="topleft" <?php if ($options['var_pos'] == 'topleft') echo 'selected="selected"'; ?>>&nbsp; Top Left &nbsp;</option>
					</select>
				</td>
			</tr>
	
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" name="submit" value="Save Options" class="button-primary" />
					<br /><br />
					<div>By installing <strong>addictive</strong>points you agree to the
						<a href="http://www.addictivepoints.com/partner/terms" target='_blank' >terms and conditions
						</a>
					</div>
				</td>
			</tr>
			
		</table>
	</form>
</div>


