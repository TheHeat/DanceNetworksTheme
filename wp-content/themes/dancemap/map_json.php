<?php

// Query the Networks and Organisations post-types
// populate JSON arrays with the relevant information

$networks = array();
$network_selection = array();
$organisations = array();

// The Networks
$net_args = array('post_type' => 'network', 'posts_per_page'=>-1, 'orderby' => 'title', 'order' => 'ASC');
$net_query = new WP_query($net_args);

if($net_query->have_posts()):

	while($net_query->have_posts()):

		// Create a container array for network information
		$network = array();

		$net_query->the_post();

		$network['title']       = get_the_title();
		$network['content']     = '<h1>' . get_the_title() . '</h1>' . get_the_content();
		$network['membership']  = get_field('membership');
    $network['website']     = get_field('website');

		$networks[ get_the_ID() ] = $network;
		$network_selection[ get_the_ID() ] = $network['title'];

	endwhile;

endif;

// The Organisations
$org_args = array('post_type' => 'organisation','posts_per_page'=>-1, 'orderby' => 'title', 'order' => 'ASC');
$org_query = new WP_query($org_args);

if($org_query->have_posts()):

	while($org_query->have_posts()):

		// Create a container array for organisation information
		$organisation = array();

		$org_query->the_post();

		$memberships = get_field('memberOf');

		if($memberships):

			$memberOf = array();
			$org_networks = array();

			foreach ($memberships as $membership):

				$memberlink = '<span class="networklink">' . get_the_title($membership->ID) . '</span>';
				$memberOf[] = $memberlink;
				$org_networks[] = get_the_title($membership->ID);

  		endforeach;

		endif;

    $venues = array();

    if( have_rows('venCap') ):
      while ( have_rows('venCap') ) : the_row();
        $venues[] = get_sub_field('cap');
      endwhile;
    endif;

		// Location from ACF Google Maps field
		$location = get_field('LatLng');
		$organisation['lat'] 		  = $location['lat'];
		$organisation['lng'] 		  = $location['lng'];

		$organisation['title'] 		= get_the_title();
		$organisation['content'] 	= 	'<h1>' . get_the_title() . '</h1>' . '<p>' . get_the_content() . '</p>';
    $organisation['website']  = get_field('website');
		$organisation['orgSize'] 	= get_field('orgSize');
		$organisation['projects'] = get_field('projects');
		$organisation['shows'] 		= get_field('shows');
    $organisation['venues']   = $venues;
		$organisation['memberOf'] = __('Member of: ') . implode(', ', $memberOf);
		$organisation['networks']	= $org_networks;

		// Push to the $organisations object
		$organisations[ get_the_ID() ] = $organisation;

	endwhile;

endif;

?>

<script>

	var networkMap =  <?php echo json_encode( $networks ); ?>;
	var orgMap =  <?php echo json_encode( $organisations ); ?>;
	var networkSelection =  <?php echo json_encode( $network_selection ); ?>;

	console.log(networkMap);
	console.log(orgMap);

// **********************
// Empty/default arrays, objects and variables
//
// **********************

// Language option
// var language = 'en';

// networkChoice controls what information is displayed on the map and in the network-info panel
var networkChoice = "";

// **********************
// Functions: filters and constructors
//
// **********************

// // Check a value against array items
function isInArray(value, array)
{
  return array.indexOf(value) > -1 ? true : false;
}


// function to clean protocols fron URLs
function linkWash(urlString)
{
    var urlParts = urlString.replace('http://','').replace('https://','').replace('www.','');
    return urlParts;
}


// Function for constructing nicely formatted links from a website address (no http://)
// Check if there is a website specified, then costruct a link
function linkBuilder(website)
{
  if(website != null){

            var linkDomain = linkWash(website);
            var linkFormat = '<p><a href="' + website + '">' + linkDomain + '</a></p>';
            return linkFormat;
          }else{
            var linkFormat = "";
            return linkFormat;
          }
}

// Function or creating icons
// 2 inputs: the icon class and the column

function iconBuilder(col, className, hovertext){
  var icon = "";
  if(col){
    icon = '<span id="' + className + '" title="' + hovertext + '" class="icon ' + className + '">' + col + '</span>';
    return icon;
  }else{
    icon = "";
    return icon;
  }

}

var infowindow = new google.maps.InfoWindow();

