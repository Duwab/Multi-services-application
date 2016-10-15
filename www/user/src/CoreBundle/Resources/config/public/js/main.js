(function(){
    var subscriptionNames = {
        'normal-plan': "Normale",
        'premium-plan': "Premium"
    };
    
    $(document).ready(function(){
        var pending = false;
        var offerId;
        $('.launch-subscription').on('click', function(){
            offerId = $(this).attr('offer-id');
            $('#name-subscription').html(subscriptionNames[offerId]);
            $('#myModal').modal({
            });
        });
            
        var onFormSubmit = function(e){
            e.preventDefault();
            if(pending)
                return alert('pending');
            pending = true;
            var serialized = $('form').serializeArray();
            var options = {
                plan: offerId
            };
            for(var i = 0; i < serialized.length; i++)
            {
                var param = serialized[i]
                options[param.name] = param.value;
            }
            $.ajax({
                method: "POST",
                url: "/app_dev.php/offers/subscribe",
                data: options
            }).done(function(data){
                pending = false;
                console.log('post success', data);
                alert('customer created ' + data.cusId);
                $('#myModal').modal('hide');
            }).fail(function(error){
                pending = false;
                alert('error');
                console.log(error);
            });
        };

        $('#myModal form').submit(onFormSubmit);
        $('#save-subscription').on('click', onFormSubmit);
    });
})();