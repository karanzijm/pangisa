<style>
    .pg-order-item-slider-images-parent-div {
        height: 10%;
        max-height: 20%;
        overflow-y: hidden;
    }

    .pg-order-item-slider-images {
        height: 55% !important;
        max-height: 65% !important;
    }
</style>
<br>
<div class="container-fluid side_padding">
    <div class="row display_item_details_page">
        <div class="col-sm-4">
            <div style="padding:10px; border:1px solid #eeeeee;">
                <p class="banner-title"><?= $item->name ?></p>
                <small>TYPE <?= $item->category ?></small>

                <p>Registration Number <?= $item->identification_number?></p>
                <hr>
                <p class="price_display" style="width: 50%;"> UGX <?= number_format( $item->price )." <small>".$item->price_point."</small>" ?></p>

                <form method="post" action="<?=base_url()?>Index/orderItem/<?=$id?>/confirm" style="" class="view_categories_search_items order_item_input">
                    <input type="text" value="<?=$user['name']?>" readonly/><br>
                    <input type="text" value="<?=$user['email']?>" readonly/><br>
                    <input type="text" value="<?=$user['phone']?>" readonly/><br>
                    <input type="number" pattern="\d+" min="1" step="1" name='quantity' placeholder="quantity"
                           class="quantity" required/><br>
                    <input type="hidden" value="<?=$item->price?>" readonly class="price"/>
                    <input type="text" placeholder="Intended Place of Use" name="place_of_use" id="place_of_use" autofocus required/><br>
                    <input type="number" pattern="\d+" min="0" step="1" oninput="validity.valid||(value='');"
                           placeholder="Number Of <?= $item->price_point = 'Per Day' ? 'Days' : 'Months' ?> Needed"
                           name="number_of_days" required class="number_of_days"/><br>
                    <input type="text" readonly class="total_amount" value="Total Amount 0 UGX"/>
                    <br></R><small>Please supply when you want to pick the item</small>
                    <input type="text" readonly class=" date datepicker form-control" value="" autocomplete="off"
                           placeholder="Pick up Date" required/>

                    <textarea placeholder="Please Describe How you intend to use this item" name="usage_description"
                              required rows="3"></textarea><br><br>

                    <sction style="text-align:left; padding:7px; margin-bottom: 5px; width:100%; color: #1984ab;">
                        <input type="checkbox" name="checkbox" value="check" id="agree" onclick="checkTerms(event)"/> <a href="#" data-toggle="modal" data-target="#exampleModalLong"> I accept the Terms and Conditions </a>
                    </sction> <br><br>

                    <button class="btn btn-sm btn-success" type="submit" value="" id="submitButton"><i class="fa fa-shopping-bag"></i>  &nbsp;&nbsp;Confirm Order</button><br>

                    <div id="map"></div>

                </form>
            </div>
        </div>

        <div class="col-sm-8">
            <a>
                <div id="carouselExampleControls" class="carousel slide pg-order-item-slider-images-parent-div"
                     data-ride="carousel">

                    <ol class="carousel-indicators">
                        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                    </ol>

                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="pg-order-item-slider-images"
                                 src="<?= base_url() ?>items/<?= $item->front_view ?>" alt="Front View">
                        </div>
                        <div class="carousel-item">
                            <img class="pg-order-item-slider-images"
                                 src="<?= base_url() ?>items/<?= $item->side_view ?>" alt="side View">
                        </div>
                        <div class="carousel-item">
                            <img class="pg-order-item-slider-images"
                                 src="<?= base_url() ?>items/<?= $item->rear_view ?>" alt="Third slide">
                        </div>
                    </div>

                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div> <!-- slider-->

                <br><br>
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#ItemDescription" data-toggle="tab">Item Description</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features" data-toggle="tab">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#location" data-toggle="tab">Pick Up Location</a>
                    </li>

                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id='ItemDescription' style="padding:40px;"><?= $item->brief_description?></div>
                    <div class="tab-pane" id='features'><?= $item->features?></div>
                    <div class="tab-pane" id='location'><?= $item->pick_up_location?></div>
                </div>
        </div>

    </div>
</div>

<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">TERMS AND CONDITIONS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>The following terms and conditions must be met before an item</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $("#submitButton").hide();

    if($("#agree").prop("checked")){
        $("#submitButton").show();
    }else{
        $("#submitButton").hide();
    }

    $(".number_of_days").keyup(function () {
        var price=$(".price").val();
        var number_of_days=$(this).val();
        var quantity = $(".quantity").val();
        var total = quantity * price * number_of_days;

        $(".total_amount").val("Total Amount "+total.toLocaleString()+ ' UGX');
    });

    function checkTerms(e) {
        $("#submitButton").show();
    }