function initialize() {

var styles = [
  {
    "stylers": [
      { "visibility": "simplified" },
      { "hue": "#5fafc1" },
      { "gamma": 0.3 }
    ]
  },{
    "featureType": "water",
    "stylers": [
      { "color": "#333333" },
      { "visibility": "simplified" }
    ]
  },
  {
    "featureType": "administrative.locality",
    "elementType": "labels",
    "stylers": [
      { "visibility": "on" }
    ]
  }
];

  // Enable the visual refresh
  google.maps.visualRefresh = true;

  var mapOptions = {
    center: new google.maps.LatLng(50, 15),
    disableDefaultUI: true,
    zoom: 4,
    // mapTypeId: google.maps.MapTypeId.ROADMAP
    styles: styles
  };

  var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

  // create the Bounds object
  var bounds = new google.maps.LatLngBounds();

	for (var i in orgMap) {

		var filter = isInArray(networkChoice, orgMap[i].networks);

	    if(!networkChoice || filter){

		  	var lat = orgMap[i].lat;
		  	var lng = orgMap[i].lng;
		  	var center = new google.maps.LatLng(lat, lng);


        // Organisation Content
               
        var orgSize   = iconBuilder(orgMap[i].orgSize, 'org-size', 'Organisation size');
        var projects  = iconBuilder(orgMap[i].projects, 'projects', 'Annual Projects');
        var shows     = iconBuilder(orgMap[i].shows, 'shows', 'Annual Performances');
        var link      = linkBuilder(orgMap[i].website);

        var title     = orgMap[i].title;
		  	var body      = orgMap[i].content + 
                        '<p>' + link + '</p>' + 
                        '<p>' + orgMap[i].memberOf + '</p>' +
                        '<div class="icons">' + orgSize + projects + shows + '</div>';


		  	createMarker(center, title, body);

		    // extend the bounds to include this marker's position
		    bounds.extend(center);
		    // resize the map
		    map.fitBounds(bounds);
		}
	}

  function createMarker(center, title, body) {

    var marker = new google.maps.Marker({
      position: center,
      title: title,
      map: map
      // animation: google.maps.Animation.DROP
    });

    google.maps.event.addListener(marker, 'click', function () {
      infowindow.setContent(body);
      infowindow.open(map, marker);
    });

  }

}

google.maps.event.addDomListener(window, 'load', initialize);


// *******************************************
// jQuery Magic
//
// *******************************************


jQuery(document).ready(function($){


  // ***************************
  // NETWORK CHOICES
  // Selecting a network either from the select menu or by clicking in an infoWindow fires a number of actions
  // 1. Loads the network info into the .network-info div
  // 2. Slides up the .project-info div if it is visible
  // 3. Slides down the .network-info div if it isn't visible
  // 4. Enables the ability to toggle project and network info

  // Update networkChoice
  // Loop through each object in networkMap looking for a match. If a match is found, ie it's not the title option:
  // update var networkChoice, toggle info divs, load the content into the .network-info div and add class .link to the title
  // initialize the Google map

  function updateNetworkChoice(){

    for(i in networkMap){

      if(networkMap[i].title === networkChoice){

        console.log("networkChoice = " + networkChoice);

        var networkMembers = '<div class="icons">' + iconBuilder(networkMap[i].membership, 'members', 'Approx. membership') + '</div>';
        var networkWebsite = linkBuilder(networkMap[i].website);


        var networkBlurb = networkMap[i].content + networkMembers + networkWebsite;

        $(".project-info").slideUp();

        $(".network-info").slideUp(function(){

          $(this).html(networkBlurb);
          $(this).slideDown();
          $(".content h1").addClass("link");

          initialize();

        });

      }
    }

  }

  // Clear the networkChoice
  // clear the var networkChoice, remove content from the network-info div, toggle info divs visibility and remove class .link from the title
  // initialize the Google mape

  function clearNetworkChoice(){
    networkChoice = "";
    console.log("networkChoice has been cleared")
    $('.network-info').html("");
    $('.network-info').slideUp();
    $(".project-info").slideDown();
    $(".content h1").removeClass("link");
    initialize();
  }

  // When #networkSelect changes, update var networkChoice with the name selected

  $("#networkSelect").change(function(){
    $("#networkSelect option:selected").each(function(){

      var choice = $(this).text();

      if (choice === "Show All"){
        clearNetworkChoice();
      }else{
        networkChoice = choice;
        updateNetworkChoice();
      };



    });
  });

  // when a .networklink is clicked, update var networkChoice with the name selected
  // this has to exist in a google.maps listener: the infoWindow doesn't exist in the DOM until it opens

  google.maps.event.addListener(infowindow, "domready", function(){

    $(".networklink").click(function(){
      networkChoice = $(this).text();
      updateNetworkChoice();
      $("#networkSelect").val(networkChoice);
    });

  });

  // // If networkChoice is defined, clicking the title toggles the project and network info divs

  $(".content h1").click(function(){

    if(networkChoice){
        $(".project-info , .network-info").slideToggle();

    }

  });


  // END NETWORK CHOICES

});



</script>