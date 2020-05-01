<!--Form used to add a shipment to the database -->
<!-- 
<html>
<head>
	<title>Add Shipment Form</title>
	
	<script>
	 $(function(){
			$("#to").datepicker({ dateFormat: 'yy-mm-dd' });
			$("#from").datepicker({ dateFormat: 'yy-mm-dd' }).bind("change",function(){
				var minValue = $(this).val();
				minValue = $.datepicker.parseDate("yy-mm-dd", minValue);
				minValue.setDate(minValue.getDate()+1);
				$("#to").datepicker( "option", "minDate", minValue );
			})
		});
	</script>
<head>
<body> 
-->
	<?php require_once('Functions/SQLFunc.php'); ?>
	<!--include PHP file to query DB-->
	<form name='newship' method='post' action='NewShipment.php'>
		<table id='newship'>
			<tr>
				<th>New Shipment</th>
				<th></th>
			</tr>
			<tr>
				<td class="a">Client <input name='client' list='client'>
					<datalist id='client'>";
						<?php clientDropdown(); ?>
					</datalist>
					</input>
				</td>
				<td class="b">Carrier <input name='carrier' list='carrier'>
					<datalist id='carrier'>";
						<?php carrierDropdown(); ?>
					</datalist>
				</td>
			</tr>
			<tr>
				<td class="a">Ship Date <input  autocomplete="off" name='shipdate' id="from" type='text'></td>
				<td class="a">Delivery Date <input autocomplete="off" name='deliverydate' id="to" type='text'></td>
			</tr>
			<tr>
				<td class="b">Shipment Status <select name='status'>
					<option value='InTransit'>In Transit</option>
					<option value='Delivered'>Delivered</option>
					<option value='OnHold'>On Hold</option>
				</select>
				</td>
				<td>Items Shipped: <input type="textbox" name="items"></td>
			</tr>
			<tr>
				<td>Notes: <input type="textbox" name="notes"></td>
				<td class="a">Tracking Number <input name='tracknum' type='text'></td>
			</tr>
			<tr>
				<td id="mybuttona"><input type='submit' value='Add New' name='Submit'></td>
				<td id="mybuttonb"><input type='reset' value='Reset Form' name='Reset'></td>
			</tr>
		</table>
	</form>
	<p>NOTE: All fields must be filled</p>
</body>
</html>