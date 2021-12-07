<?php

function get_cat_children($parent_id){
	$categories = get_term_children( $parent_id, 'product_cat' ); //get all children categories of Brands into categories var
	foreach($categories as $cat){ //foreach brands child category
		$term = get_term( $cat, 'product_cat' ); //get the term
		if($term->count > 0){ //if it has more than one item 
			echo "<option value=\"{$term->name}\">{$term->name}</option>";
		}
	}	
}

function brochure_form_init(){
	$brands_parent = 49319689; //get 'brands' category id
	$salts_parent = 49319693; //get 'salts' category id 
	?>
	<div class="sticky-form"> 
		<form id="brochure-form" method="post" action="">
		<select id="category-select" onchange="this.form.submit()" name="category">
			<option value="">Select a category...</option>
			<option value="online-e-liquid-store-uk">All E-liquid</option>
			<option value="Thor Juice E-liquid">Thor Juice</option>
			<option value="Thorbacco">Thorbacco</option>
			<option value="Hela Eliquid">Hela</option>
			<option value="Loki E-liquid">Loki</option>
			<option value="Nic Shot">Nic Shots</option>
			<?php 
			get_cat_children($brands_parent);
			get_cat_children($salts_parent);
			?>
		</select>
		</form>
	</div>
<?php
}
add_shortcode('brochure_search','brochure_form_init');

function get_products_for_brochure(){
	if(!empty($_POST['category'])){ //if a category has been selected from the form
		$category = $_POST['category']; //assign it to the category variable
	}
	else{
		$category = "online-e-liquid-store-uk"; //on initial load of page, assign category to main e-liquid category
	}
	
	$args = array( //arguments for wp query
    'post_type'      	=> 'product',
    'posts_per_page' 	=> -1,
	'product_cat'		=>	$category //this is the value the user selects
);
	$loop = new WP_Query( $args ); //create a loop object based on wp query args
	?>
<?php if($category == "online-e-liquid-store-uk"){?>
	<h3 class="brochure-header">All E-Liquids</h3>
<?php } 
		else{?>
	<h3 class="brochure-header"><?php echo $category; ?></h3>
<?php } ?>

	<table class="brochure_table">
		<th>Image</th>
		<th>Name</th>
		<th>Flavour</th>
		<th>Info</th>
	<?php while ( $loop->have_posts() ) : $loop->the_post();?>
		<tr>
		<?php 
		global $product; 
		$flavour_value = array_shift( wc_get_product_terms( $product->id, 		'pa_flavours', array( 'fields' => 'names' ) ) ); //get the flavours terms and shift forward
		$size_value = array_shift( wc_get_product_terms( $product->id, 					'pa_bottle-size', array( 'fields' => 'names' ) ) ); //get bottle size terms and shift forward
		?>
		<td class="bt_img"> <?php echo $product->get_image();?></td>
		<td class="bt_name" id="<?php echo $cat ?>"> <?php echo $product->get_name();?></td>
		<td class="bt_flavour"> <?php echo $flavour_value;?></td>
		<td class="bt_desc"><button onclick="togglediv(this.id)" id="<?php echo $product->get_id()?>">More</button></td>
		</tr>
		
				<div id="<?php echo $product->get_id()?>_description_container" style="background-color:white;display:none;" class="desc_prod_container">
					<div class="bt_img"> <?php echo $product->get_image();?></div>
					<span class="bt_name"> <?php echo $product->get_name();?></span><br>
					<span class="bt_size">Bottle size:  <?php echo $size_value;?></span>
					<p><?php echo $product->get_short_description();?></p>
					<button onClick="togglediv('close')" > Close </button>
		</div>
		
	<?php endwhile;?>
	</table>

<script>
	/*This function toggles the description popup and also checks the state of other popups to close before opening a new one*/
	function togglediv(id) {
  document.querySelectorAll(".desc_prod_container").forEach(function(div) {
    if (div.id == id + "_description_container") {
      // Toggle specified DIV
      div.style.display = div.style.display == "none" ? "block" : "none";
    }
	else if (id == "close"){
		div.style.display = "none";
	}
	  else {
      // Hide other DIVs
      div.style.display = "none";
    }
  });
}
		</script>

<script>

const popups = [...document.getElementsByClassName('popup')];

window.addEventListener('click', ({ target }) => {
  const popup = target.closest('.desc_prod_container');
  const clickedOnClosedPopup = popup && !popup.classList.contains('show');
  
  popups.forEach(p => p.classList.remove('show'));
  
  if (clickedOnClosedPopup) popup.classList.add('show');  
});

</script>

	<?php wp_reset_query(); //reset the query
}

add_shortcode('thorjuice_brochure','get_products_for_brochure'); //add to shortcode!
