

// **********************
// Networks
// Fetch networks worksheet, format and structure objects
//
// **********************

networks.fetch({
  success : function(){
    // console.log(networks.columnNames());
    // console.log("There are " + this.length + " networks");


    this.each(function(row){

       //Add names to networkNames array
      networkNames.push(row.name);

      var link = linkBuilder(row.website);
      var membership = iconBuilder(row.membership, 'members', 'Approx. membership');

        networkMap[row._id] = {
          name:   row.name,
          content:  '<h2>' + row.name + '</h2>' +
                    '<p class="en">' + row.descEn + '</p>' +
                    '<p class="fr">' + row.descFr + '</p>' +
                    '<p class="de">' + row.descDe + '</p>' +
                    '<p class="it">' + row.descIt + '</p>' +
                    link +
                    '<div class="icons">' + membership + '</div>'
        };

        jQuery(document).ready(function($){
          $('#networkSelect').append('<option id="' + row._id + '">' + row.name + '</option>');
        });

      });


  },
  error : function() {
    console.log("Network data has not loaded");
  }
});

// Output the whole networkMap object to the console
// console.log(networkMap);

// **********************
// Organisations
// Fetch organisations worksheet, format and structure objects
//
// **********************

organisations.fetch({
  success : function() {

    // console.log(organisations.columnNames());
    // console.log("There are " + this.length + " organisations");

    this.each(function(row){

      // Output organisation name and LatLng
      console.log(row.name);


      // Filter org size into s/m/l brackets as orgBraket

      var orgBracket = "";

      switch (row.orgSize){
        case "1":
        case "2 - 5":
          orgBracket = "S";
          break;
        case "6 - 15":
          orgBracket = "M";
          break;
        default:
          orgBracket = "L";
          break;
      }


      // Split venues into an array and return each as an icon

      var venues = "";

      if(row.venCap){
        var venString = row.venCap;
        var venArray = venString.split(" ");

        for (var i = 0; venArray.length > i ; i++) {

          venArray[i] = venArray[i].substr(1)
          venueIcon = iconBuilder(venArray[i], 'venue', 'Venue capacity');
          venues += venueIcon;

        };

      }

      // Check the memberOf column and split it into an array

      var memberOf = [];
      var memberships = "";
      var membershipArray = [];

      if(row.memberOf){
        var membershipString = row.memberOf;
        membershipArray = membershipString.split(", ");

        for (var i = 0; membershipArray.length > i ; i++) {

          // Filter out unknown networks by checking against the name column of the networkMap array
          var valid = isInArray(membershipArray[i], networkNames);

          if(valid){
            memberOf.push('<span class="networklink">' + membershipArray[i] + '</span>');
          }

        };
      }

      if(memberOf.length > 0){
        memberships =  '<p class="memberships">Member of: ' + memberOf.join(", ") + '</p>';
      }else{
        memberships = '';
      }

      var orgSize =  iconBuilder(orgBracket, 'org-size', 'Organisation size');
      var projects = iconBuilder(row.projects, 'projects', 'Annual Projects');
      var shows =    iconBuilder(row.shows, 'shows', 'Annual Performances');
      var link =     linkBuilder(row.website);

      // Populate orgMap object

      orgMap[row._id] = {
        address:  row.address,
        center:   new google.maps.LatLng(row.Lat , row.Long),
        name:     row.name,
        content:  '<h2>' + row.name + '</h2>' +
                    '<p class="en">' + row.descEn + '</p>' +
                    '<p class="fr">' + row.descFr + '</p>' +
                    '<p class="de">' + row.descDe + '</p>' +
                    '<p class="it">' + row.descIt + '</p>' +
                    '<div class="icons">' + orgSize + projects + shows + venues + '</div>' +
                    link + memberships,
        networks:   membershipArray

      }
    });
  },
  error : function() {
    console.log("Organisation data has not loaded");
  }
});


