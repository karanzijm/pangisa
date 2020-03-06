var APP_URL="http://localhost/pangisa/"
$(".select").selectize({"create":false,"sortField":'text',"inputClass":"form-control"})

//check price of item on item select
$(".select_item").change(function () {
    var customer_id=$(this).val();

    // $(".sales_response_div").html("Loading Please wait")

    if(customer_id==null || customer_id==''){
        alert("Please select one of the cutomers");
        return;
    }

    $.ajax(
        {
            "url":APP_URL+"getItemPrice/",
            "type":"post",
            "data":{"item_id":customer_id},
            "success":function (response) {
                $(".current_price").val(response)
            },"error":function (err) {
                $(".sales_response_div").html(err)
            }
        }
    );
})


$(".filterItemsLink").click(function (e) {
    e.preventDefault();
    var linkId=$(this).attr("categoryId");

    $(".landing_page_recomended").each(function (index,item) {
        var catAttribute=$(item).attr("categoryId");
        console.log({"LinkId":linkId, "CatAttribue":catAttribute})

        if(linkId!=catAttribute){
            $(this).toggle();
        }else{
            $(this).show();
        }
    });
})