</script>


<script>
    var map, places;
    var geocoder ;
    var myLatlng = {lat: 0.3302424742036756, lng: 32.5741383433342};
    var origin=null,destination=null,counter=0;
    var distance_api, place_name='';
    var origin_name, destination_name;
    var start = document.getElementById("place_of_use");
    var autocomplete;
    var directionsDisplay;
    var directionsService;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {center: myLatlng,zoom: 15});
        places = new google.maps.places.PlacesService(map);

        var options = {
            types: ['geocode'],
            componentRestrictions: {country: 'ug'}
        };

        autocomplete = new google.maps.places.Autocomplete(start, options);
        autocomplete.addListener('place_of_use', placeOfUseChanged);

        geocoder =  new google.maps.Geocoder();

        directionsService = new google.maps.DirectionsService
        directionsDisplay = new google.maps.DirectionsRenderer;
        directionsDisplay.setMap(map);

        //when the user fills in the destination
        function placeOfUseChanged() {
            var place = autocomplete.getPlace();
            var location_address=place['geometry']['location'];
            console.log(location_address);
            destination=location_address;

            var address_name='',infowindow=null, content=null;

            resolveAddress(destination,
                function(result) {
                    address_name = result;
                    destination_name = address_name;

                    content = "<div><b>END POINT</b><br>" + destination_name + "</div>";
                    infowindow = new google.maps.InfoWindow({
                        content: content
                    });

                    placeMarkerAndPanTo(location_address, map, address_name, infowindow);
                    calculateAndDisplayRoute(directionsService,directionsDisplay);
                });


            if (place.geometry) {
                map.panTo(place.geometry.location);
                map.setZoom(15);
                // search();
            } else {
                document.getElementById('end_point').placeholder = 'Enter a city';
            }
        }


        function resolveAddress(cordinates, callback){
            var address=null;
            cordinates=JSON.stringify(cordinates)

            var _c=JSON.parse(cordinates);
            var lat=_c['lat'];
            var long=_c['lng'];

            var latlng = {lat: parseFloat(lat), lng: parseFloat(long)};

            address=geocoder.geocode({
                'location': latlng
            }, function(results, status) {
                if (status == "OK") {

                    if (results[1]) {
                        address=results[1].formatted_address;
                        var result=address?address:"ORIGIN";
                        if(callback) callback(result);
                    }
                }else{
                    window.alert('Geocoder failed due to: ' + status);
                    return null;
                }
            });
        }


    }


    $(".get_distance").click(function(){
        if(!origin || !destination){
            alert("Please you must choose at least two places to proceed or fill in the form.")
            return;
        }

        var _weight;
        if(weight==0){
            _weight=window.prompt("Please enter your weight in kilogrames.")
        }else{
            _weight=weight;
        }


        distance_api="https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins="+origin_name+"&destinations="+destination_name+"&key=AIzaSyBGesnwYfnWy-NRpA9w_rfdwCpTIDhkcD8";
        var local_url="<?=base_url()?>CityRider/get_distance_matrix"

        $.ajax(
            {
                type:'GET',
                headers:{'Access-Control-Allow-Origin': '*'},
                url:local_url,
                data:{url:distance_api},
                success:function (response) {
                    response=JSON.parse(response);

                    var resp=response['rows'];
                    var elements=resp[0]['elements']

                    var result_status=elements[0]['status']

                    if(result_status!='OK'){
                        alert("No results were found for your particular search");
                        return;
                    }

                    var distance=elements[0]['distance']
                    var duration=elements[0]['duration']


                    distance=distance['text'];
                    duration=duration['text'];

                    var actual_distance=elements[0]['distance']['value']/1000;
                    var price=3500;
                    var total_cost=actual_distance*price;
                    total_cost/=_weight;
                    total_cost=Math.round(total_cost)
                    total_cost=total_cost.toLocaleString();


                    $(".response").html("<b>Distance</b> "+distance+"<br><b>Duration</b> "+duration+"<br><b>Total cost </b>= UGX "+total_cost);
                    $(".accept").show();

                },
                error:function (err) {
                    console.log(JSON.stringify(err))
                }
            }
        );


    });

</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAc9-9hel654Hie8JwGJB69hu_WxFTawIQ&callback=initMap&libraries=places" async defer></script>
